<?php
	// We're gonna just assume a lot of things are false until something says otherwise
	if(isset($_POST['trigger_register']) and $_POST['trigger_register']) {
		$reg['trigger'] = true;
		$trigger = true;
	} else {
		$reg['trigger'] = false;
		$trigger = false;
	}
	$reg['username']['valid']=false;
	$reg['username']['chars']=false;
	$reg['username']['long']=false;
	$reg['username']['short']=false;
	$reg['username']['available']=false;

	$reg['password']['valid']=false;
	$reg['password']['match']=false;
	$reg['password']['long']=false;
	$reg['password']['short']=false;
	$reg['password']['strength']=false;

	$reg['email']['valid']=false;
	$reg['email']['match']=false;
	$reg['email']['syntax']=false;
	$reg['email']['available']=false;

	if ($reg['trigger']) {
		$resp = recaptcha_check_answer ($auth['recaptcha']['private_key'],
										$_SERVER["REMOTE_ADDR"],
										$_POST["recaptcha_challenge_field"],
										$_POST["recaptcha_response_field"]
										);
		if($resp->is_valid) {
			core::debug($_, 'Captcha is valid');
		} else {
			$reg['captcha']=false;
		}
		$reg = register::validate($_,
									  $auth,
									  $_POST['username'],
									  $_POST['password'],
									  $_POST['password_confirm'],
									  $_POST['email'],
									  $_POST['email_confirm'],
									  $reg
									);
		if($reg['valid'] and $resp->is_valid) {
			core::log($_,$_POST['username'],1,0);
			$_SESSION['show_valid_register'] = true;
			header('Location: login');
			//echo '<br/>'.$authLoc['reg_success_1'].'<a href="login.php">'.$authLoc['reg_success_2'].'</a>'.$authLoc['reg_success_3'].PHP_EOL;
		}
	}
