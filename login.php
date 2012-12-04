<?php

require_once('../config.php');

if (isset($_POST['trigger']) and $_POST['trigger'] == true) { // $trigger is set to true by submitting the login form
	// dump login form variables from post
	$usernicename = strtolower($_POST['username']);
	$password = $_POST['password'];

	$con = db::connect($_);
	$query = "SELECT * FROM ".$_['table_prefix']."users WHERE UserNiceName=?";
	$statement = $con->prepare($query);
	$statement->execute(array($usernicename));
	//$statement = $con->query($query);
	//$row=$statement->fetch(PDO::FETCH_ASSOC);new dBug($row); die();

	if($row=$statement->fetch(PDO::FETCH_ASSOC) and db::close($statement)) {
		if (auth::checkPass($_, $authLoc, $password, $row['UserPassword'])) {
			$token=login::makeToken($_);
			// TODO: add UserLastLogin to database and update the value
			$con=db::connect($_); // merge with prepare() line?
			$query="UPDATE ".$_['table_prefix']."users SET UserToken=?, UserIP=? WHERE UserID=?";
			$statement=$con->prepare($query);
			$statement->execute(array($token,$_SERVER['REMOTE_ADDR'],$row['UserID']));
			db::close($statement);
			setcookie($_['cookie_name'], $row['UserName'], '+'.$_['cookie_expiry']);
			setcookie($_['cookie_name'].'_t', $token, '+'.$_['cookie_expiry']);

			echo '<br/>'.$authLoc['login_success'].PHP_EOL;
			die();
		} else {
			echo '<br/>'.$authLoc['login_err_match'].PHP_EOL;
		}
	} else {
		echo '<br/>'.$authLoc['login_err_user'].PHP_EOL;
	}
}
	echo '<br/>'.PHP_EOL.
	'<form name="login" action="login.php" method="post">'.PHP_EOL.
	'	'.$authLoc['login_form_username'].': <input type="text" name="username" maxlength="20" /><br/>'.PHP_EOL.
	'	'.$authLoc['login_form_password'].': <input type="password" name="password" /><br/>'.PHP_EOL.
	'	<input type="submit" value="'.$authLoc['login_form_submit'].'" />'.PHP_EOL.
	'	<input name="trigger" type="hidden" value="true">'.PHP_EOL.
	'</form>';
//}
