<?php
	class validate_email {
		public static function all ($auth, $authLoc, $email, $email_confirm) {
			if(self::match($auth, $authLoc, $email, $email_confirm)) {
				if(self::syntax($auth, $authLoc, $email)) {
					if(self::available($auth, $authLoc, $email)) {
						return true;
					}
				}
			}
			return false;
		}
		public static function match ($auth, $authLoc, $email, $email_confirm) {
			if($email === $email_confirm) { // check if $email and $email_confirm are EXACT matches
				_debug($auth, '$email and $email_confirm are exact matches.');
				return true;
			} else {
				echo '<br/>'.$authLoc['validate_email_match_error'].PHP_EOL;
				return false;
			}
		}
		public static function syntax ($auth, $authLoc, $email) {
			if(filter_var($email, FILTER_VALIDATE_EMAIL)) { // Hmm, better make sure the email address is in a valid form
				_debug($auth, '$email is valid syntax.');
				return true;
			} else {
				echo '<br/>'.$authLoc['validate_email_syntax_error'].PHP_EOL;
				return false;
			}
		}
		public static function available ($auth, $authLoc, $email) {
			if(authdb::rowExists($auth, 'users', 'UserEmail', $email)) { // check if $username already exists in the database
				echo '<br/>'.$authLoc['validate_email_available_error'].PHP_EOL;
				return false;
			} else {
				_debug($auth, '$email is available.');
				return true;
			}
		}
	}
