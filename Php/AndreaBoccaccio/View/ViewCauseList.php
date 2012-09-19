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
class Php_AndreaBoccaccio_View_ViewCauseList extends Php_AndreaBoccaccio_View_ViewConsistentListAbstract {

	private static $instance = null;

	private function __clone() {

	}

	private function __construct() {
		$this->setKind('causeList');
	}

	public static function getInstance() {
		if(self::$instance == null) {
			self::$instance = new Php_AndreaBoccaccio_View_ViewCauseList();
		}
		return self::$instance;
	}

	public function getMenu() {
		$ret = parent::getMenu();

		$ret .= "<div id=\"causeListMain\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=main\">Principale</a>";
		$ret .= "</div>\n";
		$ret .= "<div id=\"causeListMainCauseNew\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=causeNew\">Nuova Causale</a>";
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
		$causeMan = new Php_AndreaBoccaccio_Model_CauseManager();
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
		
		if(isset($myGP["wIn_out"])) {
			if(trim(strlen($myGP["wIn_out"]))>0) {
				$win_out = trim($myGP["wIn_out"]);
				$filter["in_out"] = trim($myGP["wIn_out"]);
			}
			else {
				$win_out = null;
			}
		}
		else {
			$win_out = null;
		}
		if(isset($myGP["wName"])) {
			if(strlen(trim($myGP["wName"]))>0) {
				$wname = trim($myGP["wName"]);
				$filter["name"] = trim($myGP["wName"]);
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
		$ret .= "?op=causeList&page=0" . $getWherePrefix . $myGetWhere;
		$ret .= "&orderby=" . $this->getNewOrder("in_out") . "\"\">Carico/Scarico</a>";
		$ret .= "</th>";
		$ret .= "<th class=\"tab\">";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
		$ret .= "?op=causeList&page=0" . $getWherePrefix . $myGetWhere;
		$ret .= "&orderby=" . $this->getNewOrder("name") . "\"\">Causale</a>";
		$ret .= "</th>";
		$ret .= "<th class=\"tab\">cancellazione</th>";
		$ret .= "</tr>";
		for($i = 0; $i < $max; ++$i) {
			$ret .= "<tr class=\"tab\">";
			$ret .= "<td class=\"tab\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=cause&id=" . $causes[$i]->getVar("id");
			$ret .= "\">";
			if(strcmp($causes[$i]->getVar("in_out"),"I")==0) {
				$ret .= "Carico";
			} else {
				$ret .= "Scarico";
			}
			$ret .= "</a>";
			$ret .= "</td>";
			$ret .= "<td class=\"tab\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=cause&id=" . $causes[$i]->getVar("id");
			$ret .= "\">" . $causes[$i]->getVar("name") ."</a>";
			$ret .= "</td>";
			$ret .= "<td class=\"tab\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=cause&id=" . $causes[$i]->getVar("id");
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
			$ret .= "?op=causeList&page=0" . $getWherePrefix . $myGetWhere;
			$ret .= $getOrderPrefix . $myGetOrder . "\"\">Pagina 1</a>";
			$ret .= "</div>";
			$ret .= "<div id=\"causeListPrevPage\" class=\"prevPage\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=causeList&page=" . strval(max((intval($actualPage)-1),0)) . $getWherePrefix . $myGetWhere;
			$ret .= $getOrderPrefix . $myGetOrder . "\"\">Pagina " . strval((max((intval($actualPage)-1),0))+1) . "</a>";
			$ret .= "</div>";
			$ret .= "<div id=\"causeListActualPage\" class=\"actualPage\">";
			$ret .= "Pagina ";
			$ret .= strval(intval($actualPage)+1) . " di " . strval(intval($totalPages));
			$ret .= "</div>";
			$ret .= "<div id=\"causeListNextPage\" class=\"nextPage\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=causeList&page=" . strval(min((intval($actualPage)+1),(intval($totalPages)-1))) . $getWherePrefix . $myGetWhere;
			$ret .= $getOrderPrefix . $myGetOrder . "\"\">Pagina " . strval((min((intval($actualPage)+1),(intval($totalPages)-1)))+1) . "</a>";
			$ret .= "</div>";
			$ret .= "<div id=\"causeListLastPage\" class=\"lastPage\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=causeList&page=" . strval((intval($totalPages)-1)) . $getWherePrefix . $myGetWhere;
			$ret .= $getOrderPrefix . $myGetOrder . "\"\">Pagina " . strval(intval($totalPages)) . "</a>";
			$ret .= "</div>";
			$ret .= "</div>";
			$ret .= "<br />";
		}
		$ret .= "<div id=\"causeListWhere\" class=\"where\">";
		$ret .= "<form method=\"post\" action=\"";
		$ret .= $_SERVER["PHP_SELF"];
		$ret .= "?op=causeList&page=0\"> ";
		$ret .= "<div class=\"label\">Carico/Scarico:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<select name=\"wIn_out\">";
		if(!array_key_exists("in_out", $filter)) {
			$ret .= "<option value=\"\" selected=\"selected\"> </option>";
			$ret .= "<option value=\"I\">Carico</option>";
			$ret .= "<option value=\"O\">Scarico</option>";
		} else if(strcmp($filter["in_out"],"I")==0){
			$ret .= "<option value=\"\"> </option>";
			$ret .= "<option value=\"I\" selected=\"selected\">Carico</option>";
			$ret .= "<option value=\"O\">Scarico</option>";
		}  else if(strcmp($filter["in_out"],"O")==0){
			$ret .= "<option value=\"\"> </option>";
			$ret .= "<option value=\"I\">Carico</option>";
			$ret .= "<option value=\"O\" selected=\"selected\">Scarico</option>";
		}
		$ret .= "</select>";
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"label\">Nome:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"wName\"";
		if(array_key_exists("name", $filter)) {
			$ret .= " value =\"";
			$ret .= $filter["name"];
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