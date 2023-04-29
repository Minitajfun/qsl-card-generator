<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once("config.php");
include_once("funcs.php");

$c = $_GET["q"];

$d = createarray($adipath);
$n = 0;

$rs = "<tr class='header'>";
foreach ($table["fields"] as $f) {
    $rs .= "<th>{$f["title"]}</th>";
}
$rs .= "</tr>";
foreach ($d as $de) {
    if (isset($de["call"]) && $de["call"] == $_GET["q"]) {
        $rs .= "<tr onclick=\"generateCard('{$de["call"]}',{$n})\">";
        foreach ($table["fields"] as $f) {
            if ($f["value"] == "qso_date")
                $rs .= "<td>" . implode(".", splitDate($de[$f["value"]])) . "</td>";
            else if ($f["value"] == "time_on")
                $rs .= "<td>" . implode(":", splitTime($de[$f["value"]])) . "</td>";
            else if ($de[$f["value"]] == "MFSK")
                $rs .= "<td>{$de["submode"]}</td>";
            else
                $rs .= "<td>{$de[$f["value"]]}</td>";
        }
        $rs .= "</tr>";
        $n++;
    }
}

if ($n == 0)
    header("HTTP/1.1 204 NO CONTENT");
else
    print($rs);
?>