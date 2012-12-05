<?php
	class register {
		public static function validate($_, $auth, $username, $password, $password_confirm,$email, $email_confirm, $reg) {
			$reg=validate_username::all($_, $auth, $username, $reg);
			if(
				$reg['username']['chars'] and
				$reg['username']['long'] and
				$reg['username']['short'] and
				$reg['username']['available']
			) {
				$reg['username']['valid'] = true;
			}

			$reg=validate_password::all($_, $auth, $password, $password_confirm, $reg);
			if(
				$reg['password']['match'] and
				$reg['password']['long'] and
				$reg['password']['short'] and
				$reg['password']['strength']
			) {
				$reg['password']['valid'] = true;
			}

			$reg=validate_email::all($_, $email, $email_confirm, $reg);
			if(
				$reg['email']['match'] and
				$reg['email']['syntax'] and
				$reg['email']['available']
			) {
				$reg['email']['valid'] = true;
			}

			if($reg['username']['valid'] and $reg['password']['valid'] and $reg['email']['valid'])
			{
				self::write($_,$username,auth::hashPass($_, $auth, $password),$_SERVER['REMOTE_ADDR'],$email);
				$reg['valid'] = true;
				return $reg;
			}
			$reg['valid'] = false;
			return $reg;
		}
		private static function write($_,$username,$hash,$ip,$email) {
				db::query(
					$_,
					"INSERT INTO ".$_['table_prefix']."users(UserName,UserNiceName,UserPassword,UserEmail,UserIP) VALUES(?,?,?,?,?);",
					array($username,strtolower($username),$hash,$email,$ip)
				);
		}
		public static function errors($reg,$authLoc, $auth) {
			if($reg['trigger']) {
				if(!$reg['username']['valid']) {
					if(!$reg['username']['chars']) {
						echo '<br/>'.$authLoc['validate_username_invalid'].PHP_EOL;
					}
					if(!$reg['username']['long']) {
						echo '<br/>'.$authLoc['validate_username_long'].PHP_EOL;
						' ('.$auth['validate_username']['max_length'].' '.$authLoc['char_max'].')'.
						PHP_EOL;
					}
					if(!$reg['username']['short']) {
						echo '<br/>'.$authLoc['validate_username_short'].PHP_EOL;
						' ('.$auth['validate_username']['min_length'].' '.$authLoc['char_min'].')'.
						PHP_EOL;
					}
					if(!$reg['username']['available']) {
						echo '<br/>'.$authLoc['validate_username_taken'].PHP_EOL;
					}
				}
				if(!$reg['password']['valid']) {
					if(!$reg['password']['match']) {
						echo '<br/>'.$authLoc['validate_password_match_error'].PHP_EOL;
					}
					if(!$reg['password']['long']) {
						echo '<br/>'.$authLoc['validate_password_length_long_error'].
						' ('.$auth['validate_password']['max_length'].' '.$authLoc['char_max'].')'.
						PHP_EOL;
					}
					if(!$reg['password']['short']) {
						echo '<br/>'.$authLoc['validate_password_length_short_error'].
						' ('.$auth['validate_password']['min_length'].' '.$authLoc['char_min'].')'.
						PHP_EOL;
					}
					if(!$reg['password']['strength']) {
						echo '<br/>'.$authLoc['validate_password_strength_error'].PHP_EOL;
					}
				}
				if(!$reg['email']['valid']) {
					if(!$reg['email']['match']) {
						echo '<br/>'.$authLoc['validate_email_match_error'].PHP_EOL;
					}
					elseif(!$reg['email']['syntax']) {
						echo '<br/>'.$authLoc['validate_email_syntax_error'].PHP_EOL;
					}
					elseif(!$reg['email']['available']) {
						echo '<br/>'.$authLoc['validate_email_available_error'].PHP_EOL;
					}
				}
			}
		}
	}
