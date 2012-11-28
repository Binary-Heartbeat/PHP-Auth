<?php
	class validate_password {
		public static function all($auth, $authLoc, $password, $password_confirm) {
			if(self::match($auth, $authLoc, $password, $password_confirm)) {
				if(self::length($auth, $authLoc, $password)) {
					if(self::strength($auth, $authLoc, $password)) {
						return true;
					}
				}
			}
			return false;
		}
		public static function match ($auth, $authLoc, $password, $password_confirm) {
			if($password === $password_confirm) { // check if $password and $password_confirm are EXACT matches
				_debug($auth, '$password and $password_confirm are exact matches.');
				return true;
			} else {
				echo '<br/>'.$authLoc['validate_password_match_error'].PHP_EOL;
				return false;
			}
		}
		public static function length ($auth, $authLoc, $password) {
			if(strlen($password) < intval($auth['validate_password']['min_length'])) { // Hmm, better make sure the password is an acceptable length
				echo '<br/>'.$authLoc['validate_password_length_short_error'].PHP_EOL;
				return false;
			} elseif (strlen($password) > intval($auth['validate_password']['max_length'])) { // Lets make sure they don't exceed the length set for the database
				echo '<br/>'.$authLoc['validate_password_length_long_error'].PHP_EOL;
				return false;
			} else {
				_debug($auth, '$password is a valid length.');
				return true;
			}
		}
		public static function strength ($auth, $authLoc, $password) {
			$password_strength=0;

			$password_strength_letters='/[a-zA-Z]/'; // As silly as it seems, lets define the alphabet
			if(preg_match($password_strength_letters, $password)) { // check if $password contains letters
				$password_strength++;
				_debug($auth, 'Letters present in $password.');
			} else {
				_debug($auth, 'Letters not present in $password.');
			}
			$password_strength_numbers='/[0123456789]/'; // Define numbers...
			if(preg_match($password_strength_numbers, $password)) { // check if $password contains numbers
				_debug($auth, 'Numbers present in $password.');
				$password_strength++;
			} else {
				_debug($auth, 'Numbers not present in $password.');
			}
			$password_strength_symbols="/[`~!@#$%^&*()-_=+\[{\]};:\\|,<.>\/?]/"; // And lastly symbols
			if(preg_match($password_strength_symbols, $password)) { // check if $password contains symbols
				_debug($auth, 'Symbols present in $password.');
				$password_strength++;
			} else {
				_debug($auth, 'Symbols not present in $password.');
			}

			_debug($auth, '$password_strength = '.$password_strength);

			if($password_strength < intval($auth['validate_password']['strength'])) { // Make sure that the password contains at least two types of characters
				echo '<br/>'.$authLoc['validate_password_strength_error'].PHP_EOL;
				return false;
			} else {
				_debug($auth, '$password contains characters from two, or all, of the following: letters, numbers, symbols.');
				return true;
			}
		}
	}
