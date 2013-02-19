<?php
    require_once $_['fs_root'].'includes/lib/auth/classes/activate.php';
    require_once $_['fs_root'].'includes/lib/auth/classes/auth.php';
    require_once $_['fs_root'].'includes/lib/auth/classes/login.php';
    require_once $_['fs_root'].'includes/lib/auth/classes/logout.php';
    require_once $_['fs_root'].'includes/lib/auth/classes/protect.php';
    require_once $_['fs_root'].'includes/lib/auth/classes/recaptchalib.php';
    require_once $_['fs_root'].'includes/lib/auth/classes/register.php';
    require_once $_['fs_root'].'includes/lib/auth/classes/validate_email.php';
    require_once $_['fs_root'].'includes/lib/auth/classes/validate_password.php';
    require_once $_['fs_root'].'includes/lib/auth/classes/validate_username.php';
    require_once $_['fs_root'].'includes/lib/auth/localizations/'.$_['localization'].'.php';


    if (
        isset($auth['hash']['preference']) and
        !$auth['hash']['preference'] == 'SHA512' and
        !$auth['hash']['preference'] == 'SHA256' and
        !$auth['hash']['preference'] == 'BLOWFISH')
    { // TODO: Switch to error throw function
        core::error($_, 'Fatal error: Password hashing is user-defined, but the type of hashing has not been specified.', 'fatal');
    }

    if (file_exists($_['fs_root'].'includes/lib/auth/salt.php'))
    {
        require_once $_['fs_root'].'includes/lib/auth/salt.php';
    }

    if (array_key_exists('salt', $auth)) {
        core::debug($_, '$salt obtained from salt.php');
    } else {
        $salt = substr(sha1(mt_rand()),0,22);
        if (file_put_contents(
            $_['fs_root'].'includes/lib/auth/salt.php',
            '<?php'.PHP_EOL.'$auth[\'salt\']=\''.$salt.'\';'
            )
        ) {
            echo $_['fs_root'].'includes/lib/auth/salt.php';
            core::debug($_, '$salt written to salt.php');
            require_once $_['fs_root'].'includes/lib/auth/salt.php';
        } else {
            echo "CRITICAL ERROR: Variable 'salt' missing from salt.php, unable to write file.";
            die();
        }
    }
