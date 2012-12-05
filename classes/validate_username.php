<?php
	class validate_username {
		public static function all($_, $auth, $username, $reg) {
			if($reg['username']['chars'] = self::characters($_, $auth, $username)) {
				if($reg['username']['long'] = self::length_long($_, $auth, $username)) {
					if($reg['username']['short'] = self::length_short($_, $auth, $username)) {
						if($reg['username']['available'] = self::available($_, $auth, strtolower($username))) {
							return $reg;
						}
					}
				}
			}
			return $reg;
		}
		public static function characters($_, $auth, $username) {
			if(preg_match($auth['validate_username']['regex'], $username)) {
				return false;
			} else {
				core::debug($_, '$username does not contain invalid characters.');
				return true;
			}
		}
		public static function length_long($_, $auth, $username) {
			if(strlen($username) > intval($auth['validate_username']['max_length'])) {
				return false;
			} else {
				core::debug($_, '$username is not too long.');
				return true;
			}
		}
		public static function length_short($_, $auth, $username) {
			if(strlen($username) < intval($auth['validate_username']['min_length'])) {
				return false;
			} else {
				core::debug($_, '$username is not too short.');
				return true;
			}
		}
		public static function available($_, $auth, $username) {
			if(db::rowExists($_, "SELECT COUNT(*) FROM ".$_['table_prefix']."users WHERE UserNiceName=?;", array($username))) {
				return false;
			} else { // if $username isn't already in use, we run a few checks on it
				core::debug($_, '$username is not in use.');
				return true;
			}
		}
	}
