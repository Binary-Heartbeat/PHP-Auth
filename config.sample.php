<?php
// The contents of this file needs to be copied into the main config for the parent application, or into its database

// Auth-specific config. This is independent from the application this is being paired with. In most cases the default values are suitable.
    // reCAPTCHA
        $auth['recaptcha']['enable']      = true;
        $auth['recaptcha']['public_key']  = '';
        $auth['recaptcha']['private_key'] = '';
    // Password
        $auth['validate_password']['min_length'] = '8'; // Min length. Numerical values only.
        $auth['validate_password']['max_length'] = '64'; // Max length. Numerical values only.
        // From the character categories letters, numbers, and symbols, how many types have to be used when creating a password
        // Numerical values ranging from 1 to 3, inclusive, only
        $auth['validate_password']['strength']   = '2';

    // Username
        $auth['validate_username']['min_length'] = '3'; // Min length. Numerical values only.
        $auth['validate_username']['max_length'] = '20'; // Max length. Numerical values only.
        $auth['validate_username']['regex']      = '/[^a-zA-Z0123456789\-_]/'; // Regex for valid characters.

    // Password hashing
        // $auth['hash']['preference']='SHA512'; // Uncomment to use. Comment to deactivate. Can be 'SHA512', 'SHA256', or 'BLOWFISH'

        // default for SHA512 and SHA256 is 'rounds=5000'
        // default for BLOWFISH is '10'
        // refer to http://php.net/manual/en/function.crypt.php if you are unsure about this
        $auth['hash']['cost'] = 'rounds=5000';
