<?php

require_once('../config.php');
$authpath='../auth/';
require_once($authpath.'bridge.php');

if (isset($_POST['trigger']) and $_POST['trigger']==true) {
	if (register::validate($auth, $authLoc,$_POST['username'],$_POST['password'], $_POST['password_confirm'],$_POST['email'], $_POST['email_confirm']))
	{
		echo '<br/>'.$authLoc['reg_success_1'].'<a href="login.php">'.$authLoc['reg_success_2'].'</a>'.$authLoc['reg_success_3'].PHP_EOL;
		die();
	}
}
	echo '<br/>'.PHP_EOL.
	'<form name="register" action="register.php" method="post">'.PHP_EOL.
	'	'.$authLoc['reg_form_username'].': <input type="text" name="username" maxlength="20" /><br/>'.PHP_EOL.
	'	'.$authLoc['reg_form_password'].': <input type="password" name="password" /><br/>'.PHP_EOL.
	'	'.$authLoc['reg_form_password_again'].': <input type="password" name="password_confirm" /><br/>'.PHP_EOL.
	'	'.$authLoc['reg_form_email'].': <input type="text" name="email" /><br/>'.PHP_EOL.
	'	'.$authLoc['reg_form_email_again'].': <input type="text" name="email_confirm" /><br/>'.PHP_EOL.
	'	<input type="submit" value="'.$authLoc['reg_form_submit'].'" />'.PHP_EOL.
	'	<input name="trigger" type="hidden" value="true">'.PHP_EOL.
	'</form>';
//}
