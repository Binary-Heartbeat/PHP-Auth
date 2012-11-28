<?php

// References
	// http://net.tutsplus.com/tutorials/php/understanding-hash-functions-and-keeping-passwords-safe/
	// http://stackoverflow.com/questions/5032341/where-is-the-best-place-to-store-the-password-salt-for-the-website

	class register {
		public static function validate($auth, $authLoc, $username, $password, $password_confirm,$email, $email_confirm) {
			$validate_username=validate_username::all($auth, $authLoc, $username);
			$validate_password=validate_password::all($auth, $authLoc, $password, $password_confirm);
			$validate_email=validate_email::all($auth, $authLoc, $email, $email_confirm);
			if($validate_username and $validate_password and $validate_email)
			{ self::write($auth,$username,auth::hashPass($auth, $password),$_SERVER['REMOTE_ADDR'],$email); return true; }
			return false;
		}
		private static function write($auth,$username,$hash,$ip,$email) {
				authdb::query(
					$auth,
					"INSERT INTO ".$auth['table_prefix']."users(UserName,UserNiceName,UserPassword,UserEmail,UserIP) VALUES(?,?,?,?,?);",
					array($username,strtolower($username),$hash,$email,$ip)
				);
		}
	}
	class validate_username {
		public static function all($auth, $authLoc, $username) {
			if(self::characters($auth, $authLoc, $username)) {
				if(self::available($auth, $authLoc, $username)) {
					if(self::length($auth, $authLoc, $username)) {
						return true;
					}
				}
			}
			return false;
		}
		public static function characters($auth, $authLoc, $username) {
			if(preg_match($auth['validate_username']['regex'], $username)) {
				echo '<br/>'.$authLoc['reg_err_username_invalid'].PHP_EOL;
				return false;
			} else {
				_debug($auth, '$username only contains valid characters.');
				return true;
			}
		}
		public static function available($auth, $authLoc, $username) {
			if(authdb::rowExists($auth, 'users', 'UserNiceName', $username)) { // check if $username already exists in the database
				echo '<br/>'.$authLoc['reg_err_username_taken'].PHP_EOL;
				return false;
			} else { // if $username isn't already in use, we run a few checks on it
				_debug($auth, '$username is not in use.');
				return true;
			}
		}
		public static function length($auth, $authLoc, $username) {
			if(strlen($username) > intval($auth['validate_username']['max_length'])) {
				echo '<br/>'.$authLoc['reg_err_username_long'].PHP_EOL;
				return false;
			} elseif(strlen($username) < intval($auth['validate_username']['min_length'])) {
				echo '<br/>'.$authLoc['reg_err_username_short'].PHP_EOL;
				return false;
			} else {
				_debug($auth, '$username is an acceptable length');
				return true;
			}
		}
	}
	class validate_password {
		public static function all($auth, $authLoc, $password, $password_confirm) {
			if(self::match($auth, $authLoc, $password, $password_confirm)) {
				if(self::length($auth, $authLoc, $password)) {
					if(self::strength($auth, $authLoc, $password)) {
						return true;
					}
				}
			}
			return false;
		}
		public static function match ($auth, $authLoc, $password, $password_confirm) {
			if($password === $password_confirm) { // check if $password and $password_confirm are EXACT matches
				_debug($auth, '$password and $password_confirm are exact matches.');
				return true;
			} else {
				echo '<br/>'.$authLoc['validate_password_match_error'].PHP_EOL;
				return false;
			}
		}
		public static function length ($auth, $authLoc, $password) {
			if(strlen($password) < intval($auth['validate_password']['min_length'])) { // Hmm, better make sure the password is an acceptable length
				echo '<br/>'.$authLoc['validate_password_length_short_error'].PHP_EOL;
				return false;
			} elseif (strlen($password) > intval($auth['validate_password']['max_length'])) { // Lets make sure they don't exceed the length set for the database
				echo '<br/>'.$authLoc['validate_password_length_long_error'].PHP_EOL;
				return false;
			} else {
				_debug($auth, '$password is a valid length.');
				return true;
			}
		}
		public static function strength ($auth, $authLoc, $password) {
			$password_strength=0;

			$password_strength_letters='/[a-zA-Z]/'; // As silly as it seems, lets define the alphabet
			if(preg_match($password_strength_letters, $password)) { // check if $password contains letters
				$password_strength++;
				_debug($auth, 'Letters present in $password.');
			} else {
				_debug($auth, 'Letters not present in $password.');
			}
			$password_strength_numbers='/[0123456789]/'; // Define numbers...
			if(preg_match($password_strength_numbers, $password)) { // check if $password contains numbers
				_debug($auth, 'Numbers present in $password.');
				$password_strength++;
			} else {
				_debug($auth, 'Numbers not present in $password.');
			}
			$password_strength_symbols="/[`~!@#$%^&*()-_=+\[{\]};:\\|,<.>\/?]/"; // And lastly symbols
			if(preg_match($password_strength_symbols, $password)) { // check if $password contains symbols
				_debug($auth, 'Symbols present in $password.');
				$password_strength++;
			} else {
				_debug($auth, 'Symbols not present in $password.');
			}

			_debug($auth, '$password_strength = '.$password_strength);

			if($password_strength < intval($auth['validate_password']['strength'])) { // Make sure that the password contains at least two types of characters
				echo '<br/>'.$authLoc['validate_password_strength_error'].PHP_EOL;
				return false;
			} else {
				_debug($auth, '$password contains characters from two, or all, of the following: letters, numbers, symbols.');
				return true;
			}
		}
	}
	class validate_email {
		public static function all ($auth, $authLoc, $email, $email_confirm) {
			if(self::match($auth, $authLoc, $email, $email_confirm)) {
				if(self::syntax($auth, $authLoc, $email)) {
					if(self::available($auth, $authLoc, $email)) {
						return true;
					}
				}
			}
			return false;
		}
		public static function match ($auth, $authLoc, $email, $email_confirm) {
			if($email === $email_confirm) { // check if $email and $email_confirm are EXACT matches
				_debug($auth, '$email and $email_confirm are exact matches.');
				return true;
			} else {
				echo '<br/>'.$authLoc['validate_email_match_error'].PHP_EOL;
				return false;
			}
		}
		public static function syntax ($auth, $authLoc, $email) {
			if(filter_var($email, FILTER_VALIDATE_EMAIL)) { // Hmm, better make sure the email address is in a valid form
				_debug($auth, '$email is valid syntax.');
				return true;
			} else {
				echo '<br/>'.$authLoc['validate_email_syntax_error'].PHP_EOL;
				return false;
			}
		}
		public static function available ($auth, $authLoc, $email) {
			if(authdb::rowExists($auth, 'users', 'UserEmail', $email)) { // check if $username already exists in the database
				echo '<br/>'.$authLoc['validate_email_available_error'].PHP_EOL;
				return false;
			} else {
				_debug($auth, '$email is available.');
				return true;
			}
		}
	}
	class auth {
		public static function hashPass($auth, $password) { // this will be used to generate a hash
			if((defined("CRYPT_SHA512") && CRYPT_SHA512) and (!$auth['hash']['user_defined'] or ($auth['hash']['user_defined'] && $auth['hash']['SHA512']))) {
				_debug($auth, 'Using CRYPT_SHA512 (6).');
				$algo = '6';
				if($auth['hash']['user_defined']) { $cost=$auth['hash']['cost']; } else { $cost='rounds=5000'; }
			} elseif((defined("CRYPT_SHA256") && CRYPT_SHA256) and (!$auth['hash']['user_defined'] or ($auth['hash']['user_defined'] && $auth['hash']['SHA256']))) {
				_debug($auth, 'Using CRYPT_SHA256 (5).');
				$algo = '5';
				if($auth['hash']['user_defined']) { $cost=$auth['hash']['cost']; } else { $cost='rounds=5000'; }
			} elseif((defined("CRYPT_BLOWFISH") && CRYPT_BLOWFISH) and (!$auth['hash']['user_defined'] or ($auth['hash']['user_defined'] && $auth['hash']['BLOWFISH']))) {
				if(phpversion() < '5.3.7'){
					_debug($auth, 'Using CRYPT_BLOWFISH (2a).');
					$algo = '2a';
					if($auth['hash']['user_defined']) { $cost=$auth['hash']['cost']; } else { $cost='10'; }
				} else {
					_debug($auth, 'Using CRYPT_BLOWFISH (2y).');
					$algo = '2y';
					if($auth['hash']['user_defined']) { $cost=$auth['hash']['cost']; } else { $cost='10'; }
				}
			} else {
				if(! _debug($auth, 'Binary Heartbeat\'s Auth system uses SHA512, SHA256, or blowfish for password encryption. None of these have been found in the local PHP installation. Arcfolder will now terminate.')) {
					echo '<br/>Fatal error: A critical error within this Arcfolder installation has been detected and Arcfolder has terminated. Please inform an administrator at <a href=mailto:"'.$auth['admin_email'].'" >'.$auth['admin_email'].'</a>.'.PHP_EOL;
					die();
				}
				die();
			}
			$salt = '$'.$algo.'$'.$cost.'$'.$auth['salt'].'$';
			return crypt($password, $salt);
		}
		public static function checkPass($auth,$authLoc,$password,$hash) { // compare a password against a hash
			return (auth::hashPass($auth,$password) == $hash);
		}
		public static function makeToken($auth) {
			return auth::hash($auth,md5(rand(1000000,9999999)),md5(rand(1000000,9999999)));
		}
	}
	class authdb {
		public static function connect($auth) {
			$auth['con'] = 'mysql:host='.$auth['db_host'].';dbname='.$auth['db_name'].';';
			try {
				$con = new PDO($auth['con'],$auth['db_user'],$auth['db_pass']); // mysql
			} catch(PDOException $e) {
				die ('Oops'); // Exit, displaying an error message
			}
			return $con;
		}
		public static function query($auth,$query,$values) {
			$statement = self::connect($auth)->prepare($query);
			$statement->execute($values);
			self::close($statement);
		}
		public static function close($statement) {
			return $statement->closeCursor();
		}
		public static function getRow($auth, $table, $column, $value) {
			$con = self::connect($auth);
			$query = "SELECT * FROM ".$auth['table_prefix'].$table." WHERE `".$column."` = '".$value."';";
			//$statement = $con->prepare($query);
			//$statement->execute();
			$statement = $con->query($query);
			$row = $statement->fetch();
			authdb::close($statement);
			return $row;
		}
		public static function rowExists($auth, $table, $column, $value) {
			$con = self::connect($auth);
			$query = "SELECT COUNT(*) FROM ".$auth['table_prefix'].$table." WHERE `".$column."` = '".$value."';";
			$statement = $con->prepare($query);
			$statement->execute();
			$count = $statement->fetchColumn(); // investigate switching to rowCount instead of fetchColumn
			self::close($statement);
			if ($count !== '1') {
				return FALSE;
			} else {
				return TRUE;
			}
		}
		public static function updateRow($auth, $table, $column, $value, $findColumn, $findValue) {
			$query = "UPDATE ".$auth['table_prefix'].$table." SET ".$column."='".$value."' WHERE `".$findColumn."` = '".$findValue."';";

			$statement = self::connect($auth)->prepare($query);
			$statement->execute();
			self::close($statement);
		}
	}
