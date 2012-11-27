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
	$auth['debug']			=	true; // Whether or not to display debug messages

	require_once($authpath.'auth.php');
	require_once($authpath.'localizations/'.$auth['localization'].'.php');
