<?php declare(strict_types=1);

function imagehexcoloralloacate(GdImage $img, String $hex)
{
    $hex = trim($hex, "#");
    return imagecolorallocate(
        $img,
        hexdec(substr($hex, 0, 2)),
        hexdec(substr($hex, 2, 2)),
        hexdec(substr($hex, 4, 2))
    );
}

function createarray(String $adifile)
{
    $t = file_get_contents("./" . $adifile);
    $ra = [];

    $t = substr($t, strpos($t, "<EOH>") + 9);

    $da = explode("<EOR>", $t);

    foreach ($da as $d) {
        preg_match_all("/<.+?:\d+>/", $d, $matches);
        array_push($ra, array());
        for ($i = 0; $i < count($matches[0]); $i++)
            $ra[count($ra) - 1][explode(":", substr($matches[0][$i], 1))[0]] = substr($d, strpos($d, $matches[0][$i]) + strlen($matches[0][$i]), intval(explode(":", $matches[0][$i])[1]));

    }

    return array_reverse($ra);
}

function splitDate(mixed $date) {return [ "y" => substr(strval($date), 0, 4), "m" => substr(strval($date), 4, 2), "d" => substr(strval($date), 6, 2)];}
function splitTime(mixed $time) {return [ "h" => substr(strval($time), 0, 2), "m" => substr(strval($time), 2, 2), "s" => substr(strval($time), 4, 2)];}
?>