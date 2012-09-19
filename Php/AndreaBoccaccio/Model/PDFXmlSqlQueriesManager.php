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
class Php_AndreaBoccaccio_Model_PDFXmlSqlQueriesManager extends Php_AndreaBoccaccio_Model_XmlSqlQueriesManager implements Php_AndreaBoccaccio_Model_PDFSqlQueriesInterface
{
	private $pdfTables = array();
	
	protected function addPDFTable($sqlId
			,$pageOrientation='P'
			,$unit='mm'
			,$pageSize='A4') {
		$tmpPageSize;
		
		if(!array_key_exists($sqlId, $this->pdfTables)) {
			switch ($pageSize) {
				case 'A3' :
				case 'A4' :
				case 'A5' :
				case 'Letter' :
				case 'Legal' :
					$tmpPageSize = $pageSize;
					break;
				default:
					$tmpPageSize = preg_split("/x/", $pageSize);
			}
			$this->pdfTables[$sqlId] = new Php_AndreaBoccaccio_PDF_SimplePDFTable(
					$pageOrientation
					,$unit
					,$tmpPageSize
					);
		}
	}
	
	public function printable($sqlId) {
		return array_key_exists($sqlId, $this->pdfTables);
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
		$tmpPrintNodes;
		$tmpPrintNode;
		$tmpRowNodes;
		$tmpRowNode;
		$tmpColoumnNodes;
		$tmpColoumnNode;
		$cFound;
		$j = -1;
		$tmpHeaderNodes;
		$tmpHeaderNode;
		$hdrFill = false;
		$tmpBodyNodes;
		$tmpBodyNode;
		$bodyFill = false;
		$tmpPDFTable;
		
		$i = -1;
		$nFound = -1;
		
		$xmlDoc->load($fileName);
		$xPath = new DOMXPath($xmlDoc);
		$strXPathQuery = '//sqlQueries/sqlQuery';
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
			$tmpPrintNodes = $tmpNode->getElementsByTagName('printInfo');
			if($tmpPrintNodes->length == 1) {
				$tmpPrintNode = $tmpPrintNodes->item(0);
				$this->addPDFTable($tmpNode->getAttribute('id')
						,$tmpPrintNode->getAttribute('pageOrientation')
						,$tmpPrintNode->getAttribute('unit')
						,$tmpPrintNode->getAttribute('pageSize')
						);
				$tmpRowNodes = $tmpPrintNode->getElementsByTagName('rowInfo');
				$tmpPDFTable = $this->pdfTables[$tmpNode->getAttribute('id')];
				$tmpPDFTable->setTableTitle($tmpNode->getAttribute('displayName'));
				$tmpPDFTable->setRepeatTableHeader(true);
				if($tmpRowNodes->length == 1) {
					$tmpRowNode = $tmpRowNodes->item(0);
					$tmpPDFTable->setRowInfo($tmpRowNode->getAttribute('headerHSize')
							,$tmpRowNode->getAttribute('bodyHSize'));
				}
				$tmpColoumnNodes = $tmpPrintNode->getElementsByTagName('columnInfo');
				$cFound = $tmpColoumnNodes->length;
				for($j=0; $j < $cFound; ++$j) {
					$tmpColoumnNode = $tmpColoumnNodes->item($j);
					
					$tmpHeaderNodes = $tmpColoumnNode->getElementsByTagName('header');
					$tmpBodyNodes = $tmpColoumnNode->getElementsByTagName('body');
					if(($tmpHeaderNodes->length == 1)&&($tmpBodyNodes->length == 1)) {
						$tmpHeaderNode = $tmpHeaderNodes->item(0);
						$tmpBodyNode = $tmpBodyNodes->item(0);
						if(strncmp($tmpHeaderNode->getAttribute('fill'),'enabled',strlen('enabled')) == 0) {
							$hdrFill = true;
						} else {
							$hdrFill = false;
						}
						if(strncmp($tmpBodyNode->getAttribute('fill'),'enabled',strlen('enabled')) == 0) {
							$bodyFill = true;
						} else {
							$bodyFill = false;
						}
						$tmpPDFTable->addHeader($tmpColoumnNode->getAttribute('sqlId'));
						$tmpPDFTable->addColumnInfo(
								$tmpColoumnNode->getAttribute('sqlId')
								,$tmpColoumnNode->getAttribute('wSize')
								,$tmpHeaderNode->getAttribute('display')
								,$tmpHeaderNode->getAttribute('fontFamily')
								,$tmpHeaderNode->getAttribute('fontStyle')
								,$tmpHeaderNode->getAttribute('fontSize')
								,$tmpHeaderNode->getAttribute('align')
								,$tmpHeaderNode->getAttribute('textColor')
								,$hdrFill
								,$tmpHeaderNode->getAttribute('fillColor')
								,$tmpHeaderNode->getAttribute('border')
								,$tmpBodyNode->getAttribute('fontFamily')
								,$tmpBodyNode->getAttribute('fontStyle')
								,$tmpBodyNode->getAttribute('fontSize')
								,$tmpBodyNode->getAttribute('align')
								,$tmpBodyNode->getAttribute('textColor')
								,$bodyFill
								,$tmpBodyNode->getAttribute('fillAColor')
								,$tmpBodyNode->getAttribute('fillBColor')
								,$tmpBodyNode->getAttribute('border')
								,$tmpBodyNode->getAttribute('format')
								);
					}
				}
			}
		}
	}
	
	public function getPDF($queryId, &$filter=null, $orderby=null) {
		
		$res = array();
		$tmpPDFTable;
		
		if($this->printable($queryId)) {
			$res = $this->getRes($queryId, null, $filter, $orderby);
			$tmpPDFTable = $this->pdfTables[$queryId];
			$tmpPDFTable->AliasNbPages();
			$tmpPDFTable->AddPage();
			$tmpPDFTable->simpleTable($res["result"]["result"]);
			$tmpPDFTable->Output($queryId, 'I');
		}
	}
}