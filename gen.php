<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require_once "config.php";
require_once "funcs.php";

if (!file_exists($adipath) || !file_exists($image["path"])) {
    echo "NESSESARY FILES MISSING";
    header("HTTP/1.0 404 Not Found");
    exit();
}

if (!isset($_GET["c"]) || strlen($_GET["c"]) == 0 || !isset($_GET["i"]) || strlen($_GET["i"]) == 0) {
    header("HTTP/1.0 404 Not Found");
    exit();
}


// Get nessesary data, throw 204 when no data found
$array = createarray($adipath);
$ispot = 0;
$data;
for ($i = 0; $i < count($array); $i++) {
    if (isset($array[$i]["call"]) && strtolower($array[$i]["call"]) == strtolower($_GET["c"])) {
        if ($ispot == intval($_GET["i"])) {
            $data = $array[$i];
            break;
        } else {
            $ispot = $ispot + 1;
        }
    }
}
unset($array);
unset($ispot);
if (!isset($data)) {
    echo "NO CONTENT";
    header("HTTP/1.1 204 NO CONTENT");
    exit();
}

// Set headers
header('Content-Type: image/jpeg');
if ($image["forcedownload"]) {
    header('Content-Disposition: attachment; filename="card_' . $_GET["c"] . '_' . $data["qso_date"] . $data["time_on"] . '.jpg"');
}

// Prepare frame
$frameimage = imagecreatetruecolor($frame["length"], $frame["fontsize"] * 2 + $frame["padding"] * 2 + $frame["fontsize"] + 9);

imagesetthickness($frameimage, 3);
$foregroundcolor = imagehexcoloralloacate(
    $frameimage,
    $frame["foreground"]
);
$backgroundcolor = imagehexcoloralloacate(
    $frameimage,
    $frame["background"]
);

$sizeunit = 0;
foreach ($frame["fields"] as $field) {
    $sizeunit += $field["size"];
}
$sizeunit = ($frame["length"] - 2 * $frame["padding"] - 3 * (count($frame["fields"]) + 1)) / $sizeunit;

/// Draw initial frame
imagefilledrectangle(
    $frameimage,
    0,
    0,
    $frame["length"],
    $frame["fontsize"] * 2 + $frame["padding"] * 2 + $frame["fontsize"] + 9,
    $backgroundcolor
);
imagerectangle(
    $frameimage,
    $frame["padding"],
    $frame["padding"],
    $frame["length"] - $frame["padding"],
    $frame["fontsize"] * 2 + $frame["padding"] + $frame["fontsize"] + 9,
    $foregroundcolor
);
imageline(
    $frameimage,
    $frame["padding"],
    $frame["padding"] + 3 + $frame["fontsize"] + $frame["fontsize"] / 2,
    $frame["length"] - $frame["padding"],
    $frame["padding"] + 3 + $frame["fontsize"] + $frame["fontsize"] / 2,
    $foregroundcolor
);

