<!DOCTYPE html>
<html lang="en">
<?php
include_once("config.php");
include_once("funcs.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>QSL Card Generator</title>
	<style>
		tr:hover {
			background-color: #b0b0b07a;
		}

		tr.header:hover {
			background-color: initial;
		}

		table:empty,
		tr:empty,
		th:empty,
		td:empty {
			border: none;
		}

		table,
		tr,
		th,
		td {
			border: 1px solid;
		}

		body {
			display: flex;
			align-items: center;
			flex-direction: column;
		}

		p {
			margin: 8px;
		}
	</style>
</head>

<body>
	<div>
		<form action="index.php" method="get">
			<label for="nptCallsign">Callsign: </label><input type="text" id="nptCallsign" name="c"> <input type="submit" value="Submit" id="btnSubmit">
		</form>
	</div>
	<p>ADI file last modified date:
		<b>
			<?php
			if (!file_exists($adipath)) {
				echo "FILE MISSING";
			} else {
				print(date("Y.m.d H:i:s \U\T\C", filemtime($adipath)));
			}
			?>
		</b>
	</p>
	<?php
	if (!file_exists($image["path"]))
		echo "<p><b>WARNING: CARD FILE MISSING</b></p>";
	?>
	<?php
	if ($enablecounter)
		echo "<p><b>";
	if (!file_exists("count"))
		echo 0;
	else
		echo file_get_contents("count");
	echo "</b> QSL cards generated so far</p>";
	?>
	<?php
	if (isset($_GET["c"]) && strlen($_GET["c"]) > 0)
		include_once("table.php");
	?>
	<footer><br />Created by <a href="http://github.com/minitajfun">Minitajfun</a></footer>
</body>

</html>