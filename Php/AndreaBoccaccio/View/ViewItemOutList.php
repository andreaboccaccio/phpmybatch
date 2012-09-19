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
class Php_AndreaBoccaccio_View_ViewItemOutList extends Php_AndreaBoccaccio_View_ViewConsistentListAbstract {

	private static $instance = null;

	private function __clone() {

	}

	private function __construct() {
		$this->setKind('itemOutList');
	}

	public static function getInstance() {
		if(self::$instance == null) {
			self::$instance = new Php_AndreaBoccaccio_View_ViewItemOutList();
		}
		return self::$instance;
	}

	public function getMenu() {
		$ret = parent::getMenu();

		$ret .= "<div id=\"itemListMain\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=main\">Principale</a>";
		$ret .= "</div>\n";
		$ret .= "<div id=\"itemListItemNew\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=itemOutNew";
		$ret .= "\">Nuovo Scarico</a>";
		$ret .= "</div>\n";
		$ret .= "</div>\n";

		return $ret;
	}

	public function getBody() {
		$ret = '';
		$requestedPage = 0;
		$filter = array();
		$orderby = null;
		$myGP = array();
		$tmpRes = array();
		$itemDenormMan = new Php_AndreaBoccaccio_Model_ItemOutManager();
		$itemDenorms = array();
		$causeMan = new Php_AndreaBoccaccio_Model_CauseManager();
		$causes = array();
		$cause = new Php_AndreaBoccaccio_Model_Cause();
		$actualPage = -1;
		$rowsPerPage = -1;
		$totalRows = -1;
		$totalPages = -1;
		$i = -1;
		$max = count($itemDenorms);
		$myGetWhere = $this->getWhere();
		$getWherePrefix = '';
		$myGetOrder = $this->getOrder();
		$getOrderPrefix = '';
		$filterCauses = array('in_out' => 'O');
		$tmpArr = array();
		
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
		$myGP = array_merge($_GET,$_POST);
		
		if(isset($myGP["wCause"])) {
			if(intval($myGP["wCause"])>0) {
				if(preg_match("/^(?!.*(alter|create|drop|rename|truncate|call|delete|do|handler|insert|load|replace|select|update)).*$/i", $myGP["wCause"])) {
					$filter["cause"] = strval(intval($myGP["wCause"]));
				}
			}
		}
		if(isset($myGP["wKind"])) {
			if(trim(strlen($myGP["wKind"]))>0) {
				if(preg_match("/^(?!.*(alter|create|drop|rename|truncate|call|delete|do|handler|insert|load|replace|select|update)).*$/i", $_POST["wKind"])) {
					$filter["kind"] = trim($myGP["wKind"]);
				}
			}
		}
		if(isset($myGP["wCode"])) {
			if(strlen(trim($myGP["wCode"]))>0) {
				if(preg_match("/^(?!.*(alter|create|drop|rename|truncate|call|delete|do|handler|insert|load|replace|select|update)).*$/i", $_POST["wCode"])) {
					$filter["code"] = trim($myGP["wCode"]);
				}
			}
		}
		if(isset($myGP["wName"])) {
			if(strlen(trim($myGP["wName"]))>0) {
				if(preg_match("/^(?!.*(alter|create|drop|rename|truncate|call|delete|do|handler|insert|load|replace|select|update)).*$/i", $_POST["wName"])) {
					$filter["name"] = trim($myGP["wName"]);
				}
			}
		}
		if(isset($myGP["wQty"])) {
			if(strlen(trim($myGP["wQty"]))>0) {
				if(preg_match("/^(?!.*(alter|create|drop|rename|truncate|call|delete|do|handler|insert|load|replace|select|update)).*$/i", $_POST["wQty"])) {
					$filter["qty"] = trim($myGP["wQty"]);
				}
			}
		}
		if(isset($myGP["wProducer"])) {
			if(strlen(trim($myGP["wProducer"]))>0) {
				if(preg_match("/^(?!.*(alter|create|drop|rename|truncate|call|delete|do|handler|insert|load|replace|select|update)).*$/i", $_POST["wDescription"])) {
					$filter["producer"] = trim($myGP["wProducer"]);
				}
			}
		}
		if(isset($myGP["wYearProd"])) {
			if(strlen(trim($myGP["wYearProd"]))>0) {
				if(preg_match("/^(?!.*(alter|create|drop|rename|truncate|call|delete|do|handler|insert|load|replace|select|update)).*$/i", $_POST["wDescription"])) {
					$filter["yearProd"] = trim($myGP["wYearProd"]);
				}
			}
		}
		if(isset($myGP["wBatch"])) {
			if(strlen(trim($myGP["wBatch"]))>0) {
				if(preg_match("/^(?!.*(alter|create|drop|rename|truncate|call|delete|do|handler|insert|load|replace|select|update)).*$/i", $_POST["wDescription"])) {
					$filter["batch"] = trim($myGP["wBatch"]);
				}
			}
		}
		if(isset($myGP["wOwnBatch"])) {
			if(strlen(trim($myGP["wOwnBatch"]))>0) {
				if(preg_match("/^(?!.*(alter|create|drop|rename|truncate|call|delete|do|handler|insert|load|replace|select|update)).*$/i", $_POST["wDescription"])) {
					$filter["ownBatch"] = trim($myGP["wOwnBatch"]);
				}
			}
		}
		if(isset($myGP["wOwnDocumentYear"])) {
			if(strlen(trim($myGP["wOwnDocumentYear"]))>0) {
				if(preg_match("/^(?!.*(alter|create|drop|rename|truncate|call|delete|do|handler|insert|load|replace|select|update)).*$/i", $_POST["wDescription"])) {
					$filter["ownDocumentYear"] = trim($myGP["wOwnDocumentYear"]);
				}
			}
		}
		if(isset($myGP["wOwnDocumentNumber"])) {
			if(strlen(trim($myGP["wOwnDocumentYear"]))>0) {
				if(preg_match("/^(?!.*(alter|create|drop|rename|truncate|call|delete|do|handler|insert|load|replace|select|update)).*$/i", $_POST["wDescription"])) {
					$filter["wOwnDocumentNumber"] = trim($myGP["wOwnDocumentNumber"]);
				}
			}
		}
		if(isset($myGP["wDescription"])) {
			if(strlen(trim($myGP["wDescription"]))>0) {
				if(preg_match("/^(?!.*(alter|create|drop|rename|truncate|call|delete|do|handler|insert|load|replace|select|update)).*$/i", $_POST["wDescription"])) {
					$filter["description"] = trim($myGP["wDescription"]);
				}
			}
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
		
		$tmpRes = $itemDenormMan->getModels($requestedPage,$filter,$orderby);
		$actualPage = $tmpRes["actualPage"];
		$rowsPerPage = $tmpRes["rowsPerPage"];
		$totalRows = $tmpRes["totalRows"];
		$totalPages = $tmpRes["totalPages"];
		$itemDenorms = $tmpRes["result"];
		$max = count($itemDenorms);
		
		$ret .= "<div id=\"body\">";
		$ret .= "<div id=\"listDocKinds\" class=\"list\">";
		$ret .= "<table id=\"tabDocKinds\" class=\"tab\">";
		$ret .= "<tr class=\"tab\">";
		$ret .= "<th class=\"tab\">causale</th>";
		$ret .= "<th class=\"tab\">";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
		$ret .= "?op=itemOutList&page=0" . $getWherePrefix . $myGetWhere;
		$ret .= "&orderby=" . $this->getNewOrder("batch") . "\"\">Lotto</a>";
		$ret .= "</th>";
		$ret .= "<th class=\"tab\">";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
		$ret .= "?op=itemOutList&page=0" . $getWherePrefix . $myGetWhere;
		$ret .= "&orderby=" . $this->getNewOrder("kind") . "\"\">Categoria</a>";
		$ret .= "</th>";
		$ret .= "<th class=\"tab\">";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
		$ret .= "?op=itemOutList&page=0" . $getWherePrefix . $myGetWhere;
		$ret .= "&orderby=" . $this->getNewOrder("code") . "\"\">Codice</a>";
		$ret .= "</th>";
		$ret .= "<th class=\"tab\">";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
		$ret .= "?op=itemOutList&page=0" . $getWherePrefix . $myGetWhere;
		$ret .= "&orderby=" . $this->getNewOrder("name") . "\"\">Nome</a>";
		$ret .= "</th>";
		$ret .= "<th class=\"tab\">";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
		$ret .= "?op=itemOutList&page=0" . $getWherePrefix . $myGetWhere;
		$ret .= "&orderby=" . $this->getNewOrder("qty") . "\"\">Quantita'</a>";
		$ret .= "</th>";
		$ret .= "<th class=\"tab\">";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
		$ret .= "?op=itemOutList&page=0" . $getWherePrefix . $myGetWhere;
		$ret .= "&orderby=" . $this->getNewOrder("producer") . "\"\">Produttore</a>";
		$ret .= "</th>";
		$ret .= "<th class=\"tab\">";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
		$ret .= "?op=itemOutList&page=0" . $getWherePrefix . $myGetWhere;
		$ret .= "&orderby=" . $this->getNewOrder("yearProd") . "\"\">Anno di Produzione</a>";
		$ret .= "</th>";
		$ret .= "<th class=\"tab\">cancellazione</th>";
		$ret .= "</tr>";
		for($i = 0; $i < $max; ++$i) {
			$ret .= "<tr class=\"tab\">";
			$ret .= "<td class=\"tab\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=itemOut&id=" . $itemDenorms[$i]->getVar('id');
			$cause->loadFromDbById(intval($itemDenorms[$i]->getVar('cause')));
			$ret .= "\">" . $cause->getVar('name') ."</a>";
			$ret .= "</td>";
			$ret .= "<td class=\"tab\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=itemOut&id=" . $itemDenorms[$i]->getVar('id');
			$ret .= "\">" . $itemDenorms[$i]->getVar('batch') ."</a>";
			$ret .= "</td>";
			$ret .= "<td class=\"tab\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=itemOut&id=" . $itemDenorms[$i]->getVar('id');
			$ret .= "\">" . $itemDenorms[$i]->getVar('kind') ."</a>";
			$ret .= "</td>";
			$ret .= "<td class=\"tab\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=itemOut&id=" . $itemDenorms[$i]->getVar('id');
			$ret .= "\">" . $itemDenorms[$i]->getVar('code') ."</a>";
			$ret .= "</td>";
			$ret .= "<td class=\"tab\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=itemOut&id=" . $itemDenorms[$i]->getVar('id');
			$ret .= "\">" . $itemDenorms[$i]->getVar('name') ."</a>";
			$ret .= "</td>";
			$ret .= "<td class=\"tab\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=itemOut&id=" . $itemDenorms[$i]->getVar('id');
			$ret .= "\">" . $itemDenorms[$i]->getVar('qty') ."</a>";
			$ret .= "</td>";
			$ret .= "<td class=\"tab\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=itemOut&id=" . $itemDenorms[$i]->getVar('id');
			$ret .= "\">" . $itemDenorms[$i]->getVar('producer') ."</a>";
			$ret .= "</td>";
			$ret .= "<td class=\"tab\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=itemOut&id=" . $itemDenorms[$i]->getVar('id');
			$ret .= "\">" . $itemDenorms[$i]->getVar('yearProd') ."</a>";
			$ret .= "</td>";
			$ret .= "<td class=\"tab\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=itemOut&id=" . $itemDenorms[$i]->getVar('id');
			$ret .= "&delete=maybe\">cancella</a>";
			$ret .= "</td>";
			$ret .= "</tr>";
		}
		$ret .= "</table>";
		$ret .= "</div>";
		if($totalPages > 1) {
			$ret .= "<div id=\"listItemOutPaging\" class=\"paging\">";
			$ret .= "<div id=\"listItemOutFirstPage\" class=\"firstPage\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=itemOutList&page=0" . $getWherePrefix . $myGetWhere;
			$ret .= $getOrderPrefix . $myGetOrder . "\"\">Pagina 1</a>";
			$ret .= "</div>";
			$ret .= "<div id=\"listItemOutPrevPage\" class=\"prevPage\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=itemOutList&page=" . strval(max((intval($actualPage)-1),0)) . $getWherePrefix . $myGetWhere;
			$ret .= $getOrderPrefix . $myGetOrder . "\"\">Pagina " . strval((max((intval($actualPage)-1),0))+1) . "</a>";
			$ret .= "</div>";
			$ret .= "<div id=\"listItemOutActualPage\" class=\"actualPage\">";
			$ret .= "Pagina ";
			$ret .= strval(intval($actualPage)+1) . " di " . strval(intval($totalPages));
			$ret .= "</div>";
			$ret .= "<div id=\"listItemOutNextPage\" class=\"nextPage\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=itemOutList&page=" . strval(min((intval($actualPage)+1),(intval($totalPages)-1))) . $getWherePrefix . $myGetWhere;
			$ret .= $getOrderPrefix . $myGetOrder . "\"\">Pagina " . strval((min((intval($actualPage)+1),(intval($totalPages)-1)))+1) . "</a>";
			$ret .= "</div>";
			$ret .= "<div id=\"listItemOutLastPage\" class=\"lastPage\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=itemOutList&page=" . strval((intval($totalPages)-1)) . $getWherePrefix . $myGetWhere;
			$ret .= $getOrderPrefix . $myGetOrder . "\"\">Pagina " . strval(intval($totalPages)) . "</a>";
			$ret .= "</div>";
			$ret .= "</div>";
			$ret .= "<br />";
		}
		$ret .= "<div id=\"listItemWhere\" class=\"where\">";
		$ret .= "<form method=\"post\" action=\"";
		$ret .= $_SERVER["PHP_SELF"];
		$ret .= "?op=itemOutList&page=0\"> ";
		$ret .= "<div class=\"label\">Causale:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<select name=\"wCause\">";
		$tmpArr = $causeMan->getModels(null,$filterCauses,'name');
		$causes = $tmpArr["result"];
		foreach ($causes as $gotCause) {
			$ret .= "<option value=\"". $gotCause->getVar('id') . "\">". $gotCause->getVar('name') ."</option>";
		}
		$ret .= "</select>";
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"label\">Lotto:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"wBatch\" />";
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"label\">Proprio lotto:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"wOwnBatch\" />";
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"label\">Categoria:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"wKind\" />";
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"label\">Codice:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"wCode\" />";
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"label\">Nome:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"wName\" />";
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"label\">Produttore:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"wProducer\" />";
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"label\">Anno di produzione:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"wYearProd\" />";
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"label\">Anno proprio documento:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"wOwnDocumentYear\" />";
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"label\">Codice/numero proprio documento:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"wOwnDocumentCode\" />";
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"label\">Descrizione:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"wDescription\" />";
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"submit\">";
		$ret .= "<input type=\"submit\" value=\"Filtra\" />";
		$ret .= "</div>";
		$ret .= "</form>";
		$ret .= "</div>";
		$ret .= "</div>";

		return $ret;
	}
}