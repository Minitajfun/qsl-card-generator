<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if (!isset($_GET["c"]) || strlen($_GET["c"]) == 0 || !isset($_GET["i"]) || strlen($_GET["i"]) == 0) {
    header("HTTP/1.0 404 Not Found");
    exit();
}

include_once("config.php");
include_once("funcs.php");

$a = createarray($adipath);
$x = 0;
$v;
for ($i = 0; $i < count($a); $i++) {
    if (isset($a[$i]["call"]) && $a[$i]["call"] == $_GET["c"])
        if ($x == intval($_GET["i"])) {
            $v = $a[$i];
            break;
        } else {
            $x = $x + 1;
        }
}

if (!isset($v)) {
    header("HTTP/1.1 204 NO CONTENT");
    exit();
}

unset($a);
unset($x);

header('Content-Type: image/jpeg');
header('Content-Disposition: attachment; filename="card_' . $_GET["c"] . '_' . $v["qso_date"] . $v["time_on"] . '.jpg"');


$c = imagecreatefromjpeg($cardpath);

imagesetthickness($c, 3);
$fg = imagehexcoloralloacate(
    $c,
    $frame["foreground"]
);
$bg = imagehexcoloralloacate(
    $c,
    $frame["background"]
);

$sizeunit = 0;
foreach ($frame["fields"] as $f) {
    $sizeunit += $f["size"];
}
$sizeunit = ($frame["length"] - 2 * $frame["padding"] - 3 * (count($frame["fields"]) + 1)) / $sizeunit;

// draw BG and outer frame
imagefilledrectangle(
    $c,
    $frame["pos"]["x"],
    $frame["pos"]["y"],
    $frame["pos"]["x"] + $frame["length"],
    $frame["pos"]["y"] + 110,
    $bg
);
imagerectangle(
    $c,
    $frame["pos"]["x"] + $frame["padding"],
    $frame["pos"]["y"] + $frame["padding"],
    $frame["pos"]["x"] + $frame["length"] - $frame["padding"],
    $frame["pos"]["y"] + 110 - $frame["padding"],
    $fg
);
imageline(
    $c,
    $frame["pos"]["x"] + $frame["padding"],
    $frame["pos"]["y"] + $frame["padding"] + 3 + $frame["fontsize"] + $frame["fontsize"] / 2,
    $frame["pos"]["x"] + $frame["length"] - $frame["padding"],
    $frame["pos"]["y"] + $frame["padding"] + 3 + $frame["fontsize"] + $frame["fontsize"] / 2,
    $fg
);

$st = $frame["pos"]["x"] + $frame["padding"] + 3;
for ($i = 0; $i < count($frame["fields"]); $i++) {
    $cft = 0;
    $leftt = 0;
    $rightt = 0;
    $topt = 0;
    $bottomt = 0;
    $cfv = 0;
    $leftv = 0;
    $rightv = 0;
    $topv = 0;
    $bottomv = 0;
    if ($v[$frame["fields"][$i]["value"]] == "MFSK")
        $frame["fields"][$i]["value"] = "submode";
    else if ($frame["fields"][$i]["value"] == "qso_date")
        $v[$frame["fields"][$i]["value"]] = implode(".", splitDate($v[$frame["fields"][$i]["value"]]));
    else if ($frame["fields"][$i]["value"] == "time_on")
        $v[$frame["fields"][$i]["value"]] = implode(":", splitTime($v[$frame["fields"][$i]["value"]]));
    do {
        list($leftt, $topt, $rightt, , , $bottomt) = imageftbbox($frame["fontsize"] - $cft, 0, "Roboto-Regular.ttf", $frame["fields"][$i]["title"]);
        if ($rightt - $leftt > ($st + $sizeunit * $frame["fields"][$i]["size"] + 3) - $st - ($frame["fontsize"] - $cft) / 2)
            $cft++;
        else
            break;
    } while (true);
    do {
        list($leftv, $topv, $rightv, , , $bottomv) = imageftbbox($frame["fontsize"] - $cfv, 0, "Roboto-Regular.ttf", $v[$frame["fields"][$i]["value"]]);
        if ($rightv - $leftv > ($st + $sizeunit * $frame["fields"][$i]["size"] + 3) - $st - ($frame["fontsize"] - $cfv) / 2)
            $cfv++;
        else
            break;
    } while (true);
    $st += $sizeunit * $frame["fields"][$i]["size"] + 3;
    imageline(
        $c,
        $st,
        $frame["pos"]["y"] + $frame["padding"],
        $st,
        $frame["pos"]["y"] + 110 - $frame["padding"],
        $fg
    );
    imagettftext(
        $c,
        $frame["fontsize"] - $cft,
        0,
        $st - (($sizeunit * $frame["fields"][$i]["size"] + 3) / 2) - (($rightt + $leftt) / 2),
        $frame["pos"]["y"] + $frame["padding"] + ($frame["fontsize"] - $cft) + ($frame["fontsize"] + $cft * 2) / 4,
        $fg,
        "Roboto-Regular.ttf",
        $frame["fields"][$i]["title"]
    );
    imagettftext(
        $c,
        $frame["fontsize"] - $cfv,
        0,
        $st - (($sizeunit * $frame["fields"][$i]["size"] + 3) / 2) - (($rightv + $leftv) / 2),
        ($frame["pos"]["y"] + $frame["padding"] + 6 + $frame["fontsize"] + $frame["fontsize"] / 2) + (($frame["fontsize"] - $cfv) + ($frame["fontsize"] + $cfv) / 2),
        $fg,
        "Roboto-Regular.ttf",
        $v[$frame["fields"][$i]["value"]]
    );
}

imagejpeg($c);
imagedestroy($c);

if ($enablecounter) file_put_contents("count", intval(file_get_contents("count")) + 1);
if ($enablelogging) file_put_contents("log", time() . " " . $_GET["c"] . "\n", FILE_APPEND);

?>