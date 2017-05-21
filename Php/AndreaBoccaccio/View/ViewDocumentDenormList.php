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
class Php_AndreaBoccaccio_View_ViewDocumentDenormList extends Php_AndreaBoccaccio_View_ViewConsistentAbstract {

	private static $instance = null;

	private function __clone() {

	}

	private function __construct() {
		$this->setKind('docList');
	}

	public static function getInstance() {
		if(self::$instance == null) {
			self::$instance = new Php_AndreaBoccaccio_View_ViewDocumentDenormList();
		}
		return self::$instance;
	}

	public function getMenu() {
		$ret = parent::getMenu();

		$ret .= "<div id=\"docListMain\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=main\">Principale</a>";
		$ret .= "</div>\n";
		$ret .= "<div id=\"docListMainNewDoc\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=docNew\">Nuovo Documento</a>";
		$ret .= "</div>\n";
		$ret .= "</div>\n";

		return $ret;
	}

	public function getBody() {
		$ret = '';
		$requestedPage = 0;
		$wyear = null;
		$wkind = null;
		$wcode = null;
		$wcontractor_kind = null;
		$wcontractor_code = null;
		$wcontractor = null;
		$wwarehouse = null;
		$wdate = null;
		$wvt_start = null;
		$wvt_end = null;
		$wdescription = null;
		$orderby = null;
		$myGP = array();
		$myWhere = array();
		$tmpRes = array();
		$docDenormMan = new Php_AndreaBoccaccio_Model_DocumentDenormManager();
		$tmpRes = array();
		$actualPage = -1;
		$rowsPerPage = -1;
		$totalRows = -1;
		$totalPages = -1;
		$docDenorms = array();
		$i = -1;
		$max = count($docDenorms);
		$myGetWhere = $this->getWhere();
		$getWherePrefix = '';
		$myGetOrder = $this->getOrder();
		$getOrderPrefix = '';
		
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
		
		if(isset($myGP["wYear"])) {
			if(trim(strlen($myGP["wYear"]))>0) {
				$wyear = trim($myGP["wYear"]);
			}
			else {
				$wyear = null;
			}
		}
		else {
			$wyear = null;
		}
		if(isset($myGP["wKind"])) {
			if(strlen(trim($myGP["wKind"]))>0) {
				$wkind = trim($myGP["wKind"]);
			}
			else {
				$wkind = null;
			}
		}
		else {
			$wkind = null;
		}
		if(isset($myGP["wCode"])) {
			if(strlen(trim($myGP["wCode"]))>0) {
				$wcode = trim($myGP["wCode"]);
			}
			else {
				$wcode = null;
			}
		}
		else {
			$wcode = null;
		}
		if(isset($myGP["wContractor_kind"])) {
			if(strlen(trim($myGP["wContractor_kind"]))>0) {
				$wcontractor_kind = trim($myGP["wContractor_kind"]);
			}
			else {
				$wcontractor_kind = null;
			}
		}
		else {
			$wcontractor_kind = null;
		}
		if(isset($myGP["wContractor_code"])) {
			if(strlen(trim($myGP["wContractor_code"]))>0) {
				$wcontractor_code = trim($myGP["wContractor_code"]);
			}
			else {
				$wcontractor_code = null;
			}
		}
		else {
			$wcontractor_code = null;
		}
		if(isset($myGP["wContractor"])) {
			if(strlen(trim($myGP["wContractor"]))>0) {
				$wcontractor = trim($myGP["wContractor"]);
			}
			else {
				$wcontractor = null;
			}
		}
		else {
			$wcontractor = null;
		}
		if(isset($myGP["wWarehouse"])) {
			if(strlen(trim($myGP["wWarehouse"]))>0) {
				$wwarehouse = trim($myGP["wWarehouse"]);
			}
			else {
				$wwarehouse = null;
			}
		}
		else {
			$wwarehouse = null;
		}
		if(isset($myGP["wDescription"])) {
			if(strlen(trim($myGP["wDescription"]))>0) {
				$wdescription = trim($myGP["wDescription"]);
			}
			else {
				$wdescription = null;
			}
		}
		else {
			$wdescription = null;
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
		
		$tmpRes = $docDenormMan->getDocs($requestedPage
				,$wyear
				,$wkind
				,$wcode
				,$wcontractor_kind
				,$wcontractor_code
				,$wcontractor
				,$wwarehouse
				,$wdate
				,$wvt_start
				,$wvt_end
				,$wdescription
				,$orderby
				);
		$actualPage = $tmpRes["actualPage"];
		$rowsPerPage = $tmpRes["rowsPerPage"];
		$totalRows = $tmpRes["totalRows"];
		$totalPages = $tmpRes["totalPages"];
		$docDenorms = $tmpRes["result"];
		$max = count($docDenorms);
		
		$ret .= "<div id=\"body\">";
		$ret .= "<div id=\"listDocKinds\" class=\"list\">";
		$ret .= "<table id=\"tabDocKinds\" class=\"tab\">";
		$ret .= "<tr class=\"tab\">";
		$ret .= "<th class=\"tab\">";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
		$ret .= "?op=docList&page=0" . $getWherePrefix . $myGetWhere;
		$ret .= "&orderby=" . $this->getNewOrder("year") . "\"\">anno</a>";
		$ret .= "</th>";
		$ret .= "<th class=\"tab\">";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
		$ret .= "?op=docList&page=0" . $getWherePrefix . $myGetWhere;
		$ret .= "&orderby=" . $this->getNewOrder("kind") . "\"\">tipo</a>";
		$ret .= "</th>";
		$ret .= "<th class=\"tab\">";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
		$ret .= "?op=docList&page=0" . $getWherePrefix . $myGetWhere;
		$ret .= "&orderby=" . $this->getNewOrder("contractor") . "\"\">contraente</a>";
		$ret .= "</th>";
		$ret .= "<th class=\"tab\">";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
		$ret .= "?op=docList&page=0" . $getWherePrefix . $myGetWhere;
		$ret .= "&orderby=" . $this->getNewOrder("code") . "\"\">numero/codice</a>";
		$ret .= "</th>";
		$ret .= "<th class=\"tab\">cancellazione</th>";
		$ret .= "</tr>";
		for($i = 0; $i < $max; ++$i) {
			$ret .= "<tr class=\"tab\">";
			$ret .= "<td class=\"tab\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=doc&id=" . $docDenorms[$i]->getId();
			$ret .= "\">" . $docDenorms[$i]->getYear() ."</a>";
			$ret .= "</td>";
			$ret .= "<td class=\"tab\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=doc&id=" . $docDenorms[$i]->getId();
			$ret .= "\">" . $docDenorms[$i]->getKind() ."</a>";
			$ret .= "</td>";
			$ret .= "<td class=\"tab\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=doc&id=" . $docDenorms[$i]->getId();
			$ret .= "\">" . $docDenorms[$i]->getContractor() ."</a>";
			$ret .= "</td>";
			$ret .= "<td class=\"tab\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=doc&id=" . $docDenorms[$i]->getId();
			$ret .= "\">" . $docDenorms[$i]->getCode() ."</a>";
			$ret .= "</td>";
			$ret .= "<td class=\"tab\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=doc&id=" . $docDenorms[$i]->getId();
			$ret .= "&delete=maybe\">cancella</a>";
			$ret .= "</td>";
			$ret .= "</tr>";
		}
		$ret .= "</table>";
		$ret .= "</div>";
		if($totalPages > 1) {
			$ret .= "<div id=\"listDocPaging\" class=\"paging\">";
			$ret .= "<div id=\"listDocFirstPage\" class=\"firstPage\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=docList&page=0" . $getWherePrefix . $myGetWhere;
			$ret .= $getOrderPrefix . $myGetOrder . "\"\">Pagina 1</a>";
			$ret .= "</div>";
			$ret .= "<div id=\"listDocPrevPage\" class=\"prevPage\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=docList&page=" . strval(max((intval($actualPage)-1),0)) . $getWherePrefix . $myGetWhere;
			$ret .= $getOrderPrefix . $myGetOrder . "\"\">Pagina " . strval((max((intval($actualPage)-1),0))+1) . "</a>";
			$ret .= "</div>";
			$ret .= "<div id=\"listDocActualPage\" class=\"actualPage\">";
			$ret .= "Pagina ";
			$ret .= strval(intval($actualPage)+1) . " di " . strval(intval($totalPages));
			$ret .= "</div>";
			$ret .= "<div id=\"listDocNextPage\" class=\"nextPage\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=docList&page=" . strval(min((intval($actualPage)+1),(intval($totalPages)-1))) . $getWherePrefix . $myGetWhere;
			$ret .= $getOrderPrefix . $myGetOrder . "\"\">Pagina " . strval((min((intval($actualPage)+1),(intval($totalPages)-1)))+1) . "</a>";
			$ret .= "</div>";
			$ret .= "<div id=\"listDocLastPage\" class=\"lastPage\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=docList&page=" . strval((intval($totalPages)-1)) . $getWherePrefix . $myGetWhere;
			$ret .= $getOrderPrefix . $myGetOrder . "\"\">Pagina " . strval(intval($totalPages)) . "</a>";
			$ret .= "</div>";
			$ret .= "</div>";
			$ret .= "<br />";
		}
		$ret .= "<div id=\"listDocWhere\" class=\"where\">";
		$ret .= "<form method=\"post\" action=\"";
		$ret .= $_SERVER["PHP_SELF"];
		$ret .= "?op=docList&page=0\"> ";
		$ret .= "<div class=\"label\">Anno:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"wYear\" />";
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"label\">Tipo Documento:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"wKind\" />";
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"label\">Numero/Codice:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"wCode\" />";
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"label\">Tipo Contraente:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"wContractor_kind\" />";
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"label\">P.IVA/CF Contraente:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"wContractor_code\" />";
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"label\">Contraente:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"wContractor\" />";
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"label\">Magazzino:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"wWarehouse\" />";
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
	
	private function getVariableStarting($startString) {
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
	
	private function getWhere() {
		
		return $this->getVariableStarting("w");
	}
	
	private function getOrder() {
		return $this->getVariableStarting("ord");
	}
	
	private function getNewOrder($newOrder) {
		$ret = $newOrder;
		$cmp = '';
		
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
		if(strcmp($newOrder, $cmp)==0) {
			$ret = $newOrder . " DESC";
		}
		else {
			$ret = $newOrder;
		}
		
		return $ret;
	}
}