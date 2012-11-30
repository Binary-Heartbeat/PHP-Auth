<?php
	require_once($_['fs_root'].'auth/config.php');
	require_once($_['fs_root'].'auth/classes/auth.php');
	require_once($_['fs_root'].'auth/classes/register.php');
	require_once($_['fs_root'].'auth/classes/validate_email.php');
	require_once($_['fs_root'].'auth/classes/validate_password.php');
	require_once($_['fs_root'].'auth/classes/validate_username.php');
	require_once($_['fs_root'].'auth/localizations/'.$_['localization'].'.php');

	if(isset($auth['hash']['user_defined']) and $auth['hash']['user_defined'] and !$auth['hash']['SHA512'] and !$auth['hash']['SHA256'] and !$auth['hash']['BLOWFISH'])
	{ echo '<br/>Fatal error: Password hashing has been set to user-defined, but the type of hashing has not been specified.'.PHP_EOL; die(); }

	if(file_exists($_['fs_root'].'auth/salt.php')) { require_once($_['fs_root'].'auth/salt.php'); }

	if(array_key_exists('salt',$auth)) {
		core::debug($_, '$salt obtained from salt.php');
	} else {
		$salt=substr(sha1(mt_rand()),0,22);
		if(file_put_contents($_['fs_root'].'auth/salt.php', '<?php'.PHP_EOL.'$auth[\'salt\']=\''.$salt.'\';')) {
			echo $_['fs_root'].'auth/salt.php';
			core::debug($auth, '$salt written to salt.php');
			require_once($_['fs_root'].'auth/salt.php');
		} else {
			echo "CRITICAL ERROR: Variable 'salt' missing from salt.php, unable to write file.";
			die();
		}
	}
