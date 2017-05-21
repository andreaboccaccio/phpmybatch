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
abstract class Php_AndreaBoccaccio_Model_MappingModelAbstract implements Php_AndreaBoccaccio_Model_MappingModelInterface {
	
	private $dbTabname = '';
	private $varsMapping = array();
	private $varsDefault = array();
	private $reverseVarsMapping = array();
	private $kind;
	
	private function setDbTabName($dbTabName) {
		$this->dbTabname = $dbTabName;
	}
	
	private function addVarMapping($appName, $kind, $dbName, $defValue) {
		$tmpArray = array();
		if((!array_key_exists($appName, $this->varsMapping))
				&&(!array_key_exists($dbName, $this->reverseVarsMapping))) {
			$tmpArray["kind"] = $kind;
			$tmpArray["dbName"] = $dbName;
			$this->varsMapping[$appName] = $tmpArray;
			$this->varsDefault[$appName] = $defValue;
			$this->reverseVarsMapping["$dbName"] = $appName;
		}
	}
	
	protected function loadStructureFromXml() {
		$tmpArray = array();
		$settingsFac = Php_AndreaBoccaccio_Settings_SettingsFactory::getInstance();
		$settings = $settingsFac->getSettings('xml');
		$fileName = $settings->getSettingFromFullName('model.fileName');
		$xmlDoc = new DOMDocument();
		$xPath;
		$strXPathQuery = '';
		$nodes;
		$i = -1;
		$nFound = -1;
	
		$xmlDoc->load($fileName);
		$xPath = new DOMXPath($xmlDoc);
		$strXPathQuery = '//model/class[@id="' . $this->getKind() . '"]/dbtab';
		$nodes = $xPath->query($strXPathQuery);
		$nFound = $nodes->length;
		if($nFound == 1) {
			$tmpNode = $nodes->item(0);
			$this->setDbTabName($tmpNode->getAttribute('name'));
		}
		$strXPathQuery = '//model/class[@id="' . $this->getKind() . '"]/var';
		$nodes = $xPath->query($strXPathQuery);
		$nFound = $nodes->length;
		for ($i = 0; $i < $nFound; ++$i) {
			$tmpNode = $nodes->item($i);
			$this->addVarMapping($tmpNode->getAttribute('name')
					,$tmpNode->getAttribute('kind')
					,$tmpNode->getAttribute('dbName')
					,$tmpNode->getAttribute('value'));
		}
		$this->changed = FALSE;
	}
	
	protected function setKind($kind) {
		$this->kind = $kind;
	}
	
	public function getKind() {
		return $this->kind;
	}
	
	public function getDbTabName() {
		return $this->dbTabname;
	}
	
	public function getVarName($appName=null, $dbName=null) {
		
		$retName = "";
		
		if(($appName != null)&&($dbName == null)) {
			if(array_key_exists($appName, $this->varsMapping)) {
				$retName = $this->varsMapping[$appName]["dbName"];
			}
		}
		else if(($appName == null)&&($dbName != null)) {
			if(array_key_exists($dbName, $this->reverseVarsMapping)) {
				$retName = $this->reverseVarsMapping[$dbName];
			}
		}
		
		return $retName;
	}
	
	public function getVarKind($appName) {
		
		$retVarKind = "";
		
		if(isset($appName)) {
			if($appName != null) {
				if(array_key_exists($appName, $this->varsMapping)) {
					$retVarKind = $this->varsMapping[$appName]["kind"];
				}
			}
		}
		
		return $retVarKind;
	}
	
	public function getDefaults() {
		
		return $this->varsDefault;
	}
	
	public function getDbNames() {
		
		return array_keys($this->reverseVarsMapping);
	}
}