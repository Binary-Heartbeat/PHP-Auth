<?php
	class validate_username {
		public static function all($auth, $authLoc, $username) {
			if(self::characters($auth, $authLoc, $username)) {
				if(self::available($auth, $authLoc, $username)) {
					if(self::length($auth, $authLoc, $username)) {
						return true;
					}
				}
			}
			return false;
		}
		public static function characters($auth, $authLoc, $username) {
			if(preg_match($auth['validate_username']['regex'], $username)) {
				echo '<br/>'.$authLoc['reg_err_username_invalid'].PHP_EOL;
				return false;
			} else {
				_debug($auth, '$username only contains valid characters.');
				return true;
			}
		}
		public static function available($auth, $authLoc, $username) {
			if(authdb::rowExists($auth, 'users', 'UserNiceName', $username)) { // check if $username already exists in the database
				echo '<br/>'.$authLoc['reg_err_username_taken'].PHP_EOL;
				return false;
			} else { // if $username isn't already in use, we run a few checks on it
				_debug($auth, '$username is not in use.');
				return true;
			}
		}
		public static function length($auth, $authLoc, $username) {
			if(strlen($username) > intval($auth['validate_username']['max_length'])) {
				echo '<br/>'.$authLoc['reg_err_username_long'].PHP_EOL;
				return false;
			} elseif(strlen($username) < intval($auth['validate_username']['min_length'])) {
				echo '<br/>'.$authLoc['reg_err_username_short'].PHP_EOL;
				return false;
			} else {
				_debug($auth, '$username is an acceptable length');
				return true;
			}
		}
	}
