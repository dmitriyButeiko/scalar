<?php
	$directory = dirname(__FILE__);

	foreach (glob("$directory/*.php") as $file) {
		if (realpath($file) == realpath(__FILE__)) {
			continue;
		}

		require($file);
	}
?>
