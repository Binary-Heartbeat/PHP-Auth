<?php
    class validate_username
    {
        public static function all($_, $auth, $authLoc, $username, $check)
        {
            if ($check['username']['chars'] = self::syntax($_, $auth, $username)) {
                if ($check['username']['long'] = self::long($_, $auth, $username)) {
                    if ($check['username']['short'] = self::short($_, $auth, $username)) {
                        if ($check['username']['available'] = self::available($_, $auth, strtolower($username))) {
                            return $check;
                        } else {
                            $check['username']['error_msg'] = $authLoc['validate']['username']['available_error'];
                        }
                    } else {
                        $check['username']['error_msg'] = $authLoc['validate']['username']['short_error'];
                    }
                } else {
                    $check['username']['error_msg'] = $authLoc['validate']['username']['long_error'];
                }
            } else {
                $check['username']['error_msg'] = $authLoc['validate']['username']['syntax_error'];
            }
            return $check;
        }
        public static function syntax($_, $auth, $username)
        {
            if (preg_match($auth['validate_username']['regex'], $username)) {
                return false;
            } else {
                core::debug($_, '$username does not contain invalid characters.');
                return true;
            }
        }
        public static function long($_, $auth, $username)
        {
            if (strlen($username) > intval($auth['validate_username']['max_length'])) {
                return false;
            } else {
                core::debug($_, '$username is not too long.');
                return true;
            }
        }
        public static function short($_, $auth, $username)
        {
            if (strlen($username) < intval($auth['validate_username']['min_length'])) {
                return false;
            } else {
                core::debug($_, '$username is not too short.');
                return true;
            }
        }
        public static function available($_, $auth, $username)
        {
            if (
                db::rowExists(
                    $_,
                    "SELECT COUNT(*) FROM ".$_['table_prefix']."users WHERE UserNiceName=?",
                    $username
                )
            ) {
                core::debug($_, '$username is in use.');
                return false;
            } else { // if $username isn't already in use, we run a few checks on it
                core::debug($_, '$username is not in use.');
                return true;
            }
        }
    }
