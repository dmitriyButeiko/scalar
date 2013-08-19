<html>
	<head>
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.10.0.min.js"></script>
	</head><body>
		<?php
			error_reporting(E_ALL);

			function onError($errno, $errstr, $errfile = null, $errline = 0, $errcontext = array()) {
				echo "<span style='color: darkred'><b>Error $errno:</b> $errstr (in $errfile on line $errline)</span>";
				return false;
			}
			set_error_handler("onError");

			require("Assert.php");
			require("src/Scalibrary.php");
			foreach (glob("tests/*.php") as $file) {
				echo "<div><b>$file</b></div>";
				echo "<div>";
				require($file);
				echo "</div><br/><br/>";
			}
		?>
	</body>
</html>
