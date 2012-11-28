<?php
// Before referencing this file for execution in any way, you need to define $authpath. e.g.
	// $authpath=../Auth/';
	// require_once($authpath.'bridge.php');
// $authpath is the relative or absolute path to the folder containing the files of this 'module'
// Please include the trailing slash

	// This file serves as a configuration bridge between the main PHP scripts and this auth 'module'
	// You can set each of the $auth variables to equal another variable, or a string
	$auth['db_host']		=	$_['db_host']; // Database host to connect to
	$auth['db_user']		=	$_['db_user']; // Database user to connect as
	$auth['db_pass']		=	$_['db_pass']; // Password for the above database user
	$auth['db_name']		=	$_['db_name']; // Name of the database being connected to
	$auth['table_prefix']	=	$_['table_prefix']; // Prefix for the 'users' table
	$auth['fs_root']		=	$_['fs_root']; // The root directory of the app this auth 'module' is being used in
	$auth['localization']	=	$_['localization']; // Localization to use
	$auth['admin_email']	=	$_['admin_email'];
	$auth['debug']			=	$_['debug']; // Whether or not to display debug messages


// Auth-specific config. This is independent from the application this is being paired with. In most cases the default values are suitable.
	// Password
		$auth['validate_password']['min_length'] = '8'; // Min length. Numerical values only.
		$auth['validate_password']['max_length'] = '64'; // Max length. Numerical values only.
		/* From the character categories letters, numbers, and symbols, how many types have to be used when creating a password
		Numerical values ranging from 1 to 3 inclusive only */
		$auth['validate_password']['strength'] = '2';

	// Username
		$auth['validate_username']['min_length'] = '3'; // Min length. Numerical values only.
		$auth['validate_username']['max_length'] = '20'; // Max length. Numerical values only.
		$auth['validate_username']['regex']='/[^a-zA-Z0123456789\-_]/'; // Regex for valid characters.

	// Password hashing
		$auth['hash']['user_defined']=false; // true or false. If false, nothing below this point will be used
		// If the above line is set to true, ONE of these must also be set to true
		$auth['hash']['SHA512']=false; // true or false. Specifies SHA512 for hashing
		$auth['hash']['SHA256']=false; // true or false. Specifies SHA256 for hashing
		$auth['hash']['BLOWFISH']=false; // true or false. Specifies BLOWFISH for hashing

		// default for SHA512 and SHA256 is 'rounds=5000'
		// default for BLOWFISH is '10'
		// refer to http://php.net/manual/en/function.crypt.php if you are unsure about this
		$auth['hash']['cost']='rounds=5000';


	// No touchy beyond this line

	if($auth['hash']['user_defined'] and !$auth['hash']['SHA512'] and !$auth['hash']['SHA256'] and !$auth['hash']['BLOWFISH'])
	{ echo '<br/>Fatal error: Password hashing has been set to user-defined, but the type of hashing has not been specified.'.PHP_EOL; die(); }

	require_once($authpath.'functions.php');
	require_once($authpath.'localizations/'.$auth['localization'].'.php');
	if(file_exists($authpath.'salt.php')) { require_once($authpath.'salt.php'); }

	if(array_key_exists('salt',$auth)) {
		_debug($auth, '$salt obtained from salt.php');
	} else {
		$salt=substr(sha1(mt_rand()),0,22);
		if(file_put_contents($authpath.'salt.php', '<?php'.PHP_EOL.'$auth[\'salt\']=\''.$salt.'\';')) {
			echo $authpath.'salt.php';
			_debug($auth, '$salt written to salt.php');
			require_once($authpath.'salt.php');
		} else {
			echo "CRITICAL ERROR: Variable 'salt' missing from salt.php, unable to write file.";
			die();
		}
	}
