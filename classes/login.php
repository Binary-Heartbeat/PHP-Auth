<?php
    class login
    {
        public static function invoke($_, $auth)
        {
            // We're gonna just assume things are false until something says otherwise
            if (isset($_POST['trigger_login']) and $_POST['trigger_login']) {
                $check['trigger'] = true;
            } else {
                $check['trigger'] = false;
            }
            $check['username']['valid'] = false;
            $check['password']['valid'] = false;
            $check['captcha']['force']  = false;

            if ($check['trigger']) {
                // dump login form variables from post
                $usernicename = strtolower($_POST['username']);
                $password = $_POST['password'];

                if (
                    $row = db::selectRow(
                        $_,
                        "SELECT * FROM ".$_['table_prefix']."users WHERE UserNiceName=?",
                        $usernicename
                    )
                ) {
                    $check['username']['valid'] = true;
                    if (auth::checkPass($_, $auth, $password, $row['UserPassword'])) {
                        echo 'SUCCESS';
                        /*
                        $token=auth::makeToken($_);
                        // TODO: add UserLastLogin to database and update the value
                        db::query($_,
                                    "UPDATE ".$_['table_prefix']."users SET UserToken=?, UserIP=? WHERE UserID=?",
                                    array($token,$_SERVER['REMOTE_ADDR'],$row['UserID'])
                                    );
                        setcookie($_['cookie_name'], $row['UserName'], '+'.$_['cookie_expiry']);
                        setcookie($_['cookie_name'].'_t', $token, '+'.$_['cookie_expiry']);
                        */

                        $check['password']['valid'] = true;
                        $check['success'] = true;
                    }
                } else {
                    $check['username'] = false;
                }
                // TODO: check for number of failed attempts, force captcha if >= 2 - $check['force_captcha'] = true;
            }
            return $check;
        }
    }
