<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once("config.php");
include_once("funcs.php");

if (!file_exists($adipath))
    return;
if (!isset($_GET["c"]) || strlen($_GET["c"]) == 0)
    exit();

$d = createarray($adipath);
$n = 0;

$rs = "<table><tr class='header'>";
foreach ($table["fields"] as $f) {
    $rs .= "<th>{$f["title"]}</th>";
}
$rs .= "</tr>";
foreach ($d as $de) {
    if (isset($de["call"]) && strtolower($de["call"]) == strtolower($_GET["c"])) {
        $rs .= "<tr>";
        foreach ($table["fields"] as $f) {
            if (!isset($de[$f["value"]]))
                $de[$f["value"]] = "&nbsp;";

            if (strtolower($f["value"]) == "qso_date")
                $rs .= "<td>" . implode(".", splitDate($de[$f["value"]])) . "</td>";
            else if (strtolower($f["value"]) == "time_on")
                $rs .= "<td>" . implode(":", splitTime($de[$f["value"]])) . "</td>";
            else if (strtolower($de[$f["value"]]) == "MFSK")
                $rs .= "<td>{$de["submode"]}</td>";
            else
                $rs .= "<td>{$de[$f["value"]]}</td>";
        }
        $rs .= "<td><form method=\"GET\" action=\"gen.php\"><input type=\"hidden\" name=\"c\" value=\"" . $_GET["c"] . "\"><input type=\"hidden\" name=\"i\" value=\"" . $n . "\"><input type=\"submit\" value=\"Generate\"></form></td></tr>";
        $n++;
    }
}
if ($n == 0)
    $rs .= "<td colspan=\"" . count($table["fields"]) . "\">No match found</td>";
$rs .= "</table>";

print($rs);

?>