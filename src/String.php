<?php
	class String {
		private $mChars;
		private $mCount;

		public function __construct() {
			$argNum = func_num_args();
			$args = func_get_args();

			if ($argNum <= 0) {
				$this->mChars = array();
				$this->mCount = 0;
			} else if ($argNum == 1) {
				$str = self::asString($args[0]);
				$this->mChars = self::asCharArray($str);
				$this->mCount = count($this->mChars);
			}
		}

		/**
		 * Appends a string to the end of an existing string.
		 * @param string $content The content to append to the string.
		 * @return String A copy of the string with the new content appended.
		 */
		public function append($content) {
			$copy = clone $this;
			$content = self::asString($content);

			$strLen = mb_strlen($content);
			if ($strLen > 1) {
				$charsToAdd = self::asCharArray($content);
				$copy->mChars = array_merge($copy->mChars, $charsToAdd);
				$copy->mCount += $strLen;
			} else if ($strLen == 1) {
				$copy->mChars[] = self::ord($content);
				$copy->mCount++;
			}

			return $copy;
		}

		/**
		 * Determines whether this string contains a substring.
		 * @param string $needle The substring to search for.
		 * @return boolean {@code true} if the needle is contained in the string, {@code false} otherwise.
		 */
		public function contains($needle) {
			return $this->indexOf($needle) >= 0;
		}

		/**
		 * Counts the number of occurrences of a substring.
		 * @param string $needle The substring to search for.
		 * @return integer The number of occurrences.
		 */
		public function count($needle) {
			$count = $pos = 0;
			while (($pos = $this->indexOf($needle, $pos + 1)) >= 0) {
				$count++;
			}
			return $count;
		}

		/**
		 * Checks if this string is equal to a PHP string.
		 * @param string $string The string to match against.
		 * @return boolean {@code true} if the strings are semantically equal, {@code false} otherwise.
		 */
		public function equals($string) {
			return $this->toString() == $string;
		}

		/**
		 * Determines the first index of a needle in the string.
		 * @param string $needle The substring to search for.
		 * @param integer $offset (Optional) The offset (from the start) at which to start searching. If not specified, will start at the beginning.
		 * @return integer The index of the specified needle.
		 */
		public function indexOf($needle, $offset = 0) {
			return $this->_indexFrom($needle, $offset, 1);
		}

		/**
		 * Determines the last index of a needle in the string.
		 * @param string $needle The substring to search for.
		 * @param integer $offset (Optional) The offset (from the start) at which to start searching backwards. Can be negative. If not specified, will start at the end.
		 * @return integer The last index of the specified needle.
		 */
		public function lastIndexOf($needle, $offset = -1) {
			if ($offset < 0) {
				$offset = $this->mCount + $offset;
			}

			return $this->_indexFrom($needle, $offset, -1);
		}

		/**
		 * Determines the index of a needle based on an offset and direction.
		 * @param string $needle The substring to search for.
		 * @param integer $offset The offset (from the start) at which to start searching.
		 * @param integer $direction The direction to search; positive for forward, otherwise backward.
		 * @return integer The index of the specified needle.
		 */
		private function _indexFrom($needle, $offset, $direction) {
			if (empty($needle)) {
				throw new InvalidArgumentException('$needle cannot be empty or null');
			}

			$needleArray = self::asCharArray($needle);
			$needleLength = count($needleArray);

			$endPoint = $direction > 0 ? $this->mCount - $needleLength + 1 : -1;

			for ($i = $offset; $i != $endPoint; $i += $direction) {
				$chr = $this->mChars[$i];

				if ($chr == $needleArray[0]) {
					if ($needleLength > 1) {
						$isIndex = true;
						for ($j = 1; $j < $needleLength; $j++) {
							if ($this->mChars[$j + $i] != $needleArray[$j]) {
								$isIndex = false;
								break;
							}
						}
						if ($isIndex) {
							return $i;
						}
					} else {
						return $i;
					}
				}
			}

			return -1;
		}

		/**
		 * Gets the length of the string.
		 * @return integer The length of the string.
		 */
		public function length() {
			return $this->mCount;
		}

		/**
		 * Pads a string on the left side until it reaches a desired length.
		 * @param integer $length The desired length of the string.
		 * @param string $char A string representing the content to pad with. (Currently supports only 1 character.)
		 * @return String A copy of the string padded to the desired length.
		 */
		public function padLeft($length, $char = " ") {
			return $this->_pad(-$length, $char);
		}

		/**
		 * Pads a string on the right side until it reaches a desired length.
		 * @param integer $length The desired length of the string.
		 * @param string $char A string representing the content to pad with. (Currently supports only 1 character.)
		 * @return String A copy of the string padded to the desired length.
		 */
		public function padRight($length, $char = " ") {
			return $this->_pad($length, $char);
		}

		/**
		 * Pads a string on until it reaches a desired length.
		 * @param integer $length The desired length of the string. If negative, will pad on the left; otherwise, will pad on the right.
		 * @param string $char A string representing the content to pad with. (Currently supports only 1 character.)
		 * @return String A copy of the string padded to the desired length.
		 */
		private function _pad($length, $char = " ") {
			if (empty($char)) {
				throw new InvalidArgumentException('$char cannot be empty or null');
			}

			if (mb_strlen($char) > 1) {
				throw new OperationNotSupportedException();
			}

			$realLength = abs($length);
			$copy = clone $this;
			if ($realLength < $this->mCount) {
				return $copy;
			}

			$copy->mChars = array_pad($copy->mChars, $length, self::ord($char));
			$copy->mCount = $realLength;

			return $copy;
		}

		/**
		 * Repeats a string a set number of times.
		 * @param integer $times The number of times to repeat the string.
		 * @return String A copy of the string repeated the specified number of times.
		 */
		public function repeat($times) {
			$copy = clone $this;

			$newLen = $copy->mCount * $times;
			$copy->mChars = array_pad($copy->mChars, $newLen, null); // Preallocate the array length
			for ($i = 1; $i < $times; $i++) {
				for ($j = 0; $j < $copy->mCount; $j++) {
					$copy->mChars[$j + ($i * $copy->mCount)] = $copy->mChars[$j];
				}
			}
			$copy->mCount = $newLen;

			return $copy;
		}

		/*public function replace($charsOld, $charsNew) {
			$copy = clone $this;
			$charMap = array();
			if (!is_array($charsOld)) {
				$charsOld = array(strval($charsOld));
			}
			if (!is_array($charsNew)) {
				$charsNew = array(strval($charsNew));
			}

			foreach ($charsOld as $k => $chr) {
				$charMap[self::ord($chr)] = self::ord($charsNew[$k]);
			}

			foreach ($copy->mChars as $i => $chr) {
				if (isset($charMap[$chr])) {
					$copy->mChars[$i] = $charMap[$chr];
				}
			}

			return $copy;
		}*/

		/**
		 * Breaks the string into an array of PHP primitive strings based on a delimiter. Works like PHP's {@code explode()}.
		 * @param string $delimiter (Optional) The character or string to split on. If not specified, uses a space.
		 * @param integer $limit (Optional) The maximum length of the resulting array. If not specified, caps at {@code PHP_INT_MAX}.
		 * @return array The string split up into an array of chunks.
		 */
		public function split($delimiter = ' ', $limit = PHP_INT_MAX) {
			$delimiterArray = self::asCharArray($delimiter);

			$delimLength = count($delimiterArray);
			$wordsCount = 0;
			$word = "";
			$words = array();
			for ($i = 0; $i <= $this->mCount; $i++) {
				$chr = $i == $this->mCount ? 0 : $this->mChars[$i];
				$addChunk = false;

				if ($chr == 0) {
					$delimString = "";
					$addChunk = true;
				} else if ($wordsCount < $limit - 1 && $chr == $delimiterArray[0]) {
					$addChunk = true;

					if ($delimLength > 1) {
						if ($this->mCount - $i >= $delimLength) {
							for ($j = 1; $j < $delimLength; $j++) {
								if ($this->mChars[$j + $i] != $delimiterArray[$j]) {
									$addChunk = false;
									break;
								}
							}
						} else {
							$addChunk = false;
						}
					}
				}

				if ($addChunk) {
					$words[] = $word;
					$word = "";
					$wordsCount++;

					$i += $delimLength - 1;
				} else {
					$c = self::chr($chr);
					$word .= $c;
				}
			}

			return $words;
		}

		/**
		 * Determines whether this string starts with a specified substring.
		 * @param string $needle The substring to check against.
		 * @return boolean {@code true} if the needle is found at index 0, {@code false} otherwise.
		 */
		public function startsWith($needle) {
			return $this->lastIndexOf($needle, 0) == 0;
		}

		/**
		 * Determines whether this string starts with a specified substring.
		 * @param string $needle The substring to check against.
		 * @return boolean {@code true} if the needle is the last substring of the string, {@code false} otherwise.
		 */
		public function endsWith($needle) {
			$expectedIndex = $this->mCount - mb_strlen($needle);
			return $this->indexOf($needle, $expectedIndex) == $expectedIndex;
		}

		/**
		 * Trims a specified set of characters from the start and end of the string.
		 * @param string $chars (Optional) The characters, as a string, to trim from the edges. If not specified, will use {@code " \t\n\r\0\x0B"}.
		 * @return String A copy of the string with the characters trimmed off.
		 */
		public function trim($chars = " \t\n\r\0\x0B") {
			return $this->_trim($chars, $chars);
		}

		/**
		 * Trims a specified set of characters from the start of the string.
		 * @param string $chars (Optional) The characters, as a string, to trim from the start. If not specified, will use {@code " \t\n\r\0\x0B"}.
		 * @return String A copy of the string with the characters trimmed off.
		 */
		public function trimLeft($chars = " \t\n\r\0\x0B") {
			return $this->_trim($chars, null);
		}

		/**
		 * Trims a specified set of characters from the end of the string.
		 * @param string $chars (Optional) The characters, as a string, to trim from the end. If not specified, will use {@code " \t\n\r\0\x0B"}.
		 * @return String A copy of the string with the characters trimmed off.
		 */
		public function trimRight($chars = " \t\n\r\0\x0B") {
			return $this->_trim(null, $chars);
		}

		/**
		 * Trims a specified set of characters from the start and end of the string.
		 * @param string $left The characters, as a string, to trim from the start.
		 * @param string $right The characters, as a string, to trim from the end.
		 * @return String A copy of the string with the characters trimmed off.
		 */
		private function _trim($left, $right) {
			$start = 0;
			$end = $this->mCount - 1;

			// Do not use mb_strstr here.
			if ($left != null) {
				while ($start <= $end) {
					if (strstr($left, $this->mChars[$start])) {
						$start++;
					} else {
						break;
					}
				}
			}
			if ($right != null) {
				while ($end >= $start) {
					if (strstr($right, $this->mChars[$end])) {
						$end--;
					} else {
						break;
					}
				}
			}

			if ($end < $start) {
				return new self();
			} else {
				$copy = clone $this;
				$copy->mCount = $end - $start + 1;
				$copy->mChars = array_slice($copy->mChars, $start, $copy->mCount);
				return $copy;
			}
		}

		/**
		 * Gets the string as an array of strings, each representing one character of the whole.
		 * @return array An array of single characters composing this string.
		 */
		public function toCharArray() {
			$stringArr = array();
			foreach ($this->mChars as $ord) {
				$stringArr[] = self::chr($ord);
			}
			return $stringArr;
		}

		/**
		 * Converts the string to lowercase.
		 * @return String A copy of the string in lowercase.
		 */
		public function toLower() {
			return new self(mb_convert_case($this->__toString(), MB_CASE_LOWER)); // TODO Improve!
		}

		/**
		 * Converts the string to uppercase.
		 * @return String A copy of the string in uppercase.
		 */
		public function toUpper() {
			return new self(mb_convert_case($this->__toString(), MB_CASE_UPPER)); // TODO Improve!
		}

		/**
		 * Converts the string to a usable PHP string.
		 * @return string This object as a PHP string.
		 */
		public function toString() {
			$string = "";
			foreach ($this->mChars as $ord) {
				$string .= self::chr($ord);
			}
			return $string;
		}

		/**
		 * Converts the string to a usable PHP string (via {@code toString()}).
		 * @return string This object as a PHP string.
		 */
		public function __toString() {
			return $this->toString();
		}

		/**
		 * Transforms any object into a usable string. Changes arrays into a single concatenated string. Is null-safe.
		 * @param $content The object to transform into a string.
		 * @return The provided object as a string.
		 */
		private static function asString($content) {
			if ($content === null) {
				return '';
			} else if (is_string($content)) {
				return $content;
			} else if (is_array($content)) {
				return implode('', $content);
			}

			return strval($content);
		}

		/**
		 * Transforms a string into an array, with a specified length of element strings. Works the same as {@code str_split()}, but is multibyte-safe.
		 * @param string $str The string to split up.
		 * @param integer $splitLength (optional) The length of each element. Default is 1.
		 * @return array The array of strings.
		 */
		public static function asArray($str, $splitLength = 1) {
			if (empty($str) || $splitLength <= 0) {
				return false;
			}

			$len = mb_strlen($str);
			$array = array();
			for ($i = 0; $i < $len; $i += $splitLength) {
				$array[] = mb_substr($str, $i, $splitLength);
			}
			return $array;
		}

		/**
		 * Returns a multibyte-safe ordinal for the first letter in a given string.
		 * @param string $chr The string or character to get the ordinal for.
		 * @return integer The ordinal value of the character.
		 */
		public static function ord($chr) {
			return hexdec(bin2hex($chr));
		}

		/**
		 * Returns a multibyte-safe character based on a given ordinal value.
		 * @param integer $chr The ordinal value of the character.
		 * @return string A string containing exactly the character for the ordinal.
		 */
		public static function chr($ord) {
			return pack('H*', dechex($ord));
		}

		/**
		 * Transforms a string into an array, with integers to represent each character in the sequence.
		 * @param string $str The string to split up.
		 * @return array The array of integers representing characters.
		 */
		public static function asCharArray($str) {
			if (empty($str)) {
				return array();
			}

			$len = mb_strlen($str);
			$array = array();
			for ($i = 0; $i < $len; $i++) {
				$array[] = self::ord(mb_substr($str, $i, 1));
			}
			return $array;
		}
	}
?>
