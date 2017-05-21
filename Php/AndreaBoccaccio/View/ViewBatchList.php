<?php
/*
 * phpmybatch - An open source batches of goods management system software.
 * Copyright (C)2012 Andrea Boccaccio
 * contact email: andrea@andreaboccaccio.it
 * 
 * This file is part of phpmybatch.
 * 
 * phpmybatch is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * phpmybatch is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with phpmywhs. If not, see <http://www.gnu.org/licenses/>.
 * 
 */
class Php_AndreaBoccaccio_View_ViewBatchList extends Php_AndreaBoccaccio_View_ViewConsistentListAbstract {

	private static $instance = null;

	private function __clone() {

	}

	private function __construct() {
		$this->setKind('batchList');
	}

	public static function getInstance() {
		if(self::$instance == null) {
			self::$instance = new Php_AndreaBoccaccio_View_ViewBatchList();
		}
		return self::$instance;
	}

	public function getMenu() {
		$ret = parent::getMenu();

		$ret .= "<div id=\"causeListMain\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=main\">Principale</a>";
		$ret .= "</div>\n";
		$ret .= "<div id=\"causeListMainCauseNew\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=batchWizard\">Commercializza Lotto</a>";
		$ret .= "</div>\n";
		$ret .= "</div>\n";

		return $ret;
	}

