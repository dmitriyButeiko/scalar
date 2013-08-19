<?php
	class Assert {
		private static function doFail($failure, $message = null) {
			echo "<span style='color: darkred'><b>Assertion failed:</b> $failure";
			if ($message) {
				echo " ($message)";
			}
			echo "</span><br/>";
		}

		public static function areEqual($a, $b, $message = null) {
			if ($a == $b) {
				return true;
			}

			self::doFail("`$a == $b`", $message);
			return false;
		}

		public static function isTrue($a, $message = null) {
			if ($a) {
				return true;
			}

			self::doFail("`$a == true`", $message);
			return false;
		}

		public static function isFalse($a, $message = null) {
			if ($a) {
				return true;
			}

			self::doFail("`$a == false`", $message);
			return false;
		}
	}
?>
