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
			$this->pdfTables[$sqlId] = new Php_AndreaBoccaccio_PDF_MyPDFTable(
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
		$tmpPageHeaderNodes;
		$tmpPageHeaderNode;
		$pFound = -1;
		$tmpPageFooterNodes;
		$tmpPageFooterNode;
		$pKind = null;
		$pX = null;
		$pY = null;
		$pWSize = null;
		$pHSize = null;
		$pDisplay = null;
		$pFontFamily = null;
		$pFontStyle = null;
		$pFontSize = null;
		$pAlign = null;
		$pTextColor = null;
		$pFill = null;
		$pFillColor = null;
		$pBorder = null;
		$pLn = null;
		$pType = null;
		$pLink = null;
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
				$tmpPDFTable = $this->pdfTables[$tmpNode->getAttribute('id')];
				$tmpPageHeaderNodes = $tmpPrintNode->getElementsByTagName('pageHeader');
				$pFound = $tmpPageHeaderNodes->length;
				for($j=0; $j < $pFound; ++$j) {
					$tmpPageHeaderNode = $tmpPageHeaderNodes->item($j);
					$pKind = $tmpPageHeaderNode->getAttribute('kind');
					if($tmpPageHeaderNode->hasAttribute('x')) {
						$pX = $tmpPageHeaderNode->getAttribute('x');
					} else {
						$pX = null;
					}
					if($tmpPageHeaderNode->hasAttribute('y')) {
						$pY = $tmpPageHeaderNode->getAttribute('y');
					} else {
						$pY = null;
					}
					if($tmpPageHeaderNode->hasAttribute('wSize')) {
						$pWSize = $tmpPageHeaderNode->getAttribute('wSize');
					} else {
						$pWSize = 0;
					}
					if($tmpPageHeaderNode->hasAttribute('hSize')) {
						$pHSize = $tmpPageHeaderNode->getAttribute('hSize');
					} else {
						$pHSize = 0;
					}
					if($tmpPageHeaderNode->hasAttribute('display')) {
						$pDisplay = $tmpPageHeaderNode->getAttribute('display');
					} else {
						$pDisplay = null;
					}
					if($tmpPageHeaderNode->hasAttribute('fontFamily')) {
						$pFontFamily = $tmpPageHeaderNode->getAttribute('fontFamily');
					} else {
						$pFontFamily = null;
					}
					if($tmpPageHeaderNode->hasAttribute('fontStyle')) {
						$pFontStyle = $tmpPageHeaderNode->getAttribute('fontStyle');
					} else {
						$pFontStyle = null;
					}
					if($tmpPageHeaderNode->hasAttribute('fontSize')) {
						$pFontSize = $tmpPageHeaderNode->getAttribute('fontSize');
					} else {
						$pFontSize = null;
					}
					if($tmpPageHeaderNode->hasAttribute('align')) {
						$pAlign = $tmpPageHeaderNode->getAttribute('align');
					} else {
						$pAlign = null;
					}
					if($tmpPageHeaderNode->hasAttribute('textColor')) {
						$pTextColor = $tmpPageHeaderNode->getAttribute('textColor');
					} else {
						$pTextColor = null;
					}
					if($tmpPageHeaderNode->hasAttribute('fill')) {
						if(strncmp($tmpPageHeaderNode->getAttribute('fill'),'enabled',strlen('enabled')) == 0) {
							$pFill = true;
						} else {
							$pFill = false;
						}
					} else {
						$pFill = false;
					}
					if($tmpPageHeaderNode->hasAttribute('fillColor')) {
						$pFillColor = $tmpPageHeaderNode->getAttribute('fillColor');
					} else {
						$pFillColor = null;
					}
					if($tmpPageHeaderNode->hasAttribute('border')) {
						$pBorder = $tmpPageHeaderNode->getAttribute('border');
					} else {
						$pBorder = null;
					}
					if($tmpPageHeaderNode->hasAttribute('ln')) {
						$pLn = $tmpPageHeaderNode->getAttribute('ln');
					} else {
						$pLn = null;
					}
					if($tmpPageHeaderNode->hasAttribute('type')) {
						$pType = $tmpPageHeaderNode->getAttribute('type');
					} else {
						$pType = null;
					}
					if($tmpPageHeaderNode->hasAttribute('link')) {
						$pLink = $tmpPageHeaderNode->getAttribute('link');
					} else {
						$pLink = null;
					}
					$tmpPDFTable->addPageHeader($pKind
							,$pX
							,$pY
							,$pWSize
							,$pHSize
							,$pDisplay
							,$pFontFamily
							,$pFontStyle
							,$pFontSize
							,$pAlign
							,$pTextColor
							,$pFill
							,$pFillColor
							,$pBorder
							,$pLn
							,$pType
							,$pLink
							);
				}
				$tmpPageFooterNodes = $tmpPrintNode->getElementsByTagName('pageFooter');
				$pFound = $tmpPageFooterNodes->length;
				for($j=0; $j < $pFound; ++$j) {
					$tmpPageFooterNode = $tmpPageFooterNodes->item($j);
					$pKind = $tmpPageFooterNode->getAttribute('kind');
					if($tmpPageFooterNode->hasAttribute('x')) {
						$pX = $tmpPageFooterNode->getAttribute('x');
					} else {
						$pX = null;
					}
					if($tmpPageFooterNode->hasAttribute('y')) {
						$pY = $tmpPageFooterNode->getAttribute('y');
					} else {
						$pY = null;
					}
					if($tmpPageFooterNode->hasAttribute('wSize')) {
						$pWSize = $tmpPageFooterNode->getAttribute('wSize');
					} else {
						$pWSize = 0;
					}
					if($tmpPageFooterNode->hasAttribute('hSize')) {
						$pHSize = $tmpPageFooterNode->getAttribute('hSize');
					} else {
						$pHSize = 0;
					}
					if($tmpPageFooterNode->hasAttribute('display')) {
						$pDisplay = $tmpPageFooterNode->getAttribute('display');
					} else {
						$pDisplay = null;
					}
					if($tmpPageFooterNode->hasAttribute('fontFamily')) {
						$pFontFamily = $tmpPageFooterNode->getAttribute('fontFamily');
					} else {
						$pFontFamily = null;
					}
					if($tmpPageFooterNode->hasAttribute('fontStyle')) {
						$pFontStyle = $tmpPageFooterNode->getAttribute('fontStyle');
					} else {
						$pFontStyle = null;
					}
					if($tmpPageFooterNode->hasAttribute('fontSize')) {
						$pFontSize = $tmpPageFooterNode->getAttribute('fontSize');
					} else {
						$pFontSize = null;
					}
					if($tmpPageFooterNode->hasAttribute('align')) {
						$pAlign = $tmpPageFooterNode->getAttribute('align');
					} else {
						$pAlign = null;
					}
					if($tmpPageFooterNode->hasAttribute('textColor')) {
						$pTextColor = $tmpPageFooterNode->getAttribute('textColor');
					} else {
						$pTextColor = null;
					}
					if($tmpPageFooterNode->hasAttribute('fill')) {
						if(strncmp($tmpPageFooterNode->getAttribute('fill'),'enabled',strlen('enabled')) == 0) {
							$pFill = true;
						} else {
							$pFill = false;
						}
					} else {
						$pFill = false;
					}
					if($tmpPageFooterNode->hasAttribute('fillColor')) {
						$pFillColor = $tmpPageFooterNode->getAttribute('fillColor');
					} else {
						$pFillColor = null;
					}
					if($tmpPageFooterNode->hasAttribute('border')) {
						$pBorder = $tmpPageFooterNode->getAttribute('border');
					} else {
						$pBorder = null;
					}
					if($tmpPageFooterNode->hasAttribute('ln')) {
						$pLn = $tmpPageFooterNode->getAttribute('ln');
					} else {
						$pLn = null;
					}
					if($tmpPageFooterNode->hasAttribute('type')) {
						$pType = $tmpPageFooterNode->getAttribute('type');
					} else {
						$pType = null;
					}
					if($tmpPageFooterNode->hasAttribute('link')) {
						$pLink = $tmpPageFooterNode->getAttribute('link');
					} else {
						$pLink = null;
					}
					$tmpPDFTable->addPageFooter($pKind
							,$pX
							,$pY
							,$pWSize
							,$pHSize
							,$pDisplay
							,$pFontFamily
							,$pFontStyle
							,$pFontSize
							,$pAlign
							,$pTextColor
							,$pFill
							,$pFillColor
							,$pBorder
							,$pLn
							,$pType
							,$pLink
					);
				}
				$tmpRowNodes = $tmpPrintNode->getElementsByTagName('rowInfo');
				
				if($tmpPrintNode->hasAttribute('creator')) {
					$tmpPDFTable->SetCreator($tmpPrintNode->getAttribute('creator'));
				}
				if($tmpPrintNode->hasAttribute('author')) {
					$tmpPDFTable->SetAuthor($tmpPrintNode->getAttribute('author'));
				}
				if($tmpPrintNode->hasAttribute('subject')) {
					$tmpPDFTable->SetSubject($tmpPrintNode->getAttribute('subject'));
				}
				if($tmpPrintNode->hasAttribute('title')) {
					$tmpPDFTable->SetTitle($tmpPrintNode->getAttribute('title'));
				}
				$tmpPDFTable->setTableTitle($tmpNode->getAttribute('displayName'));
				$tmpPDFTable->setRepeatTableHeader(true);
				if($tmpRowNodes->length == 1) {
					$tmpRowNode = $tmpRowNodes->item(0);
					$tmpPDFTable->setRowInfo($tmpRowNode->getAttribute('headerHSize')
							,$tmpRowNode->getAttribute('bodyHSize'));
					if($tmpRowNode->hasAttribute('rowsPerPage')) {
						$tmpPDFTable->setRowsPerPage($tmpRowNode->getAttribute('rowsPerPage'));
					}
					if($tmpRowNode->hasAttribute('pageFooterStart')) {
						$tmpPDFTable->setPageFooterStart($tmpRowNode->getAttribute('pageFooterStart'));
					}
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