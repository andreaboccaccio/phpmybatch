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
class Php_AndreaBoccaccio_Model_ItemDenorm {

	private $id = -1;
	private $document = -1;
	private $kind = '';
	private $code = '';
	private $name = '';
	private $qty = -1;
	private $value = -1;
	private $cost = -1.0;
	private $price = -1.0;
	private $description = '';
	private $changed = FALSE;

	public function __construct() {
		$this->changed = FALSE;
	}
	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		if($this->id != intval($id)) {
			$this->id = intval($id);
			$this->changed = TRUE;
		}
	}
	public function getDocument() {
		return $this->document;
	}
	public function setDocument($document) {
		if($this->document != $document) {
			$this->document = $document;
			$this->changed = TRUE;
		}
	}
	public function getKind() {
		return $this->kind;
	}
	public function setKind($kind) {
		if($this->kind != $kind) {
			$this->kind = $kind;
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
	public function getQty() {
		return $this->qty;
	}
	public function setQty($qty) {
		if($this->qty != intval($qty)) {
			$this->qty = intval($qty);
			$this->changed = TRUE;
		}
	}
	public function getValue() {
		return $this->value;
	}
	public function setValue($value) {
		if($this->value != intval($value)) {
			$this->value = intval($value);
			$this->changed = TRUE;
		}
	}
	public function getCost() {
		return $this->cost;
	}
	public function setCost($cost) {
		if($this->cost != floatval($cost)) {
			$this->cost = floatval($cost);
			$this->changed = TRUE;
		}
	}
	public function getPrice() {
		return $this->price;
	}
	public function setPrice($price) {
		if($this->price != floatval($price)) {
			$this->price = floatval($price);
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
	public function init($id
			,$document
			,$kind
			,$code
			,$name
			,$qty
			,$value
			,$cost
			,$price
			,$description) {
		$this->setId($id);
		$this->setDocument($document);
		$this->setKind($kind);
		$this->setCode($code);
		$this->setName($name);
		$this->setQty($qty);
		$this->setValue($value);
		$this->setCost($cost);
		$this->setPrice($price);
		$this->setDescription($description);
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
			$strSQL = "SELECT * FROM ITEM_DENORM WHERE (id=";
			$strSQL .= $id;
			$strSQL .= ");";
			$res = $db->execQuery($strSQL);
			if($res["success"] == TRUE) {
				$ret = $res["numrows"];
				if($ret == 1) {
					$tmpRow = $res["result"][0];
					$this->init(intval($tmpRow["id"])
							,$tmpRow["document"]
							,$tmpRow["kind"]
							,$tmpRow["code"]
							,$tmpRow["name"]
							,$tmpRow["qty"]
							,$tmpRow["value"]
							,$tmpRow["cost"]
							,$tmpRow["price"]
							,$tmpRow["description"]
							);
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
				$strSQL = "SELECT * FROM ITEM_DENORM WHERE ((id=";
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
				$strSQL = "INSERT INTO ITEM_DENORM (";
				$strSQL .= "document";
				$strSQL .= ",kind";
				$strSQL .= ",code";
				$strSQL .= ",name";
				$strSQL .= ",qty";
				$strSQL .= ",value";
				$strSQL .= ",cost";
				$strSQL .= ",price";
				$strSQL .= ",description) ";
				$strSQL .= "VALUES ('";
				$strSQL .= $this->getDocument();
				$strSQL .= "', '";
				$strSQL .= $this->getKind();
				$strSQL .= "', '";
				$strSQL .= $this->getCode();
				$strSQL .= "', '";
				$strSQL .= $this->getName();
				$strSQL .= "', '";
				$strSQL .= $this->getQty();
				$strSQL .= "', '";
				$strSQL .= $this->getValue();
				$strSQL .= "', '";
				$strSQL .= $this->getCost();
				$strSQL .= "', '";
				$strSQL .= $this->getPrice();
				$strSQL .= "', '";
				$strSQL .= $this->getDescription();
				$strSQL .= "');";
				$res = $db->execQuery($strSQL);
				if($res["success"] == TRUE) {
					$ret = $res["numrows"];
				}
			}
			else {
				$strSQL = "UPDATE ITEM_DENORM ";
				$strSQL .= "SET document='";
				$strSQL .= $this->getDocument();
				$strSQL .= "', kind='";
				$strSQL .= $this->getKind();
				$strSQL .= "', code='";
				$strSQL .= $this->getCode();
				$strSQL .= "', name='";
				$strSQL .= $this->getName();
				$strSQL .= "', qty='";
				$strSQL .= $this->getQty();
				$strSQL .= "', value='";
				$strSQL .= $this->getValue();
				$strSQL .= "', cost='";
				$strSQL .= $this->getCost();
				$strSQL .= "', price='";
				$strSQL .= $this->getPrice();
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