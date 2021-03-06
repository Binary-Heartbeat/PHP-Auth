<?php
    class validate_password
    {
        public static function all($_, $auth, $authLoc, $password, $password_confirm, $check)
        {
            if ($check['password']['match'] = self::match($_, $auth, $password, $password_confirm)) {
                if ($check['password']['long'] = self::long($_, $auth, $password)) {
                    if ($check['password']['short'] = self::short($_, $auth, $password)) {
                        if ($check['password']['strength'] = self::strength($_, $auth, $password)) {
                            return $check;
                        } else {
                            $check['password']['error_msg'] = $authLoc['validate']['password']['strength_error'];
                        }
                    } else {
                        $check['password']['error_msg'] = $authLoc['validate']['password']['short_error'];
                    }
                } else {
                    $check['password']['error_msg'] = $authLoc['validate']['password']['long_error'];
                }
            } else {
                $check['password']['error_msg'] = $authLoc['validate']['password']['match_error'];
            }
            return $check;
        }
        public static function match ($_, $auth, $password, $password_confirm)
        {
            if ($password === $password_confirm) { // check if $password and $password_confirm are EXACT matches
                core::debug($_, '$password and $password_confirm are exact matches.');
                return true;
            } else {
                return false;
            }
        }
        public static function long ($_, $auth, $password)
        {
            if (strlen($password) > intval($auth['validate_password']['max_length'])) {
                return false;
            } else {
                core::debug($_, '$password is not too long.');
                return true;
            }
        }
        public static function short ($_, $auth, $password)
        {
            if (strlen($password) < intval($auth['validate_password']['min_length'])) {
                return false;
            } else {
                core::debug($_, '$password is not too short.');
                return true;
            }
        }
        public static function strength ($_, $auth, $password)
        {
            $password_strength=0;

            $password_strength_letters='/[a-zA-Z]/'; // As silly as it seems, lets define the alphabet
            if (preg_match($password_strength_letters, $password)) { // check if $password contains letters
                $password_strength++;
                core::debug($_, '$password contains letters.');
            } else {
                core::debug($_, '$password does not contain letters.');
            }
            $password_strength_numbers='/[0123456789]/'; // Define numbers...
            if (preg_match($password_strength_numbers, $password)) { // check if $password contains numbers
                core::debug($_, '$password contains numbers.');
                $password_strength++;
            } else {
                core::debug($_, '$password does not contain numbers.');
            }
            $password_strength_symbols="/[`~!@#$%^&*()\-_=+\[{\]};:\\|,<.>\/?]/"; // And lastly symbols
            if (preg_match($password_strength_symbols, $password)) { // check if $password contains symbols
                core::debug($_, '$password contains symbols.');
                $password_strength++;
            } else {
                core::debug($_, '$password does not contain symbols.');
            }

            core::debug($_, '$password_strength = '.$password_strength);

            if ($password_strength < intval($auth['validate_password']['strength'])) { // Make sure that the password contains at least two types of characters
                return false;
            } else {
                core::debug($_, '$password contains characters from two, or all, of the following: letters, numbers, symbols.');
                return true;
            }
        }
    }
