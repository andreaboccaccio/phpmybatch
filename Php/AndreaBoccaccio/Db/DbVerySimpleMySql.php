<?php
/*
 * phpmywhs - An open source warehouse management software.
 * Copyright (C)2012 Andrea Boccaccio
 * contact email: andrea@andreaboccaccio.it
 * 
 * This file is part of phpmywhs.
 * 
 * phpmywhs is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * phpmywhs is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with phpmywhs. If not, see <http://www.gnu.org/licenses/>.
 * 
 */
class Php_AndreaBoccaccio_Db_DbVerySimpleMySql extends Php_AndreaBoccaccio_Db_DbAbstract {
	
	private $dbconn = null;
	private static $instance = null;
	
	private function __clone() {
	
	}
	
	private function __construct() {
		$this->setKind('mysql');
	}
	
	public static function getInstance() {
		if(self::$instance == null) {
			self::$instance = new Php_AndreaBoccaccio_Db_DbVerySimpleMySql();
		}
		return self::$instance;
	}
	
	private function connect() {
		$settingsFac = Php_AndreaBoccaccio_Settings_SettingsFactory::getInstance();
		$settings = $settingsFac->getSettings('xml');
		$server = $settings->getSettingFromFullName('db.server');
		$username = $settings->getSettingFromFullName('db.username');
		$password = $settings->getSettingFromFullName('db.password');
		$dbname = $settings->getSettingFromFullName('db.dbname');
		$this->dbconn =	mysql_connect($server,$username,$password);
		if(!$this->dbconn) {
			die('DB Connection Fail : ' . mysql_errno() . ':' . mysql_error());
		}
		mysql_select_db($dbname);
	}
	
	public function execQuery($strSQL) {
		$ret = array();
		$resQuery = FALSE;
		$qKind = -1;
		$nFieds = -1;
		$nRows = -1;
		$fieldsArray = array();
		$resultArray = array();
		$i = -1;
		
		$this->connect();
		//$strSQL = mysql_real_escape_string(trim($strSQL),$this->dbconn);
		$strSQL = trim($strSQL);
		if(substr_compare($strSQL, "SELECT", 0, strlen("SELECT"), TRUE) == 0) {
			$qKind = 1;
		}
		else if((substr_compare($strSQL, "DELETE", 0, strlen("DELETE"), TRUE) == 0)
				|| (substr_compare($strSQL, "INSERT", 0, strlen("INSERT"), TRUE) == 0)
				|| (substr_compare($strSQL, "REPLACE", 0, strlen("REPLACE"), TRUE) == 0)
				|| (substr_compare($strSQL, "UPDATE", 0, strlen("UPDATE"), TRUE) == 0)
				) {
			$qKind = 2;
		}
		else {
			$qKind = 3;
		}
		$resQuery = mysql_query($strSQL,$this->dbconn);
		if(!$resQuery) {
			$ret["qKind"] = $qKind;
			$ret["success"] = FALSE;
			$ret["errno"] = mysql_errno($this->dbconn);
			$ret["error"] = mysql_error($this->dbconn);
		}
		else {
			$ret["qKind"] = $qKind;
			$ret["success"] = TRUE;
			if($qKind == 1) {
				$nRows = mysql_num_rows($resQuery);
				$nFields = mysql_num_fields($resQuery);
				$ret["numrows"] = $nRows;
				$ret["numfields"] = $nFields;
				for ($i = 0; $i < $nFields; $i++) {
					$fieldsArray[$i] = mysql_field_name($resQuery, $i);
				}
				$ret["fields"] = $fieldsArray;
				for ($i = 0; $i < $nRows; $i++) {
					$resultArray[$i] = mysql_fetch_assoc($resQuery);
				}
				$ret["result"] = $resultArray;
			}
			else if($qKind == 2) {
				$ret["numrows"] = mysql_affected_rows($this->dbconn);
			}
		}
		
		return $ret;
	}
	
	public function closeConnection() {
		$ret = FALSE;
		if(mysql_ping($this->dbconn)) {
			$ret = mysql_close($this->dbconn);
		}
		else {
			$ret = TRUE;
		}
		return $ret;
	}
	
	public function sanitize($str) {
		$this->connect();
		return mysql_real_escape_string($str,$this->dbconn);
	}
}