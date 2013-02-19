<?php
    class register
    {
        public static function invoke($_, $auth, $authLoc)
        {
            // We're gonna just assume a lot of things are false until something says otherwise
            if (isset($_POST['trigger_register']) and $_POST['trigger_register']) {
                $check['trigger'] = true;
            } else {
                $check['trigger'] = false;
            }
            $check['username']['valid']     = false;
            $check['username']['chars']     = false;
            $check['username']['long']      = false;
            $check['username']['short']     = false;
            $check['username']['available'] = false;

            $check['password']['valid']     = false;
            $check['password']['match']     = false;
            $check['password']['long']      = false;
            $check['password']['short']     = false;
            $check['password']['strength']  = false;

            $check['email']['valid']        = false;
            $check['email']['match']        = false;
            $check['email']['syntax']       = false;
            $check['email']['available']    = false;

            $check['captcha']['force']      = false;

            if ($check['trigger']) {
                if ($auth['recaptcha']['enable']) {
                    core::debug($_, 'Captcha is enabled');
                    $check['captcha']['resp'] = recaptcha_check_answer(
                        $auth['recaptcha']['private_key'],
                        $_SERVER["REMOTE_ADDR"],
                        $_POST["recaptcha_challenge_field"],
                        $_POST["recaptcha_response_field"]
                    );
                    if ($check['captcha']['resp']->is_valid) {
                        core::debug($_, 'Captcha is valid');
                        $check['captcha']['valid'] = true;
                    } else {
                        core::debug($_, 'Captcha is not valid');
                        $check['captcha']['valid'] = false;
                    }
                } else {
                    core::debug($_, 'Captcha is disabled');
                    $check['captcha']['valid'] = true;
                }

                $check = self::validate(
                    $_,
                    $auth,
                    $authLoc,
                    $_POST['username'],
                    $_POST['password'],
                    $_POST['password_confirm'],
                    $_POST['email'],
                    $_POST['email_confirm'],
                    $check
                );

                if ($check['valid'] and $check['captcha']['valid']) {
                    if (register::write(
                        $_,
                        $_POST['username'],
                        auth::hashPass($_, $auth, $_POST['password']),
                        $_SERVER['REMOTE_ADDR'],
                        $_POST['email']
                        )
                    ) {
                        core::log($_,$_POST['username'],1,0);
                        if ($_['alert_registration']) {
                            $message = "New member has just registered: $_POST[username]";
                            mail::send(
                                $_['admin_email'], // TODO: pull proper recipient email from settings.
                                'New user registration',
                                $_POST['username'].' has registered.',
                                $_['admin_email']
                            );
                        }
                        $check['register']['valid'] = true;
                    } else {
                        echo 'Error 482: Somebody shot the server with a 12-gauge. Please contact the system administrator. Serious note: Something went wrong, drop a line by '.$_['admin_email'];
                    }
                }
            }
            return $check;
        }
        public static function validate($_, $auth, $authLoc, $username, $password, $password_confirm,$email, $email_confirm, $reg)
        {
            $reg = validate_username::all($_, $auth, $authLoc, $username, $reg);
            if (
                $reg['username']['chars'] and
                $reg['username']['long'] and
                $reg['username']['short'] and
                $reg['username']['available']
            ) {
                $reg['username']['valid'] = true;
            }

            $reg = validate_password::all($_, $auth, $authLoc, $password, $password_confirm, $reg);
            if (
                $reg['password']['match'] and
                $reg['password']['long'] and
                $reg['password']['short'] and
                $reg['password']['strength']
            ) {
                $reg['password']['valid'] = true;
            }

            $reg = validate_email::all($_, $authLoc, $email, $email_confirm, $reg);
            if (
                $reg['email']['match'] and
                $reg['email']['syntax'] and
                $reg['email']['available']
            ) {
                $reg['email']['valid'] = true;
            }

            if (
                $reg['username']['valid'] and
                $reg['password']['valid'] and
                $reg['email']['valid']
            ) {
                $reg['valid'] = true;
                return $reg;
            }
            $reg['valid'] = false;
            return $reg;
        }
        public static function write($_,$username,$hash,$ip,$email)
        {
            $query['fields'] = 'UserName,UserNiceName,UserEmail,UserPassword,UserIP,UserGroup';
            $query['values'] = '?,?,?,?,?,?';
            $values          = array($username,strtolower($username),$email,$hash,$ip,$_['default_group']);

            if ($_['activation_method'] !== '0') {
                $query['fields'] = $query['fields'].',UserActive';
                $query['values'] = $query['values'].',?';
                array_push($values, '0');

                if ($_['activation_method'] == '1') {
                    $query['fields'] = $query['fields'].',UserActivationKey';
                    $query['values'] = $query['values'].',?';
                    array_push($values, auth::makeKey());
                }
            } else {
                $query['fields'] = $query['fields'].',UserActive';
                $query['values'] = $query['values'].',?';
                array_push($values, '1');
            }
            if (db::insertRow(
                $_,
                "INSERT INTO ".$_['table_prefix']."users(".$query['fields'].") VALUES(".$query['values'].");",
                $values
            )
            ) {
                return true;
            } else {
                return false;
            }
        }
/*        public static function error($auth, $authLoc, $check, $type)
        {
            if ($check['trigger']) {

                if ($type = 'username') {
                    if (!$reg['username']['valid']) {
                        if (!$reg['username']['chars']) {
                            return $authLoc['validate']['username']['invalid'];
                        } elseif (!$reg['username']['long']) {
                            return $authLoc['validate']['username']['long'].' ('.$auth['validate_username']['max_length'].' '.$authLoc['char_max'].')';
                        } elseif (!$reg['username']['short']) {
                            return $authLoc['validate']['username']['short'].' ('.$auth['validate_username']['min_length'].' '.$authLoc['char_min'].')';
                        } elseif (!$reg['username']['available']) {
                            return $authLoc['validate']['username']['taken'];
                        } else {
                            echo 'Something broke when retrieving the error message for username validity...';
                        }
                    }
                    return false;
                }

                if () {
                    if (!$reg['password']['valid']) {
                        if (!$reg['password']['match']) {
                            return $authLoc['validate']['password']['match_error'];
                        }
                        elseif (!$reg['password']['long']) {
                            return $authLoc['validate']['password']['length_long_error'].' ('.$auth['validate_password']['max_length'].' '.$authLoc['char_max'].')';
                        }
                        elseif (!$reg['password']['short']) {
                            return $authLoc['validate']['password']['length_short_error'].
                            ' ('.$auth['validate_password']['min_length'].' '.$authLoc['char_min'].')'.
                            PHP_EOL;
                        }
                        elseif (!$reg['password']['strength']) {
                            echo '<br/>'.$authLoc['validate']['password']['strength_error'].PHP_EOL;
                        }
                    }
                }

                if () {
                    if (!$reg['email']['valid']) {
                        if (!$reg['email']['match']) {
                            echo '<br/>'.$authLoc['validate']['email']['match_error'].PHP_EOL;
                        }
                        elseif (!$reg['email']['syntax']) {
                            echo '<br/>'.$authLoc['validate']['email']['syntax_error'].PHP_EOL;
                        }
                        elseif (!$reg['email']['available']) {
                            echo '<br/>'.$authLoc['validate']['email']['available_error'].PHP_EOL;
                        }
                    }
                }
            }
        }*/
    }
