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
class Php_AndreaBoccaccio_Model_DocumentKind {
	
	private $id = -1;
	private $code = '';
	private $name = '';
	private $description = '';
	private $changed = FALSE;
	
	public function __construct() {
		$this->changed = FALSE;
	}
	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		if($this->id != $id) {
			$this->id = $id;
			$this->changed = TRUE;
		}
	}
	public function getCode() {
		return $this->code;
	}
	public function setCode($code) {
		if($this->code != $code) {
			$this->code = $code;
			$this->changed = TRUE;
		}
	}
	public function getName() {
		return $this->name;
	}
	public function setName($name) {
		if($this->name != $name) {
			$this->name = $name;
			$this->changed = TRUE;
		}
	}
	public function getDescription() {
		return $this->description;
	}
	public function setDescription($description) {
		if($this->description != $description) {
			$this->description = $description;
			$this->changed = TRUE;
		}
	}
	public function init($id, $code, $name, $description) {
		if($this->id != $id) {
			$this->id = $id;
			$this->changed = TRUE;
		}
		if($this->code != $code) {
			$this->code = $code;
			$this->changed = TRUE;
		}
		if($this->name != $name) {
			$this->name = $name;
			$this->changed = TRUE;
		}
		if($this->description != $description) {
			$this->description = $description;
			$this->changed = TRUE;
		}
	}
	public function loadFromDbById($id) {
		$ret = -1;
		$setting;
		$db;
		$res = array();
		$tmpRow = array();
		$strSQL = '';
		
		if($id > 0) {
			$setting = Php_AndreaBoccaccio_Settings_SettingsFactory::getInstance()->getSettings('xml');
			$db = Php_AndreaBoccaccio_Db_DbFactory::getInstance()->getDb($setting->getSettingFromFullName('classes.db'));
			$strSQL = "SELECT * FROM DOCUMENT_KIND WHERE (id=";
			$strSQL .= $id;
			$strSQL .= ");";
			$res = $db->execQuery($strSQL);
			if($res["success"] == TRUE) {
				$ret = $res["numrows"];
				if($ret == 1) {
					$tmpRow = $res["result"][0];
					$this->init(intval($tmpRow["id"]),$tmpRow["code"],$tmpRow["name"],$tmpRow["description"]);
				}
			}
		}
		return $ret;
	}
	public function saveToDb() {
		$ret = -1;
		$setting;
		$db;
		$newObj = 0;

		if($this->changed) {
			$setting = Php_AndreaBoccaccio_Settings_SettingsFactory::getInstance()->getSettings('xml');
			$db = Php_AndreaBoccaccio_Db_DbFactory::getInstance()->getDb($setting->getSettingFromFullName('classes.db'));
			if($this->id > 0) {				
				$strSQL = "SELECT * FROM DOCUMENT_KIND WHERE ((id=";
				$strSQL .= $this->id;
				$strSQL .= ");";
				$res = $db->execQuery($strSQL);
				if($res["success"] == TRUE) {
					$ret = $res["numrows"];
					if($ret == 1) {
						$newObj = 0;
					}
					else {
						$newObj = 1;
					}
				}
			}
			else {
				$newObj = 1;
			}
			if($newObj) {
				$strSQL = "INSERT INTO DOCUMENT_KIND (code,name,description) ";
				$strSQL .= "VALUES ('";
				$strSQL .= $this->getCode();
				$strSQL .= "', '";
				$strSQL .= $this->getName();
				$strSQL .= "', '";
				$strSQL .= $this->getDescription();
				$strSQL .= "');";
				$res = $db->execQuery($strSQL);
				if($res["success"] == TRUE) {
					$ret = $res["numrows"];
				}
			}
			else {
				$strSQL = "UPDATE DOCUMENT_KIND ";
				$strSQL .= "SET code='";
				$strSQL .= $this->getCode();
				$strSQL .= "', name='";
				$strSQL .= $this->getName();
				$strSQL .= "', description='";
				$strSQL .= $this->getDescription();
				$strSQL .= "' WHERE (id =";
				$strSQL .= $this->getId();
				$strSQL .= ");";
				$res = $db->execQuery($strSQL);
				if($res["success"] == TRUE) {
					$ret = $res["numrows"];
				}
			}
		}
		
		return $ret;
	}
}