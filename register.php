<?php

	if (isset($_POST['trigger']) and $_POST['trigger']==true) {
		$resp = recaptcha_check_answer ($auth['recaptcha']['private_key'],$_SERVER["REMOTE_ADDR"],$_POST["recaptcha_challenge_field"],$_POST["recaptcha_response_field"]);
		if($resp->is_valid) {
			core::debug($_, '');
		} elseif($resp->is_valid) {
			;
		}
		if (register::validate($_, $auth, $authLoc,$_POST['username'],$_POST['password'], $_POST['password_confirm'],$_POST['email'], $_POST['email_confirm']) and $resp->is_valid)
		{
			core::log($_,$_POST['username'],1,0);
			echo '<br/>'.$authLoc['reg_success_1'].'<a href="login.php">'.$authLoc['reg_success_2'].'</a>'.$authLoc['reg_success_3'].PHP_EOL;
		} else {
			$showForm=true;
		}
	} else {
		$showForm=true;
	}
	if($showForm==true) {
		$recapt=recaptcha_get_html($auth['recaptcha']['public_key']);
		echo <<<EOT
	<br/>
	<form name="register" action="register.php" method="post">
	$authLoc[reg_form_username]: <input type="text" name="username" maxlength="$auth[validate_username][max_length]" /><br/>
	$authLoc[reg_form_password]: <input type="password" name="password" maxlength="$auth[validate_password][max_length]	"/><br/>
	$authLoc[reg_form_password_again]: <input type="password" name="password_confirm" /><br/>
	$authLoc[reg_form_email]: <input type="text" name="email" /><br/>
	$authLoc[reg_form_email_again]: <input type="text" name="email_confirm" /><br/>
	$recapt
	<input type="submit" value="$authLoc[reg_form_submit]" />
	<input name="trigger" type="hidden" value="true">
	</form>
EOT;

	}
