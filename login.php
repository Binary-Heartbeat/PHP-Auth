<?php
	$status['username'] = false;
	$status['password'] = false;

	if (isset($_POST['trigger_login']) and $_POST['trigger_login']) { // $trigger is set to true by submitting the login form
		// dump login form variables from post
		$usernicename = strtolower($_POST['username']);
		$password = $_POST['password'];

		$row = db::getRow($_,
							"SELECT * FROM ".$_['table_prefix']."users WHERE UserNiceName=?",
							$usernicename
							);

		if($row) {
			if (auth::checkPass($_, $auth, $authLoc, $password, $row['UserPassword'])) {
				$token=auth::makeToken($_);
				// TODO: add UserLastLogin to database and update the value
				db::query($_,
							"UPDATE ".$_['table_prefix']."users SET UserToken=?, UserIP=? WHERE UserID=?",
							array($token,$_SERVER['REMOTE_ADDR'],$row['UserID'])
							);
				setcookie($_['cookie_name'], $row['UserName'], '+'.$_['cookie_expiry']);
				setcookie($_['cookie_name'].'_t', $token, '+'.$_['cookie_expiry']);

				echo '<br/>'.$authLoc['login_success'].PHP_EOL;
				$status['password'] = true;
			}
		} else {
			$status['username'] = false;
		}
	}
