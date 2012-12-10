<?php
	class register {
		public static function invoke($_, $auth, $authLoc) {
			// We're gonna just assume a lot of things are false until something says otherwise
			if(isset($_POST['trigger_register']) and $_POST['trigger_register']) {
				$check['trigger'] = true;
			} else {
				$check['trigger'] = false;
			}
			$check['username']['valid']=false;
			$check['username']['chars']=false;
			$check['username']['long']=false;
			$check['username']['short']=false;
			$check['username']['available']=false;

			$check['password']['valid']=false;
			$check['password']['match']=false;
			$check['password']['long']=false;
			$check['password']['short']=false;
			$check['password']['strength']=false;

			$check['email']['valid']=false;
			$check['email']['match']=false;
			$check['email']['syntax']=false;
			$check['email']['available']=false;

			if ($check['trigger']) {
				if($auth['recaptcha']['enable']) {
					$check['captcha']['resp'] = recaptcha_check_answer($auth['recaptcha']['private_key'],
													$_SERVER["REMOTE_ADDR"],
													$_POST["recaptcha_challenge_field"],
													$_POST["recaptcha_response_field"]
													);
					if($check['captcha']['resp']->is_valid) {
						core::debug($_, 'Captcha is valid');
						$check['captcha']['valid'] = true;
					} else {
						$check['captcha']['valid'] = false;
					}
				} else {
					$check['captcha']['valid'] = true;
				}
				$check = self::validate(
					$_,
					$auth,
					$_POST['username'],
					$_POST['password'],
					$_POST['password_confirm'],
					$_POST['email'],
					$_POST['email_confirm'],
					$check
				);
				if($check['valid'] and $check['captcha']['valid']) {
					if(register::write($_, $_POST['username'], auth::hashPass($_, $auth, $_POST['password']), $_SERVER['REMOTE_ADDR'], $_POST['email'])) {
						core::log($_,$_POST['username'],1,0);
						if($_['alert_registration']) {
							$message = "New member has just registered: $_POST[username]";
							mail::send(
										$_['admin_email'], // TODO: pull proper recipient email from settings.
										'New user registration',
										$_POST['username'].' has registered.',
										$_['admin_email']
							);
						}
						$_SESSION['show_valid_register'] = true;
						if($_['debug']) {
							echo '<script>document.location = "login";</script>';
						} else {
							header('Location: login');
						}
					} else {
						echo 'Error 482: Somebody shot the server with a 12-gauge. Please contact the system administrator. Serious note: Something went wrong, drop a line by '.$_['admin_email'];
					}
				}
			}
			return $check;
		}
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
				$reg['valid'] = true;
				return $reg;
			}
			$reg['valid'] = false;
			return $reg;
		}
		public static function write($_,$username,$hash,$ip,$email) {

			$query['fields'] = 'UserName,UserNiceName,UserEmail,UserPassword,UserIP,UserGroup';
			$query['values'] = '?,?,?,?,?,?';
			$values = array($username,strtolower($username),$email,$hash,$ip,$_['default_group']);

			if($_['activation_method'] !== '0') {
				$query['fields'] = $query['fields'].',UserActive';
				$query['values'] = $query['values'].',?';
				array_push($values, '0');

				if($_['activation_method'] == '1') {
					$query['fields'] = $query['fields'].',UserActivationKey';
					$query['values'] = $query['values'].',?';
					array_push($values, auth::makeKey());
				}
			} else {
				$query['fields'] = $query['fields'].',UserActive';
				$query['values'] = $query['values'].',?';
				array_push($values, '1');
			}
			if(db::query(
				$_,
				"INSERT INTO ".$_['table_prefix']."users(".$query['fields'].") VALUES(".$query['values'].");",
				$values
			)) {
				return true;
			} else {
				return false;
			}
		}
		public static function errors($reg,$authLoc, $auth) {
			if($reg['trigger']) {
				if(!$reg['username']['valid']) {
					if(!$reg['username']['chars']) {
						echo '<br/>'.$authLoc['validate_username_invalid'].PHP_EOL;
					} elseif(!$reg['username']['long']) {
						echo '<br/>'.$authLoc['validate_username_long'].PHP_EOL;
						' ('.$auth['validate_username']['max_length'].' '.$authLoc['char_max'].')'.
						PHP_EOL;
					} elseif(!$reg['username']['short']) {
						echo '<br/>'.$authLoc['validate_username_short'].PHP_EOL;
						' ('.$auth['validate_username']['min_length'].' '.$authLoc['char_min'].')'.
						PHP_EOL;
					} elseif(!$reg['username']['available']) {
						echo '<br/>'.$authLoc['validate_username_taken'].PHP_EOL;
					}
				}
				if(!$reg['password']['valid']) {
					if(!$reg['password']['match']) {
						echo '<br/>'.$authLoc['validate_password_match_error'].PHP_EOL;
					}
					elseif(!$reg['password']['long']) {
						echo '<br/>'.$authLoc['validate_password_length_long_error'].
						' ('.$auth['validate_password']['max_length'].' '.$authLoc['char_max'].')'.
						PHP_EOL;
					}
					elseif(!$reg['password']['short']) {
						echo '<br/>'.$authLoc['validate_password_length_short_error'].
						' ('.$auth['validate_password']['min_length'].' '.$authLoc['char_min'].')'.
						PHP_EOL;
					}
					elseif(!$reg['password']['strength']) {
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
