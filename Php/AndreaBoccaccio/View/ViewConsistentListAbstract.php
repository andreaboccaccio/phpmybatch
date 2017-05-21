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
abstract class Php_AndreaBoccaccio_View_ViewConsistentListAbstract extends Php_AndreaBoccaccio_View_ViewConsistentAbstract {
	
	protected function getVariableStarting($startString) {
		$ret = '';
		$i = 0;
		$tmpCmds = array_merge($_GET,$_POST);
	
		foreach ($tmpCmds as $tmpKey => $tmpValue) {
			if((strncmp($tmpKey, $startString, strlen($startString))==0)&&(strlen(trim($tmpValue))>0)) {
				if($i >0) {
					$ret .= "&";
				}
				$ret .= $tmpKey . "=" .trim($tmpValue);
				++$i;
			}
		}
		return $ret;
	}
	
	protected function getWhere() {
	
		return $this->getVariableStarting("w");
	}
	
	protected function getOrder() {
		return $this->getVariableStarting("ord");
	}
	
	protected function getNewOrder($newOrder) {
		$ret = $newOrder;
		$cmp = '';
		$setting = Php_AndreaBoccaccio_Settings_SettingsFactory::getInstance()->getSettings('xml');
		$dateOrderCode = $setting->getSettingFromFullName('date.orderCode');
		$dates = preg_split('/,/', $setting->getSettingFromFullName('date.datesFields'));
	
		if(isset($_POST["orderby"])) {
			if($_POST["orderby"] != null) {
				$cmp = trim($_POST["orderby"]);
			}
			else if(isset($_GET["orderby"])) {
				if($_GET["orderby"] != null) {
					$cmp = trim($_GET["orderby"]);
				}
			}
		}
		else if(isset($_GET["orderby"])) {
			if($_GET["orderby"] != null) {
				$cmp = trim($_GET["orderby"]);
			}
		}		
		if(in_array(strtoupper($newOrder),$dates)) {
			if(strcmp($newOrder, preg_replace('/' . $dateOrderCode. '/', '', $cmp))==0) {
				$ret = $dateOrderCode . $newOrder . " DESC";
			}
			else {
				$ret = $dateOrderCode . $newOrder;
			}
		} else {
			if(strcmp($newOrder, $cmp)==0) {
				$ret = $newOrder . " DESC";
			}
			else {
				$ret = $newOrder;
			}
		}
	
		return $ret;
	}
}