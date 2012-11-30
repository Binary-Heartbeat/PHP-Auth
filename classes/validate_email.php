<?php
	class validate_email {
		public static function all ($_, $auth, $authLoc, $email, $email_confirm) {
			if(self::match($_, $authLoc, $email, $email_confirm)) {
				if(self::syntax($_, $authLoc, $email)) {
					if(self::available($_, $auth, $authLoc, $email)) {
						return true;
					}
				}
			}
			return false;
		}
		public static function match ($_, $authLoc, $email, $email_confirm) {
			if($email === $email_confirm) { // check if $email and $email_confirm are EXACT matches
				core::debug($_, '$email and $email_confirm are exact matches.');
				return true;
			} else {
				echo '<br/>'.$authLoc['validate_email_match_error'].PHP_EOL;
				return false;
			}
		}
		public static function syntax ($_, $authLoc, $email) {
			if(filter_var($email, FILTER_VALIDATE_EMAIL)) { // Hmm, better make sure the email address is in a valid form
				core::debug($_, '$email is valid syntax.');
				return true;
			} else {
				echo '<br/>'.$authLoc['validate_email_syntax_error'].PHP_EOL;
				return false;
			}
		}
		public static function available ($_, $authLoc, $email) {
			if(db::rowExists($_, 'users', 'UserEmail', $email)) { // check if $username already exists in the database
				echo '<br/>'.$authLoc['validate_email_available_error'].PHP_EOL;
				return false;
			} else {
				core::debug($_, '$email is available.');
				return true;
			}
		}
	}
