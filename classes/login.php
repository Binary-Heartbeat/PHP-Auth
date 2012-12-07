<?php
	class login {
		public static function errors($status, $authLoc, $auth) {
			if(isset($trigger) and $trigger) {
				if(!$status['username']) {
					echo PHP_EOL.$authLoc['login_err_user'].'<br/>';
				} elseif(!$status['password']) {
					echo PHP_EOL.$authLoc['login_err_password'].'<br/>';
				}
			}
		}
	}
