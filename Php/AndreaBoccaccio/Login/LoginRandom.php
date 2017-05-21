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
class Php_AndreaBoccaccio_Login_LoginRandom extends Php_AndreaBoccaccio_Login_LoginAbstract {
	
	private static $instance = null;
	
	private function __clone() {
	
	}
	
	private function __construct() {
		$this->setKind('random');
	}
	
	public static function getInstance() {
		if(self::$instance == null) {
			self::$instance = new Php_AndreaBoccaccio_Login_LoginRandom();
		}
		return self::$instance;
	}
	
	private function cleanUp() {
		$setting = Php_AndreaBoccaccio_Settings_SettingsFactory::getInstance()->getSettings('xml');
		$db = Php_AndreaBoccaccio_Db_DbFactory::getInstance()->getDb($setting->getSettingFromFullName('classes.db'));
		$strSQL = '';
		$res = array();
		
		$strSQL = "DELETE FROM SESSION WHERE ";
		$strSQL .= "(TIMESTAMPDIFF(SECOND,utcvt_end,UTC_TIMESTAMP())>0);";
		$res = $db->execQuery($strSQL);
	}
	
	private function createRandomCode($usrId) {
		$setting = Php_AndreaBoccaccio_Settings_SettingsFactory::getInstance()->getSettings('xml');
		$ret = hash($setting->getSettingFromFullName('login.hash'),strval(mt_rand()));
		
		return $ret;
	}
	
	private function newSess($usrId) {
		$ret = '';
		$setting = Php_AndreaBoccaccio_Settings_SettingsFactory::getInstance()->getSettings('xml');
		$db = Php_AndreaBoccaccio_Db_DbFactory::getInstance()->getDb($setting->getSettingFromFullName('classes.db'));
		$strSQL = '';
		$res = array();
		$already = TRUE;
		$newCode = $this->createRandomCode($usrId);
		
		while($already) {
			$strSQL = "SELECT * FROM SESSION WHERE (code='";
			$strSQL .= $newCode;
			$strSQL .= "');";
			$res = $db->execQuery($strSQL);
			if($res["success"] == TRUE) {
				if($res["numrows"] == 1) {
					$newCode = $this->createRandomCode($usrId);
				}
				else {
					$already = FALSE;
				}
			}
		}
		$strSQL = "INSERT INTO SESSION (code,utcvt_start,utcvt_end,user) ";
		$strSQL .= "VALUES ('";
		$strSQL .= $newCode;
		$strSQL .= "',UTC_TIMESTAMP(),TIMESTAMPADD(SECOND,";
		$strSQL .= $setting->getSettingFromFullName('session.persistence');
		$strSQL .= ",UTC_TIMESTAMP())," .$usrId . ");" ;
		$res = $db->execQuery($strSQL);
		if($res["success"] == TRUE) {
			if($res["numrows"] == 1) {
				$ret = $newCode;
			}
			else {
				
				$ret = '';
			}
		}
		return $ret;
	}
	
	private function checkSession($code) {
		$ret = '';
		$setting = Php_AndreaBoccaccio_Settings_SettingsFactory::getInstance()->getSettings('xml');
		$db = Php_AndreaBoccaccio_Db_DbFactory::getInstance()->getDb($setting->getSettingFromFullName('classes.db'));
		$strSQL = '';
		$res = array();
		$already = TRUE;
		$usrId = -1;
		$sessionId = -1;
		$newCode;
		
		$this->cleanUp();
		
		$strSQL = "SELECT S.id AS sid, U.id AS uid FROM USER AS U INNER JOIN SESSION AS S"; 
		$strSQL .= " ON (U.id = S.user) WHERE (S.code='";
		$strSQL .= $code;
		$strSQL .= "');";
		$res = $db->execQuery($strSQL);
		if($res["success"] == TRUE) {
			if($res["numrows"] == 1) {
				$usrId = intval($res["result"][0]["uid"]);
				$sessionId = intval($res["result"][0]["sid"]);
				$newCode = $this->createRandomCode($usrId);
				
				while($already) {
					$strSQL = "SELECT * FROM SESSION WHERE (code='";
					$strSQL .= $newCode;
					$strSQL .= "');";
					$res = $db->execQuery($strSQL);
					if($res["success"] == TRUE) {
						if($res["numrows"] == 1) {
							$newCode = $this->createRandomCode($usrId);
						}
						else {
							$already = FALSE;
							$strSQL = "UPDATE SESSION SET code ='";
							$strSQL .= $newCode;
							$strSQL .= "' WHERE (id=";
							$strSQL .= $sessionId;
							$strSQL .= ");";
							$res = $db->execQuery($strSQL);
							$ret = $newCode;
						}
					}
				}
			}
			else {
				$ret = '';
			}
		}
		else {
			$ret = '';
		}
		return $ret;
	}
	
