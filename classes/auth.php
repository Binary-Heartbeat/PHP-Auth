<?php
    // http://net.tutsplus.com/tutorials/php/understanding-hash-functions-and-keeping-passwords-safe/
    class auth
    {
        private static function SHA512($auth)
        {
            if (defined("CRYPT_SHA512") and CRYPT_SHA512) {
                if (!isset($auth['hash']['preference'])) {
                    return true;
                } elseif ($auth['hash']['preference']='SHA512') {
                    return true;
                }
            }
            return false;
        }

        private static function SHA256($auth)
        {
            if (defined("CRYPT_SHA256") and CRYPT_SHA256) {
                if (!$auth['hash']['preference']) {
                    return true;
                } elseif ($auth['hash']['preference']='SHA256') {
                    return true;
                }
            }
            return false;
        }

        private static function BLOWFISH($auth)
        {
            if (defined("CRYPT_BLOWFISH") and BLOWFISH) {
                if (!$auth['hash']['preference']) {
                    return true;
                } elseif ($auth['hash']['preference']='BLOWFISH') {
                    return true;
                }
            }
            return false;
        }

        public static function hashPass($_, $auth, $password) // this will be used to generate a hash
        {
            if (self::SHA512($auth)) {
                core::debug($_, 'Using CRYPT_SHA512 (6).');
                $algo = '6';
                if(isset($auth['hash']['preference'])) {
                    $cost = $auth['hash']['cost'];
                } else {
                    $cost = 'rounds=5000';
                }
            } elseif (self::SHA256($auth)) {
                core::debug($_, 'Using CRYPT_SHA256 (5).');
                $algo = '5';
                if (isset($auth['hash']['preference'])) {
                    $cost=$auth['hash']['cost'];
                } else {
                    $cost='rounds=5000';
                }
            } elseif (self::BLOWFISH($auth)) {
                if (phpversion() < '5.3.7'){
                    core::debug($_, 'Using CRYPT_BLOWFISH (2a).');
                    $algo = '2a';
                    if (isset($auth['hash']['preference'])) { $cost=$auth['hash']['cost']; } else { $cost='10'; }
                } else {
                    core::debug($_, 'Using CRYPT_BLOWFISH (2y).');
                    $algo = '2y';
                    if (isset($auth['hash']['preference'])) {
                        $cost=$auth['hash']['cost'];
                    } else {
                        $cost='10';
                    }
                }
            } else {
                if (!core::debug(
                    $_,
                    'Binary Heartbeat\'s Auth system uses SHA512, SHA256, or blowfish for password encryption. None of these have been found in the local PHP installation. Arcfolder will now terminate.')
                ) {
                    echo '<br/>Fatal error: A critical error within this Arcfolder installation has been detected and Arcfolder has terminated. Please inform an administrator at <a href=mailto:"'.$auth['admin_email'].'" >'.$auth['admin_email'].'</a>.'.PHP_EOL;
                    die();
                }
                die();
            }
            $salt = '$'.$algo.'$'.$cost.'$'.$auth['salt'].'$';
            return crypt($password, $salt);
        }

        public static function checkPass($_, $auth, $password, $hash) // compare a password against a hash
        {
            return (auth::hashPass($_, $auth, $password) == $hash);
        }

        public static function makeKey() // Multi-purpose random 'key' generator. Used for: account activation strings
        {
            return md5(rand(1000000,9999999)).md5(rand(1000000,9999999));
        }

        public static function verify() // TODO: verify if the current login is valid
        {
            ;
        }

        public static function deny() // TODO: check if login is valid, deny if not
        {
            ;
        }

        public static function getCaptcha($auth, $check, $page)
        {
            if ($auth['recaptcha']['enable']) {
                if (
                    $check['captcha']['force'] or

                    in_array(
                        $page['action'], array(
                            'register',
                            'activate',
                            'recover'
                        )
                    ) /*or
                       *
                    in_array(
                        $page['page'], array(
                            ''
                        )
                    )*/
                ) {

                /*if ($page['action'] == 'login' and $check['captcha']['force']) {
                    return self::buildCaptcha($check, $auth);
                } elseif ($page['action'] !== 'login') { */

                    return self::buildCaptcha($check, $auth);
                } else {
                    return false;
                }
            }
        }

        private static function buildCaptcha($check, $auth)
        {
            if (isset($check['captcha']['resp'])) {
                return recaptcha_get_html($auth['recaptcha']['public_key'], $check['captcha']['resp']->error);
            } else {
                return recaptcha_get_html($auth['recaptcha']['public_key']);
            }
        }
    }
