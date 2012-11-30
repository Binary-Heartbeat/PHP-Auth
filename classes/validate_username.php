<?php
	class validate_username {
		public static function all($_, $auth, $authLoc, $username) {
			if(self::characters($_, $auth, $authLoc, $username)) {
				if(self::length($_, $auth, $authLoc, $username)) {
					if(self::available($_, $auth, $authLoc, $username)) {
						return true;
					}
				}
			}
			return false;
		}
		public static function characters($_, $auth, $authLoc, $username) {
			if(preg_match($auth['validate_username']['regex'], $username)) {
				echo '<br/>'.$authLoc['reg_err_username_invalid'].PHP_EOL;
				return false;
			} else {
				core::debug($_, '$username only contains valid characters.');
				return true;
			}
		}
		public static function length($_, $auth, $authLoc, $username) {
			if(strlen($username) > intval($auth['validate_username']['max_length'])) {
				echo '<br/>'.$authLoc['reg_err_username_long'].PHP_EOL;
				return false;
			} elseif(strlen($username) < intval($auth['validate_username']['min_length'])) {
				echo '<br/>'.$authLoc['reg_err_username_short'].PHP_EOL;
				return false;
			} else {
				core::debug($_, '$username is an acceptable length');
				return true;
			}
		}
		public static function available($_, $auth, $authLoc, $username) {
			if(db::rowExists($_, 'users', 'UserNiceName', $username)) { // check if $username already exists in the database
				echo '<br/>'.$authLoc['reg_err_username_taken'].PHP_EOL;
				return false;
			} else { // if $username isn't already in use, we run a few checks on it
				core::debug($_, '$username is not in use.');
				return true;
			}
		}
	}
