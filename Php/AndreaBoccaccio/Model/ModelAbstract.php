<?php
/*
 * phpmywhs - An open source warehouse management software.
 * Copyright (C)2012 Andrea Boccaccio
 * contact email: andrea@andreaboccaccio.com
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
abstract class Php_AndreaBoccaccio_Model_ModelAbstract implements Php_AndreaBoccaccio_Model_ModelInterface {
	
	private $kind = '';
	private $vars = array();
	private $mappingModel = null;
	private $changed = FALSE;
	
	protected function getKind() {
		return $this->kind;
	}
	
	protected function setKind($kind) {
		$this->kind = $kind;
	}
	
	protected function initMapping() {
		$tmpFactory = Php_AndreaBoccaccio_Model_MappingModelFactory::getInstance();
		$this->mappingModel = $tmpFactory->getMappingModel($this->getKind());
		$this->vars = $this->mappingModel->getDefaults();
	}
	
	public function getVar($name) {
		$ret = null;
	
		if(array_key_exists($name, $this->vars)) {
			$ret = $this->vars[$name];
		}
		else {
			$ret = null;
		}
		return $ret;
	}
	
	public function setVar($name, $value) {
		if(array_key_exists($name, $this->vars)) {
			switch ($this->mappingModel->getVarKind($name)) {
				case "int":
					if($this->vars[$name] != intval($value)) {
						$this->vars[$name] = intval($value);
						$this->changed = TRUE;
					}
					break;
				case "float":
					if($this->vars[$name] != floatval($value)) {
						$this->vars[$name] = floatval($value);
						$this->changed = TRUE;
					}
					break;
				default:
					if($this->vars[$name] != $value) {
						$this->vars[$name] = $value;
						$this->changed = TRUE;
					}
			}
		}
	}
	public function init(&$initArray) {
		foreach ($initArray as $name => $value) {
			$this->setVar($name, $value);
		}
	}
	public function loadFromDbById($id) {
		$ret = -1;
		$setting;
		$db;
		$res = array();
		$tmpRow = array();
		$strSQL = '';
		$i = 0;
		$tmpArray = array();
	
		if($id > 0) {
			$setting = Php_AndreaBoccaccio_Settings_SettingsFactory::getInstance()->getSettings('xml');
			$db = Php_AndreaBoccaccio_Db_DbFactory::getInstance()->getDb($setting->getSettingFromFullName('classes.db'));
			$strSQL = "SELECT ";
			foreach ($this->mappingModel->getDbNames() as $num => $dbname) {
				if($i>0) {
					$strSQL .= ",";
				}
				$strSQL .= $dbname;
				++$i;
			}
			$strSQL .= " FROM ";
			$strSQL .= $this->mappingModel->getDbTabName();
			$strSQL .= " WHERE (id=";
			$strSQL .= $id;
			$strSQL .= ");";
			$res = $db->execQuery($strSQL);
			if($res["success"] == TRUE) {
				$ret = $res["numrows"];
				if($ret == 1) {
					$tmpRow = $res["result"][0];
					foreach ($res["fields"] as $dbname) {
						$tmpArray[$this->mappingModel->getVarName(null,$dbname)] = $tmpRow[$dbname];
					}
					$this->init($tmpArray);
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
		$tmpArray = array();
		$max = -1;
		$i = -1;
		$prec = 0;
		$appName = "";
	
		if($this->changed) {
			$setting = Php_AndreaBoccaccio_Settings_SettingsFactory::getInstance()->getSettings('xml');
			$db = Php_AndreaBoccaccio_Db_DbFactory::getInstance()->getDb($setting->getSettingFromFullName('classes.db'));
			if($this->getVar("id") > 0) {
				$strSQL = "SELECT * FROM ";
				$strSQL .= $this->mappingModel->getDbTabName();
				$strSQL .= " WHERE ((id=";
				$strSQL .= $this->getVar("id");
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
			$i = 0;
			foreach ($this->mappingModel->getDbNames() as $num => $dbname) {
				if(strcmp($dbname, "id") != 0) {
					$appName = $this->mappingModel->getVarName(null,$dbname);
					$tmpArray[$i] = array();
					$tmpArray[$i]["dbname"] = $dbname;
					$tmpArray[$i]["kind"] = $this->mappingModel->getVarKind($appName);
					$tmpArray[$i]["value"] = $this->getVar($appName);
					++$i;
				}
			}
			$max = count($tmpArray);
			if($newObj) {
				$prec = 0;
				$strSQL = "INSERT INTO ";
				$strSQL .= $this->mappingModel->getDbTabName();
				$strSQL .= " (";
				for($i = 0; $i < $max;++$i) {
					if($i > 0) {
						$strSQL .= ",";
					}
					$strSQL .= $tmpArray[$i]["dbname"];
				}
				$strSQL .= ") ";
				$strSQL .= "VALUES (";
				for($i = 0; $i < $max;++$i) {
					if($i > 0) {
						if($prec > 0) {
							$strSQL .= "'";
						}
						$strSQL .= ",";
					}
					switch ($tmpArray[$i]["kind"]) {
						case "int":
						case "float":
							$prec = 0;
							break;
						default:
							$strSQL .= "'";
							$prec = 1;
					}
					$strSQL .= $tmpArray[$i]["value"];
				}
				if($prec > 0) {
					$strSQL .= "'";
				}
				$strSQL .= ");";
				$res = $db->execQuery($strSQL);
				if($res["success"] == TRUE) {
					$ret = $res["numrows"];
				}
			}
			else {
				$prec = 0;
				$strSQL = "UPDATE ";
				$strSQL .= $this->mappingModel->getDbTabName();
				$strSQL .= " ";
				$strSQL .= "SET ";
				for($i = 0; $i < $max;++$i) {
					if($i > 0) {
						if($prec > 0) {
							$strSQL .= "'";
						}
						$strSQL .= ", ";
					}
					$strSQL .= $tmpArray[$i]["dbname"];
					$strSQL .= "=";
					switch ($tmpArray[$i]["kind"]) {
						case "int":
						case "float":
							$prec = 0;
							break;
						default:
							$strSQL .= "'";
							$prec = 1;
					}
					$strSQL .= $tmpArray[$i]["value"];
				}
				if($prec > 0) {
					$strSQL .= "'";
				}
				$strSQL .= " WHERE (id =";
				$strSQL .= $this->getVar("id");
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