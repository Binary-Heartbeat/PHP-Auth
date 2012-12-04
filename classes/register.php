<?php
	class register {
		public static function validate($_, $auth, $authLoc, $username, $password, $password_confirm,$email, $email_confirm) {
			$validate_username=validate_username::all($_, $auth, $authLoc, $username);
			$validate_password=validate_password::all($_, $auth, $authLoc, $password, $password_confirm);
			$validate_email=validate_email::all($_, $auth, $authLoc, $email, $email_confirm);
			if($validate_username and $validate_password and $validate_email)
			{ self::write($auth,$username,auth::hashPass($_, $auth, $password),$_SERVER['REMOTE_ADDR'],$email); return true; }
			return false;
		}
		private static function write($auth,$username,$hash,$ip,$email) {
				db::query(
					$_,
					"INSERT INTO ".$auth['table_prefix']."users(UserName,UserNiceName,UserPassword,UserEmail,UserIP) VALUES(?,?,?,?,?);",
					array($username,strtolower($username),$hash,$email,$ip)
				);
		}
	}
