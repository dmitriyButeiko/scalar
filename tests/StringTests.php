<ul>
	<?php
		class StringTests {
			public static function TestLength() {
				$s1 = new String();
				$s2 = new String("");
				$s3 = new String("test");
				$s4 = new String("test      ");

				$success = true;
				$success &= Assert::areEqual($s1->length(), 0, "`new String()` has a length of 0");
				$success &= Assert::areEqual($s2->length(), 0, "`new String(\"\")` has a length of 0");
				$success &= Assert::areEqual($s3->length(), 4, "`new String(\"test\")` has a length of 4");
				$success &= Assert::areEqual($s4->length(), 10, "`new String(\"test      \")` has a length of 10");

				return $success;
			}

			public static function TestAppend() {
				$s1a = new String();
				$s1b = $s1a->append("z");

				$s2a = new String("");
				$s2b = $s2a->append("z");

				$s3a = new String("z");
				$s3b = $s3a->append("z");

				$success = true;
				
				$success &= Assert::isTrue($s1a->equals(""), "String 1a is not affected by append");
				$success &= Assert::isTrue($s1b->equals("z"), "String 1b is appended");
				
				$success &= Assert::isTrue($s2a->equals(""), "String 2a is not affected by append");
				$success &= Assert::isTrue($s2b->equals("z"), "String 2b is appended");
				
				$success &= Assert::isTrue($s3a->equals("z"), "String 3a is not affected by append");
				$success &= Assert::isTrue($s3b->equals("zz"), "String 3b is appended");

				return $success;
			}
		}

		foreach (get_class_methods("StringTests") as $method) {
			echo "<li><b>{$method}</b>: ";
			$result = call_user_func("StringTests::$method");
			echo $result ? "<span style='color:green'>Passed</span>" : "<span style='color:red'>Failed</span>";
			echo "</li>";
		}
	?>
</ul>