	public function getBody() {
		$ret = '';
		$requestedPage = 0;
		$win_out = null;
		$wname = null;
		$wdescription = null;
		$orderby = null;
		$myGP = array();
		$myWhere = array();
		$tmpRes = array();
		$causeMan = new Php_AndreaBoccaccio_Model_BatchManager();
		$tmpRes = array();
		$actualPage = -1;
		$rowsPerPage = -1;
		$totalRows = -1;
		$totalPages = -1;
		$filter = array();
		$causes = array();
		$i = -1;
		$max = count($causes);
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
		
		if(isset($myGP["wBatch"])) {
			if(trim(strlen($myGP["wBatch"]))>0) {
				$win_out = trim($myGP["wBatch"]);
				$filter["batch"] = trim($myGP["wBatch"]);
			}
			else {
				$win_out = null;
			}
		}
		else {
			$win_out = null;
		}
		if(isset($myGP["wVt_startFrom"])) {
			if(strlen(trim($myGP["wVt_startFrom"]))>0) {
				$wname = trim($myGP["wVt_startFrom"]);
				$filter["_f_vt_start"] = trim($myGP["wVt_startFrom"]);
			}
			else {
				$wname = null;
			}
		}
		else {
			$wname = null;
		}
		if(isset($myGP["wVt_startTo"])) {
			if(strlen(trim($myGP["wVt_startTo"]))>0) {
				$wname = trim($myGP["wVt_startTo"]);
				$filter["_t_vt_start"] = trim($myGP["wVt_startTo"]);
			}
			else {
				$wname = null;
			}
		}
		else {
			$wname = null;
		}
		if(isset($myGP["wVt_endFrom"])) {
			if(strlen(trim($myGP["wVt_endFrom"]))>0) {
				$wname = trim($myGP["wVt_endFrom"]);
				$filter["_f_vt_end"] = trim($myGP["wVt_endFrom"]);
			}
			else {
				$wname = null;
			}
		}
		else {
			$wname = null;
		}
		if(isset($myGP["wVt_endTo"])) {
			if(strlen(trim($myGP["wVt_endTo"]))>0) {
				$wname = trim($myGP["wVt_endTo"]);
				$filter["_t_vt_end"] = trim($myGP["wVt_endTo"]);
			}
			else {
				$wname = null;
			}
		}
		else {
			$wname = null;
		}
		if(isset($myGP["wDescription"])) {
			if(strlen(trim($myGP["wDescription"]))>0) {
				$wdescription = trim($myGP["wDescription"]);
				$filter["description"] = trim($myGP["wDescription"]);
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
		
		$tmpRes = $causeMan->getModels($requestedPage,$filter,$orderby);
		
		$actualPage = $tmpRes["actualPage"];
		$rowsPerPage = $tmpRes["rowsPerPage"];
		$totalRows = $tmpRes["totalRows"];
		$totalPages = $tmpRes["totalPages"];
		$causes = $tmpRes["result"];
		$max = count($causes);
		
		$ret .= "<div id=\"body\">";
		$ret .= "<div id=\"listCause\" class=\"list\">";
		$ret .= "<table id=\"tabListCause\" class=\"tab\">";
		$ret .= "<tr class=\"tab\">";
		$ret .= "<th class=\"tab\">";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
		$ret .= "?op=batchList&page=0" . $getWherePrefix . $myGetWhere;
		$ret .= "&orderby=" . $this->getNewOrder("batch") . "\"\">Lotto</a>";
		$ret .= "</th>";
		$ret .= "<th class=\"tab\">";
		$ret .= "Data di Arrivo";
		$ret .= "</th>";
		$ret .= "<th class=\"tab\">";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
		$ret .= "?op=batchList&page=0" . $getWherePrefix . $myGetWhere;
		$ret .= "&orderby=" . $this->getNewOrder("vt_start") . "\"\">Data Inizio Comm.</a>";
		$ret .= "</th>";
		$ret .= "<th class=\"tab\">";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
		$ret .= "?op=batchList&page=0" . $getWherePrefix . $myGetWhere;
		$ret .= "&orderby=" . $this->getNewOrder("vt_end") . "\"\">Data Fine Comm.</a>";
		$ret .= "</th>";
		$ret .= "<th class=\"tab\">cancellazione</th>";
		$ret .= "</tr>";
		for($i = 0; $i < $max; ++$i) {
			$ret .= "<tr class=\"tab\">";
			$ret .= "<td class=\"tab\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=batch&id=" . $causes[$i]->getVar("id");
			$ret .= "\">" . $causes[$i]->getVar("batch") ."</a>";
			$ret .= "</td>";
			$ret .= "<td class=\"tab\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=batch&id=" . $causes[$i]->getVar("id");
			$ret .= "\">";
			$ret .= $causeMan->getFirstArrival($causes[$i]->getVar("batch"));
			$ret .= "</a>";
			$ret .= "</td>";
			$ret .= "<td class=\"tab\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=batch&id=" . $causes[$i]->getVar("id");
			$ret .= "\">" . $causes[$i]->getVar("vt_start") ."</a>";
			$ret .= "</td>";
			$ret .= "<td class=\"tab\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=batch&id=" . $causes[$i]->getVar("id");
			$ret .= "\">" . $causes[$i]->getVar("vt_end") ."</a>";
			$ret .= "</td>";
			$ret .= "<td class=\"tab\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=batch&id=" . $causes[$i]->getVar("id");
			$ret .= "&delete=maybe\">cancella</a>";
			$ret .= "</td>";
			$ret .= "</tr>";
		}
		$ret .= "</table>";
		$ret .= "</div>";
		if($totalPages > 1) {
			$ret .= "<div id=\"causeListPaging\" class=\"paging\">";
			$ret .= "<div id=\"causeListFirstPage\" class=\"firstPage\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=batchList&page=0" . $getWherePrefix . $myGetWhere;
			$ret .= $getOrderPrefix . $myGetOrder . "\"\">Pagina 1</a>";
			$ret .= "</div>";
			$ret .= "<div id=\"causeListPrevPage\" class=\"prevPage\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=batchList&page=" . strval(max((intval($actualPage)-1),0)) . $getWherePrefix . $myGetWhere;
			$ret .= $getOrderPrefix . $myGetOrder . "\"\">Pagina " . strval((max((intval($actualPage)-1),0))+1) . "</a>";
			$ret .= "</div>";
			$ret .= "<div id=\"causeListActualPage\" class=\"actualPage\">";
			$ret .= "Pagina ";
			$ret .= strval(intval($actualPage)+1) . " di " . strval(intval($totalPages));
			$ret .= "</div>";
			$ret .= "<div id=\"causeListNextPage\" class=\"nextPage\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=batchList&page=" . strval(min((intval($actualPage)+1),(intval($totalPages)-1))) . $getWherePrefix . $myGetWhere;
			$ret .= $getOrderPrefix . $myGetOrder . "\"\">Pagina " . strval((min((intval($actualPage)+1),(intval($totalPages)-1)))+1) . "</a>";
			$ret .= "</div>";
			$ret .= "<div id=\"causeListLastPage\" class=\"lastPage\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=batchList&page=" . strval((intval($totalPages)-1)) . $getWherePrefix . $myGetWhere;
			$ret .= $getOrderPrefix . $myGetOrder . "\"\">Pagina " . strval(intval($totalPages)) . "</a>";
			$ret .= "</div>";
			$ret .= "</div>";
			$ret .= "<br />";
		}
		$ret .= "<div id=\"causeListWhere\" class=\"where\">";
		$ret .= "<form method=\"post\" action=\"";
		$ret .= $_SERVER["PHP_SELF"];
		$ret .= "?op=batchList&page=0\"> ";
		$ret .= "<div class=\"label\">Lotto:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"wBatch\"";
		if(array_key_exists("batch", $filter)) {
			$ret .= " value =\"";
			$ret .= $filter["batch"];
			$ret .= "\"/>";
		} else {
			$ret .= " />";
		}
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"label\">Data Inizio Comm. Dal :</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"wVt_startFrom\"";
		if(array_key_exists("_f_vt_start", $filter)) {
			$ret .= " value =\"";
			$ret .= $filter["_f_vt_start"];
			$ret .= "\"/>";
		} else {
			$ret .= " />";
		}
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"label\">Data Inizio Comm. Al :</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"wVt_startTo\"";
		if(array_key_exists("_t_vt_start", $filter)) {
			$ret .= " value =\"";
			$ret .= $filter["_t_vt_start"];
			$ret .= "\"/>";
		} else {
			$ret .= " />";
		}
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"label\">Data Fine Comm. Dal :</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"wVt_endFrom\"";
		if(array_key_exists("_f_vt_end", $filter)) {
			$ret .= " value =\"";
			$ret .= $filter["_f_vt_end"];
			$ret .= "\"/>";
		} else {
			$ret .= " />";
		}
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"label\">Data Fine Comm. Al :</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"wVt_endTo\"";
		if(array_key_exists("_t_vt_end", $filter)) {
			$ret .= " value =\"";
			$ret .= $filter["_t_vt_end"];
			$ret .= "\"/>";
		} else {
			$ret .= " />";
		}
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"label\">Descrizione:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"wDescription\"";
		if(array_key_exists("description", $filter)) {
			$ret .= " value =\"";
			$ret .= $filter["description"];
			$ret .= "\"/>";
		} else {
			$ret .= " />";
		}
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