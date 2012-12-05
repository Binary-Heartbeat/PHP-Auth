<?php
	class login {
		public static function errors($status, $authLoc, $auth) {
			if(!$status['username']) {
				echo PHP_EOL.$authLoc['login_err_user'].'<br/>';
			} elseif(!$status['password']) {
				echo PHP_EOL.$authLoc['login_err_match'].'<br/>';
			}
		}
	}
