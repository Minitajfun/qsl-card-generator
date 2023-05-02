<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once("config.php");
include_once("funcs.php");

if (!isset($_GET["c"]) || strlen($_GET["c"]) == 0)
    exit();

$d = createarray($adipath);
$n = 0;

$rs = "<tr class='header'>";
foreach ($table["fields"] as $f) {
    $rs .= "<th>{$f["title"]}</th>";
}
$rs .= "</tr>";

foreach ($d as $de) {
    if (isset($de["call"]) && $de["call"] == $_GET["c"]) {
        $rs .= "<tr>";
        foreach ($table["fields"] as $f) {
            if (!isset($de[$f["value"]])) $de[$f["value"]] = "&nbsp;";

            if ($f["value"] == "qso_date")
                $rs .= "<td>" . implode(".", splitDate($de[$f["value"]])) . "</td>";
            else if ($f["value"] == "time_on")
                $rs .= "<td>" . implode(":", splitTime($de[$f["value"]])) . "</td>";
            else if ($de[$f["value"]] == "MFSK")
                $rs .= "<td>{$de["submode"]}</td>";
            else
                $rs .= "<td>{$de[$f["value"]]}</td>";
        }
        $rs .= "<td><a href=\"gen.php?c=" . $_GET["c"] . "&i=$n\"><input type=\"button\" value=\"Generate\"></a></td></tr>";
        $n++;
    }
}

if ($n == 0)
    $rs .= "<td colspan=\"" . count($table["fields"]) . "\">No match found</td>";

print($rs);

?>