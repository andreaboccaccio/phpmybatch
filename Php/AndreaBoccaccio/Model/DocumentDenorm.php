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
class Php_AndreaBoccaccio_Model_DocumentDenorm {

	private $id = -1;
	private $year = '';
	private $kind = '';
	private $code = '';
	private $contractor_kind = '';
	private $contractor_code = '';
	private $contractor = '';
	private $warehouse = '';
	private $date = '';
	private $vt_start = '';
	private $vt_end = '';
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
	public function getYear() {
		return $this->year;
	}
	public function setYear($year) {
		if($this->year != $year) {
			$this->year = $year;
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
	public function getContractorKind() {
		return $this->contractor_kind;
	}
	public function setContractorKind($contractor_kind) {
		if($this->contractor_kind != $contractor_kind) {
			$this->contractor_kind = $contractor_kind;
			$this->changed = TRUE;
		}
	}
	public function getContractorCode() {
		return $this->contractor_code;
	}
	public function setContractorCode($contractor_code) {
		if($this->contractor_code != $contractor_code) {
			$this->contractor_code = $contractor_code;
			$this->changed = TRUE;
		}
	}
	public function getContractor() {
		return $this->contractor;
	}
	public function setContractor($contractor) {
		if($this->contractor != $contractor) {
			$this->contractor = $contractor;
			$this->changed = TRUE;
		}
	}
	public function getWarehouse() {
		return $this->warehouse;
	}
	public function setWarehouse($warehouse) {
		if($this->warehouse != $warehouse) {
			$this->warehouse = $warehouse;
			$this->changed = TRUE;
		}
	}
	public function getDate() {
		return $this->date;
	}
	public function setDate($date) {
		if($this->date != $date) {
			$this->date = $date;
			$this->changed = TRUE;
		}
	}
	public function getVtStart() {
		return $this->vt_start;
	}
	public function setVtStart($vt_start) {
		if($this->vt_start != $vt_start) {
			$this->vt_start = $vt_start;
			$this->changed = TRUE;
		}
	}
	public function getVtEnd() {
		return $this->vt_end;
	}
	public function setVtEnd($vt_end) {
		if($this->vt_end != $vt_end) {
			$this->vt_end = $vt_end;
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
			,$year
			,$kind
			,$code
			,$contractor_kind
			,$contractor_code
			,$contractor
			,$warehouse
			,$date
			,$vt_start
			,$vt_end
			,$description) {
		$this->setId($id);
		$this->setYear($year);
		$this->setKind($kind);
		$this->setCode($code);
		$this->setContractorKind($contractor_kind);
		$this->setContractorCode($contractor_code);
		$this->setContractor($contractor);
		$this->setWarehouse($warehouse);
		$this->setDate($date);
		$this->setVtStart($vt_start);
		$this->setVtEnd($vt_end);
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
			$strSQL = "SELECT * FROM DOCUMENT_DENORM WHERE (id=";
			$strSQL .= $id;
			$strSQL .= ");";
			$res = $db->execQuery($strSQL);
			if($res["success"] == TRUE) {
				$ret = $res["numrows"];
				if($ret == 1) {
					$tmpRow = $res["result"][0];
					$this->init(intval($tmpRow["id"])
							,$tmpRow["year"]
							,$tmpRow["kind"]
							,$tmpRow["code"]
							,$tmpRow["contractor_kind"]
							,$tmpRow["contractor_code"]
							,$tmpRow["contractor"]
							,$tmpRow["warehouse"]
							,$tmpRow["date"]
							,$tmpRow["vt_start"]
							,$tmpRow["vt_end"]
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
				$strSQL = "SELECT * FROM DOCUMENT_DENORM WHERE ((id=";
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
				$strSQL = "INSERT INTO DOCUMENT_DENORM (year,kind,code,contractor_kind,contractor_code,contractor,warehouse,date,vt_start,vt_end,description) ";
				$strSQL .= "VALUES ('";
				$strSQL .= $this->getYear();
				$strSQL .= "', '";
				$strSQL .= $this->getKind();
				$strSQL .= "', '";
				$strSQL .= $this->getCode();
				$strSQL .= "', '";
				$strSQL .= $this->getContractorKind();
				$strSQL .= "', '";
				$strSQL .= $this->getContractorCode();
				$strSQL .= "', '";
				$strSQL .= $this->getContractor();
				$strSQL .= "', '";
				$strSQL .= $this->getWarehouse();
				$strSQL .= "', '";
				$strSQL .= $this->getDate();
				$strSQL .= "', '";
				$strSQL .= $this->getVtStart();
				$strSQL .= "', '";
				$strSQL .= $this->getVtEnd();
				$strSQL .= "', '";
				$strSQL .= $this->getDescription();
				$strSQL .= "');";
				$res = $db->execQuery($strSQL);
				if($res["success"] == TRUE) {
					$ret = $res["numrows"];
				}
			}
			else {
				$strSQL = "UPDATE DOCUMENT_DENORM ";
				$strSQL .= "SET year='";
				$strSQL .= $this->getYear();
				$strSQL .= "', kind='";
				$strSQL .= $this->getKind();
				$strSQL .= "', code='";
				$strSQL .= $this->getCode();
				$strSQL .= "', contractor_kind='";
				$strSQL .= $this->getContractorKind();
				$strSQL .= "', contractor_code='";
				$strSQL .= $this->getContractorCode();
				$strSQL .= "', contractor='";
				$strSQL .= $this->getContractor();
				$strSQL .= "', warehouse='";
				$strSQL .= $this->getWarehouse();
				$strSQL .= "', date='";
				$strSQL .= $this->getDate();
				$strSQL .= "', vt_start='";
				$strSQL .= $this->getVtStart();
				$strSQL .= "', vt_end='";
				$strSQL .= $this->getVtEnd();
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