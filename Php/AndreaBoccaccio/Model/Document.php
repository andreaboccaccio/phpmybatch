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
class Php_AndreaBoccaccio_Model_Document extends Php_AndreaBoccaccio_Model_ModelAbstract implements Php_AndreaBoccaccio_Model_EffaceableModelInterface {
	
	public function __construct() {
		$this->setKind("document");
		$this->initMapping();
	}
	
	public function isEffaceable() {
		$ret = FALSE;
		$res = array();
		$n = -1;
		$setting = Php_AndreaBoccaccio_Settings_SettingsFactory::getInstance()->getSettings('xml');
		$db = Php_AndreaBoccaccio_Db_DbFactory::getInstance()->getDb($setting->getSettingFromFullName('classes.db'));
		$strSQL = "SELECT COUNT(*) AS N FROM ITEM_DENORM WHERE (document=";
		$strSQL .= $this->getVar("id");
		$strSQL .= ");";
		
		$res = $db->execQuery($strSQL);
		if($res["success"]) {
			$n = intval($res["result"][0]["N"]);
			if($n == 0) {
				$ret = TRUE;
			} else {
				$ret = FALSE;
			}
		} else {
			var_dump($strSQL);
			var_dump($res);
		}
		
		return $ret;
	}
}