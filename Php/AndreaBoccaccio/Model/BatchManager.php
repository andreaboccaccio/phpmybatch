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
class Php_AndreaBoccaccio_Model_BatchManager extends Php_AndreaBoccaccio_Model_ManagerAbstract implements Php_AndreaBoccaccio_Model_BatchManagerInterface {
	
	public function __construct() {
		$this->setKind("batch");
		$this->init();
	}
	
	protected function getModel() {
		return new Php_AndreaBoccaccio_Model_Batch();
	}
	
	public function getFirstArrival($batch=null) {
		$sRet = '';
		$setting = Php_AndreaBoccaccio_Settings_SettingsFactory::getInstance()->getSettings('xml');
		$db = Php_AndreaBoccaccio_Db_DbFactory::getInstance()->getDb($setting->getSettingFromFullName('classes.db'));
		$res = array();
		$strSQL = '';
		if($batch != null) {
			if(strlen($batch)>0) {
				$strSQL = "SELECT";
				$strSQL .= " A.batch";
				$strSQL .= " ,CONCAT(SUBSTRING(A.MinDate,7,2),'/',SUBSTRING(A.MinDate,5,2),'/',SUBSTRING(A.MinDate,1,4)) AS DataArrivo";
				$strSQL .= " FROM";
				$strSQL .= " (SELECT";
				$strSQL .= " batch";
				$strSQL .= " ,CAST(MIN(CAST(CONCAT(SUBSTRING(arrival,7,4),SUBSTRING(arrival,4,2),SUBSTRING(arrival,1,2)) AS UNSIGNED INTEGER)) AS CHAR) AS MinDate";
				$strSQL .= " FROM";
				$strSQL .= " ITEM_DENORM";
				$strSQL .= " WHERE";
				$strSQL .= "(batch ='" . $batch . "')";
				$strSQL .= " GROUP BY";
				$strSQL .= " batch) AS A";
				$strSQL .= "; ";
			
				$res = $db->execQuery($strSQL);
				if($res["success"] == TRUE) {
					if($res["numrows"] > 0) {
						$sRet = $res["result"][0]["DataArrivo"];
					}
					else {
						$sRet = '';
					}
				}
				else {
					var_dump($strSQL);
					var_dump($res);
				}
			}
		}
		return $sRet;
	}
}