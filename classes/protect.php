<?php
    class protect
    {
        public static function brute($_, $auth, $username)
        {
            // Return of true = brute force attempt may be in progress, block it. Return of false = no additional measures
            if (validate_username::available($_, $auth, $username)) {
                true;
            } else {
                $row = db::getRow(
                    $_,
                    'SELECT COUNT(*) FROM '.$_["table_prefix"].'users WHERE UserNiceName=?',
                    strtolower($username)
                );
            }
            return false;
        }
    }
