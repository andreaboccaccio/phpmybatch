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
class Php_AndreaBoccaccio_Model_DocumentKindManager {
	
	private $docKindArray = array();
	
	public function getDocs($code = null, $name = null, $description = null, $orderby = null) {
		$tmpDocKind;
		$tmpRow;
		$i = -1;
		$where = 0;
		$setting = Php_AndreaBoccaccio_Settings_SettingsFactory::getInstance()->getSettings('xml');
		$db = Php_AndreaBoccaccio_Db_DbFactory::getInstance()->getDb($setting->getSettingFromFullName('classes.db'));
		$strSQL = "SELECT * FROM DOCUMENT_KIND";
		if(($code != null)||($name != null)||($description != null)) {
			$strSQL .= " WHERE (";
			if($code != null) {
				if(strlen($code) > 0) {
					$strSQL .= "(code LIKE '%";
					$strSQL .= $code;
					$strSQL .= "%')";
					++$where;
				}
			}
			if($where > 0) {
				$strSQL .= " AND ";
			}
			if($name != null) {
				if(strlen($name) > 0) {
					$strSQL .= "(name LIKE '%";
					$strSQL .= $name;
					$strSQL .= "%')";
					++$where;
				}
			}
			if($where > 0) {
				$strSQL .= " AND ";
			}
			if($description != null) {
				if(strlen($description) > 0) {
					$strSQL .= "(description LIKE '%";
					$strSQL .= $description;
					$strSQL .= "%')";
					++$where;
				}
			}
			$srtSQL .= ")";
		}
		if($orderby != null) {
			$strSQL .= " ORDER BY ";
			$strSQL .= $orderby;
		}
		$strSQL .= ";";
		$res = $db->execQuery($strSQL);
		if($res["success"] == TRUE) {
			if($res["numrows"] > 0) {
				for ($i = 0; $i < $res["numrows"]; ++$i) {
					$tmpRow = $res["result"][$i];
					$tmpDocKind = new Php_AndreaBoccaccio_Model_DocumentKind();
					$tmpDocKind->init($tmpRow["id"], $tmpRow["code"], $tmpRow["name"], $tmpRow["description"]);
					$this->docKindArray[$i] = $tmpDocKind;
				}
			}
		}
		return $this->docKindArray;
	}
	
	public function eraseDocumentKind($id) {
		$setting = Php_AndreaBoccaccio_Settings_SettingsFactory::getInstance()->getSettings('xml');
		$db = Php_AndreaBoccaccio_Db_DbFactory::getInstance()->getDb($setting->getSettingFromFullName('classes.db'));
		$strSQL = "DELETE FROM DOCUMENT_KIND";
		$res = array();
		
		$strSQL .= " WHERE(id=" .$id .");";
		$res = $db->execQuery($strSQL);
	}
}