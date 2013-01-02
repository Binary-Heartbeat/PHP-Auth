<?php
    class validate_email
    {
        public static function all ($_, $authLoc, $email, $email_confirm, $check)
        {
            if ($check['email']['match'] = self::match($_, $email, $email_confirm)) {
                if ($check['email']['syntax'] = self::syntax($_, $email)) {
                    if ($check['email']['available'] = self::available($_, $email)) {
                        return $check;
                    } else {
                        $check['email']['error_msg'] = $authLoc['validate']['email']['available_error'];
                    }
                } else {
                    $check['email']['error_msg'] = $authLoc['validate']['email']['syntax_error'];
                }
            } else {
                $check['email']['error_msg'] = $authLoc['validate']['email']['match_error'];
            }
            return $check;
        }
        public static function match ($_, $email, $email_confirm)
        {
            if ($email === $email_confirm) { // check if $email and $email_confirm are EXACT matches
                core::debug($_, '$email and $email_confirm are exact matches.');
                return true;
            } else {
                core::debug($_, '$email and $email_confirm are not exact matches.');
                return false;
            }
        }
        public static function syntax ($_, $email)
        {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) { // Hmm, better make sure the email address is in a valid form
                core::debug($_, '$email is valid syntax.');
                return true;
            } else {
                core::debug($_, '$email is not valid syntax.');
                return false;
            }
        }
        public static function available ($_, $email)
        {
            if (
                db::rowExists(
                    $_,
                    "SELECT COUNT(*) FROM ".$_['table_prefix']."users WHERE UserEmail=?;",
                    $email
                )
            ) {
                core::debug($_, '$email is in use.');
                return false;
            } else {
                core::debug($_, '$email is not in use.');
                return true;
            }
        }
    }
