<?php
/*
 * phpmybatch - An open source batches of goods management system software.
 * Copyright (C)2012 Andrea Boccaccio
 * contact email: andrea@andreaboccaccio.it
 * 
 * This file is part of phpmybatch.
 * 
 * phpmybatch is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * phpmybatch is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with phpmybatch. If not, see <http://www.gnu.org/licenses/>.
 * 
 */
class Php_AndreaBoccaccio_Model_StealBatch {
	
	public function steal($oldDocId, $newDocId) {
		$res = array();
		$setting = Php_AndreaBoccaccio_Settings_SettingsFactory::getInstance()->getSettings('xml');
		$db = Php_AndreaBoccaccio_Db_DbFactory::getInstance()->getDb($setting->getSettingFromFullName('classes.db'));
		$strSQL = "UPDATE ITEM_DENORM SET document=" . $newDocId;
		$strSQL .= " WHERE (document=" .$oldDocId . ");";
		
		$res = $db->execQuery($strSQL);
		if(!$res["success"]) {
			var_dump($strSQL);
			var_dump($res);
		}
		return $res;
	}
}