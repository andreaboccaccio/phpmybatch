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
class Php_AndreaBoccaccio_View_ViewPDFSqlQueries extends Php_AndreaBoccaccio_View_ViewConsistentListAbstract {

	private static $instance = null;

	private function __clone() {

	}

	private function __construct() {
		$this->setKind('sqlQueries');
	}

	public static function getInstance() {
		if(self::$instance == null) {
			self::$instance = new Php_AndreaBoccaccio_View_ViewPDFSqlQueries();
		}
		return self::$instance;
	}

	public function getMenu() {
		$ret = parent::getMenu();

		$ret .= "<div id=\"docListMain\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=main\">Principale</a>";
		$ret .= "</div>\n";
		$ret .= "<div id=\"docListMainNewDoc\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=sqlQueries\">Interrogazioni</a>";
		$ret .= "</div>\n";
		$ret .= "</div>\n";

		return $ret;
	}

	public function getBody() {
		$getQueries = array();
		$getContent = array();
		$fields = array();
		$myGP = array();
		$filter = array();
		$tmpFilter = '';
		$nFields = -1;
		$rows = array();
		$nRows = -1;
		$tmpField = '';
		$orderby = '';
		$requestedPage = -1;
		$queryId = '';
		$selectQuery = 0;
		$printQuery = 0;
		$querySuccess = FALSE;
		$sqlQueriesManager = new Php_AndreaBoccaccio_Model_PDFXmlSqlQueriesManager();
		$setting = Php_AndreaBoccaccio_Settings_SettingsFactory::getInstance()->getSettings('xml');
		$myGetWhere = $this->getWhere();
		$getWherePrefix = '';
		$myGetOrder = $this->getOrder();
		$getOrderPrefix = '';
		$i = -1;
		$j = -1;
		$actualPage = -1;
		$rowsPerPage = -1;
		$totalRows = -1;
		$totalPages = -1;
		$dateLowLimCode = $setting->getSettingFromFullName('date.lowerLimitCode');
		$dateUpLimCode = $setting->getSettingFromFullName('date.upperLimitCode');
		$dates = preg_split('/,/', $setting->getSettingFromFullName('date.datesFields'));		
		$ret = '';
		
		if(strlen(trim($myGetWhere))>0) {
			$getWherePrefix = '&';
		}
		else {
			$getWherePrefix = '';
		}
		if(strlen(trim($myGetOrder))>0) {
			$getOrderPrefix = '&';
		}
		else {
			$getOrderPrefix = '';
		}
		
		$sqlQueriesManager->init();
		
		$myGP = array_merge($_GET,$_POST);
		
		if(isset($myGP["queryId"])) {
			if(trim(strlen($myGP["queryId"]))>0) {
				if(preg_match("/^(?!.*(alter|create|drop|rename|truncate|call|delete|do|handler|insert|load|replace|select|update)).*$/i", $myGP["queryId"])) {
					$selectQuery = 0;
					$queryId = trim($myGP["queryId"]);
					if(isset($_GET["page"])) {
						if(strlen($_GET["page"])>0) {
							$requestedPage = intval($_GET["page"]);
						}
						else {
							$requestedPage = 0;
						}
					}
					else {
						$requestedPage = 0;
					}
					if(isset($myGP["orderby"])) {
						if(strlen(trim($myGP["orderby"]))>0) {
							$orderby = trim($myGP["orderby"]);
						}
						else {
							$orderby = null;
						}
					}
					else {
						$orderby = null;
					}
					foreach($myGP as $tmpKey => $tmpValue) {
						if((strncmp($tmpKey, 'w', strlen('w'))==0)&&(strlen(trim($tmpValue))>0)) {
							if(preg_match("/^(?!.*(alter|create|drop|rename|truncate|call|delete|do|handler|insert|load|replace|select|update)).*$/i", $myGP[$tmpKey])) {
								$tmpFilter = substr($tmpKey,1);
								$filter[$tmpFilter] = trim($myGP[$tmpKey]);
							}
						}
					}
					if(isset($myGP["print"])) {
						if(strlen(trim($myGP["print"]))>0) {
							$printQuery = 1;
						}
						else {
							$printQuery = 0;
						}
					}
					else {
						$printQuery = 0;
					}
				}
				else {
					$selectQuery = 1;
				}
			}
			else {
				$selectQuery = 1;
			}
		}
		else {
			$selectQuery = 1;
		}
		
		$ret .= "<div id=\"body\">";
		if($selectQuery == 1) {
			$ret .= "<div id=\"sqlQueriesSelect\" class=\"where\">";
			$ret .= "<form method=\"post\" action=\"";
			$ret .= $_SERVER["PHP_SELF"];
			$ret .= "?op=sqlQueries\"> ";
			$ret .= "<div class=\"label\">Interrogazione:</div>";
			$ret .= "<div class=\"input\">";
			$ret .= "<select name=\"queryId\">";
			$getQueries = $sqlQueriesManager->getSqlQueries();
			foreach ($getQueries as $qId => $qDescr) {
				$ret .= "<option value=\"". $qId;
				$ret .= "\">". $qDescr ."</option>";
			}
			$ret .= "</select>";
			$ret .= "</div><br />";
			$ret .= "<div class=\"submit\">";
			$ret .= "<input type=\"submit\" value=\"Interroga\" />";
			$ret .= "</div>";
			$ret .= "</div>";
		}
		else if($printQuery == 0){
			$getContent = $sqlQueriesManager->getRes(trim($myGP["queryId"]),$requestedPage,$filter,$orderby);
			//var_dump($getContent);

			$querySuccess = $getContent["result"]["success"];
			if($querySuccess) {
				$actualPage = $getContent["actualPage"];
				$rowsPerPage = $getContent["rowsPerPage"];
				$totalRows = $getContent["totalRows"];
				$totalPages = $getContent["totalPages"];
				$fields = $getContent["result"]["fields"];
				$nFields = count($fields);
				$ret .= "<div>Interrogazione: " . $sqlQueriesManager->getDisplayName(trim($myGP["queryId"])) . "</div>";
				if($sqlQueriesManager->printable(trim($myGP["queryId"]))) {
					$ret .= "<div id=\"printLinkSqlQuery\">";
					$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
					$ret .= "?op=sqlQueries&queryId=" . trim($myGP["queryId"]) . "&print=1" . $getWherePrefix . $myGetWhere;
					$ret .= $getOrderPrefix . $myGetOrder . "\"\">Stampa PDF</a>";
					$ret .= "</div>";
					$ret .= "</div>";
				}
				$ret .= "<div id=\"listSqlQuery\" class=\"list\">";
				$ret .= "<table id=\"tabSqlQuery\" class=\"tab\">";
				$ret .= "<tr class=\"tab\">";
				for($i = 0; $i < $nFields; ++$i) {
					$ret .= "<th class=\"tab\">";
					$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
					$ret .= "?op=sqlQueries&page=0&queryId=" . trim($myGP["queryId"]) . $getWherePrefix . $myGetWhere;
					$ret .= "&orderby=" . $this->getNewOrder($fields[$i]) . "\"\">$fields[$i]</a>";
					$ret .= "</th>";
				}
				$ret .= "</tr>";
				$rows = $getContent["result"]["result"];
				$nRows = count($rows);
				for($i = 0; $i < $nRows; ++$i){
					$ret .= "<tr class=\"tab\">";
					for($j = 0; $j < $nFields; ++$j) {
						$tmpField = $fields[$j];
						$ret .= "<td class=\"tab\">";
						$ret .= $rows[$i][$tmpField];
						$ret .= "</td>";
					}
					$ret .= "</tr>";
				}
				$ret .= "</table>";
				$ret .= "</div>";
				if($totalPages > 1) {
					$ret .= "<div id=\"listDocPaging\" class=\"paging\">";
					$ret .= "<div id=\"listDocFirstPage\" class=\"firstPage\">";
					$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
					$ret .= "?op=sqlQueries&page=0&queryId=" . trim($myGP["queryId"]) . $getWherePrefix . $myGetWhere;
					$ret .= $getOrderPrefix . $myGetOrder . "\"\">Pagina 1</a>";
					$ret .= "</div>";
					$ret .= "<div id=\"listDocPrevPage\" class=\"prevPage\">";
					$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
					$ret .= "?op=sqlQueries&queryId=" . trim($myGP["queryId"]) . "&page=" . strval(max((intval($actualPage)-1),0)) . $getWherePrefix . $myGetWhere;
					$ret .= $getOrderPrefix . $myGetOrder . "\"\">Pagina " . strval((max((intval($actualPage)-1),0))+1) . "</a>";
					$ret .= "</div>";
					$ret .= "<div id=\"listDocActualPage\" class=\"actualPage\">";
					$ret .= "Pagina ";
					$ret .= strval(intval($actualPage)+1) . " di " . strval(intval($totalPages));
					$ret .= "</div>";
					$ret .= "<div id=\"listDocNextPage\" class=\"nextPage\">";
					$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
					$ret .= "?op=sqlQueries&queryId=" . trim($myGP["queryId"]) . "&page=" . strval(min((intval($actualPage)+1),(intval($totalPages)-1))) . $getWherePrefix . $myGetWhere;
					$ret .= $getOrderPrefix . $myGetOrder . "\"\">Pagina " . strval((min((intval($actualPage)+1),(intval($totalPages)-1)))+1) . "</a>";
					$ret .= "</div>";
					$ret .= "<div id=\"listDocLastPage\" class=\"lastPage\">";
					$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
					$ret .= "?op=sqlQueries&queryId=" . trim($myGP["queryId"]) . "&page=" . strval((intval($totalPages)-1)) . $getWherePrefix . $myGetWhere;
					$ret .= $getOrderPrefix . $myGetOrder . "\"\">Pagina " . strval(intval($totalPages)) . "</a>";
					$ret .= "</div>";
					$ret .= "</div>";
					$ret .= "<br />";
				}
				$ret .= "<div id=\"sqlQueriesWhere\" class=\"where\">";
				$ret .= "<form method=\"post\" action=\"";
				$ret .= $_SERVER["PHP_SELF"];
				$ret .= "?op=sqlQueries&queryId=" . trim($myGP["queryId"]) . "&page=0\"> ";
				for($i = 0; $i < $nFields; ++$i) {					
					if(in_array(strtoupper($fields[$i]),$dates)) {
						$ret .= "<div class=\"label\">" . $fields[$i] . " Dal :</div>";
						$ret .= "<div class=\"input\">";
						$ret .= "<input type=\"text\" name=\"w";
						$ret .= $dateLowLimCode;
						$ret .= $fields[$i] . "\" />";
						$ret .= "</div>";
						$ret .= "<br />";
						$ret .= "<div class=\"label\">" . $fields[$i] . " Al :</div>";
						$ret .= "<div class=\"input\">";
						$ret .= "<input type=\"text\" name=\"w";
						$ret .= $dateUpLimCode;
						$ret .= $fields[$i] . "\" />";
						$ret .= "</div>";
						$ret .= "<br />";
					} else {
						$ret .= "<div class=\"label\">" . $fields[$i] . ":</div>";
						$ret .= "<div class=\"input\">";
						$ret .= "<input type=\"text\" name=\"w" . $fields[$i] . "\" />";
						$ret .= "</div>";
						$ret .= "<br />";
					}
				}
				$ret .= "<div class=\"submit\">";
				$ret .= "<input type=\"submit\" value=\"Filtra\" />";
				$ret .= "</div>";
				$ret .= "</form>";
				$ret .= "</div>";
			}
			else {
				$ret .= "Errore nell'interrogazione " . trim($myGP["queryId"]);
			}
		} else {
			$sqlQueriesManager->getPDF(trim($myGP["queryId"]),$filter,$orderby);
		}
		$ret .= "</div>";
		
		return $ret;
	}
}