/// Fill frame with data
$cellstep = $frame["padding"] + 3;
for ($i = 0; $i < count($frame["fields"]); $i++) {

    $titlefontreduction = 0;
    $titleleftposcorrection = 0;
    $titlerightposcorrection = 0;
    $titletopposcorrection = 0;
    $titlebottomposcorrection = 0;
    $valuefontreduction = 0;
    $valueleftposcorrection = 0;
    $valuerightposcorrection = 0;
    $valuetopposcorrection = 0;
    $valuebottomposcorrection = 0;

    /// Swap some values
    if (strtolower($data[$frame["fields"][$i]["value"]]) == "mfsk") {
        $frame["fields"][$i]["value"] = "submode";
    } else if (strtolower($frame["fields"][$i]["value"]) == "qso_date") {
        $data[$frame["fields"][$i]["value"]] = implode(".", splitDate($data[$frame["fields"][$i]["value"]]));
    } else if (strtolower($frame["fields"][$i]["value"]) == "time_on") {
        $data[$frame["fields"][$i]["value"]] = implode(":", splitTime($data[$frame["fields"][$i]["value"]]));
    }

    /// Prepare variables for font resizing 
    do {
        list($titleleftposcorrection, $titletopposcorrection, $titlerightposcorrection,,, $titlebottomposcorrection) = imageftbbox($frame["fontsize"] - $titlefontreduction, 0, realpath("Roboto-Regular.ttf"), $frame["fields"][$i]["title"]);
        if ($titlerightposcorrection - $titleleftposcorrection > ($cellstep + $sizeunit * $frame["fields"][$i]["size"] + 3) - $cellstep - ($frame["fontsize"] - $titlefontreduction) / 2) {
            $titlefontreduction++;
        } else {
            break;
        }
    } while (true);
    do {
        list($valueleftposcorrection, $valuetopposcorrection, $valuerightposcorrection,,, $valuebottomposcorrection) = imageftbbox($frame["fontsize"] - $valuefontreduction, 0, realpath("Roboto-Regular.ttf"), $data[$frame["fields"][$i]["value"]]);
        if ($valuerightposcorrection - $valueleftposcorrection > ($cellstep + $sizeunit * $frame["fields"][$i]["size"] + 3) - $cellstep - ($frame["fontsize"] - $valuefontreduction) / 2) {
            $valuefontreduction++;
        } else {
            break;
        }
    } while (true);

    /// Set how wide should each cell be
    $cellstep += $sizeunit * $frame["fields"][$i]["size"] + 3;

    /// Cell separation line
    imageline(
        $frameimage,
        intval($cellstep),
        intval($frame["padding"]),
        intval($cellstep),
        intval($frame["fontsize"] * 2 + $frame["padding"] + $frame["fontsize"] + 9),
        $foregroundcolor
    );
    /// Title
    imagettftext(
        $frameimage,
        intval($frame["fontsize"] - $titlefontreduction),
        0,
        intval($cellstep - (($sizeunit * $frame["fields"][$i]["size"] + 3) / 2) - (($titlerightposcorrection + $titleleftposcorrection) / 2)),
        intval($frame["padding"] + ($frame["fontsize"] - $titlefontreduction) + ($frame["fontsize"] + $titlefontreduction * 2) / 4),
        $foregroundcolor,
        realpath("Roboto-Regular.ttf"),
        $frame["fields"][$i]["title"]
    );
    /// Value
    imagettftext(
        $frameimage,
        intval($frame["fontsize"] - $valuefontreduction),
        0,
        intval($cellstep - (($sizeunit * $frame["fields"][$i]["size"] + 3) / 2) - (($valuerightposcorrection + $valueleftposcorrection) / 2)),
        intval(($frame["padding"] + 6 + $frame["fontsize"] + $frame["fontsize"] / 2) + (($frame["fontsize"] - $valuefontreduction) + ($frame["fontsize"] + $valuefontreduction) / 4)),
        $foregroundcolor,
        realpath("Roboto-Regular.ttf"),
        $data[$frame["fields"][$i]["value"]]
    );
}

// Prepare QSL card
[$cardoriginalwidth, $cardoriginalheight] = getimagesize($image["path"]);
$cardimage = imagecreatefromjpeg($image["path"]);
// Resize
if ($image["resize"]["enabled"]) {
    $cardimageresized = imagecreatetruecolor($image["resize"]["width"], $image["resize"]["height"]);
    imagecopyresized($cardimageresized, $cardimage, 0, 0, 0, 0, $image["resize"]["width"], $image["resize"]["height"], $cardoriginalwidth, $cardoriginalheight);
    $cardimage = $cardimageresized;
    imagedestroy($cardimageresized);
}
/// Add space if nessesary
if (
    ($frame["pos"]["y"] + $frame["fontsize"] * 2 + $frame["padding"] * 2 + $frame["fontsize"] + 9) > imagesy($cardimage)
    || ($frame["pos"]["x"] + $frame["length"] > imagesx($cardimage))
    || $frame["pos"]["y"] < 0
    || $frame["pos"]["x"] < 0
) {
    $cardimageresized = imagecreatetruecolor(
        max(imagesx($cardimage), $frame["pos"]["x"] + $frame["length"]) + abs(min(0, $frame["pos"]["x"])),
        max(imagesy($cardimage), $frame["pos"]["y"] + $frame["fontsize"] * 2 + $frame["padding"] * 2 + $frame["fontsize"] + 9) + abs(min(0, $frame["pos"]["y"]))
    );
    imagefill($cardimageresized, 0, 0, $backgroundcolor);
    imagecopymerge($cardimageresized, $cardimage, abs(min(0, $frame["pos"]["x"])), abs(min(0, $frame["pos"]["y"])), 0, 0, imagesx($cardimage), imagesy($cardimage), 100);
    $cardimage = $cardimageresized;
}

// Put frame onto the QSL card
imagecopymerge($cardimage, $frameimage, max(0, $frame["pos"]["x"]), max(0, $frame["pos"]["y"]), 0, 0, $frame["length"], $frame["fontsize"] * 2 + $frame["padding"] * 2 + $frame["fontsize"] + 9, 100);

// Display and destroy images
imagejpeg($cardimage);
imagedestroy($frameimage);
imagedestroy($cardimage);

// Write selected data
if ($enablecounter) {
    file_put_contents("count", intval(file_get_contents("count")) + 1);
}
if ($enablelogging) {
    file_put_contents("log", time() . " " . $_GET["c"] . "\n", FILE_APPEND);
}