	private function checkUsrPwd($usr, $pwd) {
		$ret = '';
		$setting = Php_AndreaBoccaccio_Settings_SettingsFactory::getInstance()->getSettings('xml');
		$db = Php_AndreaBoccaccio_Db_DbFactory::getInstance()->getDb($setting->getSettingFromFullName('classes.db'));
		$strSQL = '';
		$res = array();
		
		$strSQL = "SELECT * FROM USER WHERE ((name='";
		$strSQL .= $usr;
		$strSQL .= "')";
		$strSQL .= "AND(pwd='";
		$strSQL .= $pwd;
		$strSQL .= "')";
		$strSQL .= ");";
		$res = $db->execQuery($strSQL);
		if($res["success"] == TRUE) {
			if($res["numrows"] == 1) {
				$this->cleanUp();
				$ret = $this->newSess(intval($res["result"][0]["id"]));
				
			}
			else {
				$ret = '';
			}
		}
		else {
			$ret = '';
		}
		return $ret;
	}
	
	public function getNewSessionCode($usr, $pwd, $code) {
		$ret = '';
		$tmpUsr = '';
		$tmpPwd = '';
		$setting = Php_AndreaBoccaccio_Settings_SettingsFactory::getInstance()->getSettings('xml');
		$strSQL = '';
		
		if(($code == null)&&($usr != null)&&($pwd != null)) {
			$tmpUsr = trim($usr);
			$tmpPwd = trim($pwd);
			if((strlen($tmpUsr) > 0)&&(strlen($tmpPwd)>0)) {
				$tmpPwd = hash($setting->getSettingFromFullName('login.hash'),$tmpPwd);
				$ret = $this->checkUsrPwd($tmpUsr, $tmpPwd);
			}
			else {
				$ret = '';
			}
		}
		else if(($code != null)&&($usr == null)&&($pwd == null)) {
			$ret = $this->checkSession($code);
		}
		else {
			$ret = '';
		}
		return $ret;
	}
	
	public function getUserLevel($code) {
		
		$ret = -1;
		$setting = Php_AndreaBoccaccio_Settings_SettingsFactory::getInstance()->getSettings('xml');
		$db = Php_AndreaBoccaccio_Db_DbFactory::getInstance()->getDb($setting->getSettingFromFullName('classes.db'));
		$strSQL = '';
		
		if($code != null) {
			$strSQL = "SELECT U.level FROM USER AS U INNER JOIN SESSION AS S";
			$strSQL .= " ON (U.id = S.user) WHERE (S.code='";
			$strSQL .= $code;
			$strSQL .= "');";
			$res = $db->execQuery($strSQL);
			if($res["success"] == TRUE) {
				if($res["numrows"] == 1) {
					$ret = intval($res["result"][0]["level"]);
				}
				else {
					$ret = -1;
				}
			}
			else {
				$ret = -1;
			}
		}
		else {
			$ret = -1;
		}
		
		return $ret;
	}
	
	public function logout($code) {
		$setting = Php_AndreaBoccaccio_Settings_SettingsFactory::getInstance()->getSettings('xml');
		$db = Php_AndreaBoccaccio_Db_DbFactory::getInstance()->getDb($setting->getSettingFromFullName('classes.db'));
		$strSQL = '';
		
		if($code != null) {
			$strSQL = "DELETE FROM SESSION ";
			$strSQL .= " WHERE (code='";
			$strSQL .= $code;
			$strSQL .= "');";
			$res = $db->execQuery($strSQL);
		}
	}
}