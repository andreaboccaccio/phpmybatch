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
abstract class Php_AndreaBoccaccio_FactoryAbstract {
	
	protected $classArray;
	
	protected $classDefault;
	
	private $kind;
	
	protected function setKind($kind) {
		$this->kind = $kind;
	}
	
	protected function getKind() {
		return $this->kind;
	}
	
	protected function getClasses() {
		$retArray = array();
		$settingsFac = Php_AndreaBoccaccio_Settings_SettingsFactory::getInstance();
		$settings = $settingsFac->getSettings('xml');
		$fileName = $settings->getSettingFromFullName('factories.fileName');
		$xmlDoc = new DOMDocument();
		$xPath;
		$strXPathQuery = '';
		$nodes;
		$i = -1;
		$nFound = -1;
		
		$xmlDoc->load($fileName);
		$xPath = new DOMXPath($xmlDoc);
		$strXPathQuery = '//factories/factory[@id="' . $this->getKind() . '"]/class';
		$nodes = $xPath->query($strXPathQuery);
		$nFound = $nodes->length;
		for ($i = 0; $i < $nFound; ++$i) {
			$tmpNode = $nodes->item($i);
			$retArray[$i] = $tmpNode->getAttribute('id');
			if ($tmpNode->getAttribute('default') == 'yes') {
				$this->classDefault = $i;
			}
		}
	
		return $retArray;
	}
	
	protected function init() {
		
		foreach ($this->getClasses() as $tmpStrCl) {
			$tmpCl = new $tmpStrCl();
			$this->classArray[$tmpCl->getKind()] = $tmpCl;
		}
	}
	
	public function getClass($kind) {
		$ret = null;
		if(array_key_exists($kind, $this->classArray)) {
			$ret = $this->classArray[$kind];
			$this->classArray[$kind] = new get_class($ret);
		}
		else {
			$ret = $this->classArray[$this->classDefault];
			$this->classArray[$this->classDefault] = new get_class($ret);
		}
		return $ret;
	}
}