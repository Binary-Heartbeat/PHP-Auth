<?php
	class validate_email {
		public static function all ($_, $email, $email_confirm, $reg) {
			if($reg['email']['match'] = self::match($_, $email, $email_confirm)) {
				if($reg['email']['syntax'] = self::syntax($_, $email)) {
					if($reg['email']['available'] = self::available($_, $email)) {
						return $reg;
					}
				}
			}
			return $reg;
		}
		public static function match ($_, $email, $email_confirm) {
			if($email === $email_confirm) { // check if $email and $email_confirm are EXACT matches
				core::debug($_, '$email and $email_confirm are exact matches.');
				return true;
			} else {
				return false;
			}
		}
		public static function syntax ($_, $email) {
			if(filter_var($email, FILTER_VALIDATE_EMAIL)) { // Hmm, better make sure the email address is in a valid form
				core::debug($_, '$email is valid syntax.');
				return true;
			} else {
				return false;
			}
		}
		public static function available ($_, $email) {
			if(db::rowExists($_, "SELECT COUNT(*) FROM ".$_['table_prefix']."users WHERE UserEmail=?;", array($email))) {
				return false;
			} else {
				core::debug($_, '$email is not in use.');
				return true;
			}
		}
	}
