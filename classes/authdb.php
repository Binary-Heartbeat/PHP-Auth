<?php
	class authdb {
		public static function connect($auth) {
			$auth['con'] = 'mysql:host='.$auth['db_host'].';dbname='.$auth['db_name'].';';
			try {
				$con = new PDO($auth['con'],$auth['db_user'],$auth['db_pass']); // mysql
			} catch(PDOException $e) {
				die ('Could not connect to database.'); // Exit, displaying an error message
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
