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
abstract class Php_AndreaBoccaccio_Model_WizardFieldsFilterAbstract extends Php_AndreaBoccaccio_Model_WizardFieldsViewAbstract implements Php_AndreaBoccaccio_Model_WizardFieldsFilterInterface {
	
	private $fieldsFilter = array();
	
	private function addFieldFilter($sqlField, $filterField) {
		if(!array_key_exists($sqlField, $this->fieldsFilter)) {
			$this->fieldsFilter[$sqlField] = $filterField;
		}
	}
	
	public function getFieldsFilter() {
		$ret = array();
		$ret = $this->fieldsFilter;
	
		return $ret;
	}
	
	public function init() {
	
		$tmpArray = array();
		$settingsFac = Php_AndreaBoccaccio_Settings_SettingsFactory::getInstance();
		$settings = $settingsFac->getSettings('xml');
		$fileName = $settings->getSettingFromFullName('sqlQueries.fileName');
		$xmlDoc = new DOMDocument();
		$xPath;
		$strXPathQuery = '';
		$nodes;
		$tmpNode;
		$i = -1;
		$nFound = -1;
	
		parent::init();
	
		$xmlDoc->load($fileName);
		$xPath = new DOMXPath($xmlDoc);
		$strXPathQuery = '//sqlQueries/wizard[@id="' . $this->getWizKind() . '"]/fieldsFilter/fieldFilter';;
		$nodes = $xPath->query($strXPathQuery);
		$nFound = $nodes->length;
	
		for ($i = 0; $i < $nFound; ++$i) {
			$tmpNode = $nodes->item($i);
			$this->addFieldFilter($tmpNode->getAttribute('sql')
					,$tmpNode->getAttribute('filterField'));
		}
	}
}