<?php
// Validators
    // Username
    $authLoc['validate']['username']['syntax_error'] = "The provided username contains invalid characters. Usernames may contain letters, numbers, dashes (-), and underscores (_).";
    $authLoc['validate']['username']['short_error'] = "The provided username is too short.";
    $authLoc['validate']['username']['long_error'] = "The provided username is too long.";
    $authLoc['validate']['username']['available_error'] = "This username is already in use.";
    // Password
    $authLoc['validate']['password']['match_error'] = "The provided passwords did not match.";
    $authLoc['validate']['password']['short_error'] = "Password is too short";
    $authLoc['validate']['password']['long_error'] = "Password is too long";
    $authLoc['validate']['password']['strength_error'] = "Password must contain characters from at least two of these categories: letters, numbers, symbols.";
    // Email
    $authLoc['validate']['email']['match_error'] = "The provided emails did not match.";
    $authLoc['validate']['email']['syntax_error'] = "This email address is not valid.";
    $authLoc['validate']['email']['available_error'] = "This email address is already in use.";

// Registration
    $authLoc['form_register'] = "Register";
    $authLoc['reg_form_submit'] = "Register";
    // Success
    $authLoc['reg_success'] = "Registration successful! You may now login.";

// Login
    //
    $authLoc['form_login'] = "Login";
    $authLoc['login_form_submit'] = "Login";
    // Fail
    $authLoc['login_err_user'] = "The specified username does not exist.";
    $authLoc['login_err_password'] = "Incorrect password.";
    //
    $authLoc['login_success']="Login successful.";

// Forms
    $authLoc['form_username'] = "Username";
    $authLoc['form_password'] = "Password";
    $authLoc['form_password_again'] = "Password Again";
    $authLoc['form_email'] = "Email";
    $authLoc['form_email_again'] = "Email Again";
    $authLoc['form_captcha'] = "Captcha";
    $authLoc['form_clear'] = "Clear Form";

// Misc
    $authLoc['char_min'] = "char min";
    $authLoc['char_max'] = "char max";
