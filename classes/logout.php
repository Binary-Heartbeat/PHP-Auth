<?php
    class logout
    {
        public static function invoke($_)
        {
            if (isset($_SESSION['user']['valid']) and $_SESSION['user']['valid'])
            {
                session_destroy();
                $check['logout']['valid'] = true;
            } else {
                $check['logout']['valid'] = false;
            }
            return $check;
        }
    }
/*
if (jf_logout($_))
echo <<< EOT
    Logout successful.<br/>
    You will be automatically redirected to the home page in 5 seconds.
    <script language="JavaScript">
        setTimeout('pageRedirect("$_[web_root]")', 5000)
    </script>
EOT;
null;
*/
