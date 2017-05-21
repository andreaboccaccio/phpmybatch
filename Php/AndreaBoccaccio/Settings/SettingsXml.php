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
 * along with phpmywhs.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class Php_AndreaBoccaccio_Settings_SettingsXml extends Php_AndreaBoccaccio_Settings_SettingsAbstract {
	
	private $fileName = 'config/config.xml';
	
	private $settings;
	
	private static $instance = null;
	
	private function __clone() {
		
	}
	
	private function __construct() {
		$this->setKind('xml');
		$this->load();
	}
	
	public static function getInstance() {
		if(self::$instance == null) {
			self::$instance = new Php_AndreaBoccaccio_Settings_SettingsXml();
		}
		return self::$instance;
	}
	
	protected function load() {
		$this->settings = new DOMDocument();
		$this->settings->load($this->fileName);
	}
	
	public function getSettingFromFullName($settingFullName) {
		$ret = '';
		$xpath = new DOMXPath($this->settings);
		$strXPathQuery = '//config/' . str_replace('.', '/', $settingFullName);
		$nodes = $xpath->query($strXPathQuery);
		$firstNode = $nodes->item(0);
		$firstValue = '';
		if($firstNode != null) {
			$ret = $this->settings->saveXML($firstNode);
			$firstValue = preg_split("/[\s]+/", trim($firstNode->nodeValue));
			$ret = $firstValue[0];
		}
		else {
			$ret = '';
		}
		return $ret;
	}
}