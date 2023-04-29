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
	</style>
	<script>
		document.addEventListener("DOMContentLoaded", () => {
			document.getElementById('nptCallsign').addEventListener("keydown", (e) => { if (e.keyCode === 13) fetchTable(); });
			document.getElementById('btnSubmit').addEventListener("click", fetchTable);
		});

		function fetchTable() {
			if (document.getElementById('nptCallsign').value.length == 0) return;
			let xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function () {
				if (this.readyState == 4 && this.status == 200) {
					document.getElementById('results').innerHTML = this.responseText;
				}
				else if (this.readyState == 4 && this.status == 204) {
					alert("Callsign not found");
				}
			};
			xhttp.open("GET", "table.php?q=" + document.getElementById('nptCallsign').value, true);
			xhttp.send();
		}

		function generateCard(c, i) {
			window.location.href = `gen.php?c=${c}&i=${i}`;
		};
	</script>
</head>

<body>
	<div>
		<label for="nptCallsign">Callsign: </label><input type="text" id="nptCallsign"> <input type="submit"
			value="Submit" id="btnSubmit">
	</div>
	<p>Adi file last update:
		<b>
			<?php
			$t = file_get_contents($adipath);
			preg_match("<CREATED_TIMESTAMP:\d+>", $t, $m, PREG_OFFSET_CAPTURE);
			$t = substr($t, $m[0][1] + strlen($m[0][0]) + 1, intval(explode(":", $m[0][0])[1]));
			echo implode('.', splitDate(explode(' ', $t)[0])) . ' ' . implode(':', splitTime(explode(' ', $t)[1]));
			?>
		</b>
	</p><?php 
	if ($enablecounter) echo "<p><b>" . (file_get_contents("count")) . "</b> QSL cards generated so far</p>";?>
	<table id="results"></table>
	<footer><br />Created by <a href="http://github.com/minitajfun">Minitajfun</a></footer>
</body>

</html>