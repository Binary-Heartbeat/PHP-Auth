<?php
// Validators
	// Username
	$authLoc["validate_username_invalid"] = "The provided username contains invalid characters. Usernames may contain letters, numbers, dashes (-), and underscores (_).";
	$authLoc["validate_username_short"] = "The provided username is too short.";
	$authLoc["validate_username_long"] = "The provided username is too long.";
	$authLoc["validate_username_taken"] = "This username is already in use.";
	// Password
	$authLoc["validate_password_match_error"] = "The provided passwords did not match.";
	$authLoc["validate_password_length_short_error"] = "Password is too short";
	$authLoc["validate_password_length_long_error"] = "Password is too long";
	$authLoc["validate_password_strength_error"] = "Password must contain characters from at least two of these categories: letters, numbers, symbols.";
	// Email
	$authLoc["validate_email_match_error"] = "The provided emails did not match.";
	$authLoc["validate_email_syntax_error"] = "This email address is not valid.";
	$authLoc["validate_email_available_error"] = "This email address is already in use.";

// Registration
	$authLoc["form_register"] = "Register";
	$authLoc["reg_form_submit"] = "Register";
	// Success
	$authLoc["reg_success"] = "Registration successful! You may now login.";

// Login
	//
	$authLoc["form_login"] = "Login";
	$authLoc["login_form_submit"] = "Login";
	$authLoc["login_form_remember"] = "Remember me";
	// Fail
	$authLoc["login_err_user"] = "The specified username does not exist.";
	$authLoc["login_err_password"] = "Incorrect password.";
	//
	$authLoc["login_success"]="Login successful.";

// Forms
	$authLoc["form_username"] = "Username";
	$authLoc["form_password"] = "Password";
	$authLoc["form_password_again"] = "Password Again";
	$authLoc["form_email"] = "Email";
	$authLoc["form_email_again"] = "Email Again";
	$authLoc["form_captcha"] = "Captcha";

// Misc
	$authLoc["char_min"] = "char min";
	$authLoc["char_max"] = "char max";
