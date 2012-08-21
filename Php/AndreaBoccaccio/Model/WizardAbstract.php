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
abstract class Php_AndreaBoccaccio_Model_WizardAbstract extends Php_AndreaBoccaccio_Model_SqlQueriesManagerAbstract implements Php_AndreaBoccaccio_Model_WizardInterface
{
	private $wizKind = '';
	
	private $fieldsMapping = array();
	
	private function addFieldMapping($sqlField, $viewInsertField) {
		if(!array_key_exists($sqlField, $this->fieldsMapping)) {
			$this->fieldsMapping[$sqlField] = $viewInsertField;
		}
	}
	
	protected function getWizKind() {
		return $this->wizKind;
	}
	
	protected function setWizKind($wizKind) {
		$this->wizKind = $wizKind;
	}
	
	public function getFieldsMapping() {
		$ret = array();
		$ret = $this->fieldsMapping;
		
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
		$strSQLNodes;
		$tmpStrNode;
		$i = -1;
		$nFound = -1;
		
		$xmlDoc->load($fileName);
		$xPath = new DOMXPath($xmlDoc);
		$strXPathQuery = '//sqlQueries/wizard[@id="' . $this->getWizKind() . '"]';;
		$nodes = $xPath->query($strXPathQuery);
		$nFound = $nodes->length;
		
		for ($i = 0; $i < $nFound; ++$i) {
			$tmpNode = $nodes->item($i);
			$strSQLNodes = $tmpNode->getElementsByTagName('strSQL');
			if($strSQLNodes->length == 1) {
				$strTmpNode = $strSQLNodes->item(0);
			}
			$this->addQuery($tmpNode->getAttribute('id')
					,$tmpNode->getAttribute('displayName')
					,preg_replace("/[\s]+/", " ", $strTmpNode->nodeValue));
		}
		$strXPathQuery = '//sqlQueries/wizard[@id="' . $this->getWizKind() . '"]/fieldsMapping/fieldMapping';;
		$nodes = $xPath->query($strXPathQuery);
		$nFound = $nodes->length;
		
		for ($i = 0; $i < $nFound; ++$i) {
			$tmpNode = $nodes->item($i);
			$this->addFieldMapping($tmpNode->getAttribute('sql')
					,$tmpNode->getAttribute('viewInsert'));
		}
	}
}