<?php
/*
 * phpmybatch - An open source batches of goods management system software.
 * Copyright (C)2012 Andrea Boccaccio
 * contact email: andrea@andreaboccaccio.com
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
 * along with phpmybatch. If not, see <http://www.gnu.org/licenses/>.
 * 
 */
class Php_AndreaBoccaccio_View_ViewStealBatch extends Php_AndreaBoccaccio_View_ViewConsistentListAbstract {

	private static $instance = null;

	private function __clone() {

	}

	private function __construct() {
		$this->setKind('batchSteal');
	}

	public static function getInstance() {
		if(self::$instance == null) {
			self::$instance = new Php_AndreaBoccaccio_View_ViewStealBatch();
		}
		return self::$instance;
	}

	public function getMenu() {
		$ret = parent::getMenu();

		$ret .= "<div id=\"itemListMain\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=main\">Principale</a>";
		$ret .= "</div>\n";
		$ret .= "<div id=\"itemListDoc\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=doc&id=";
		$ret .= $_GET["newDocId"];
		$ret .= "\">Documento</a>";
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
		$itemDenormMan = new Php_AndreaBoccaccio_Model_ItemManager();
		$itemDenorms = array();
		$actualPage = -1;
		$rowsPerPage = -1;
		$totalRows = -1;
		$totalPages = -1;
		$i = -1;
		$country = new Php_AndreaBoccaccio_Model_Country();
		$max = count($itemDenorms);
		$myGetWhere = $this->getWhere();
		$getWherePrefix = '';
		$myGetOrder = $this->getOrder();
		$getOrderPrefix = '';
		$oldDoc = new Php_AndreaBoccaccio_Model_Document();
		$newDoc = new Php_AndreaBoccaccio_Model_Document();
		$oldOk = FALSE;
		$newOK = FALSE;
		$stealBatch = new Php_AndreaBoccaccio_Model_StealBatch();
		$steal = FALSE;
		$res02 = array();

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

		if(isset($myGP["oldDocId"])) {
			if(intval($myGP["oldDocId"])>0) {
				if(preg_match("/^(?!.*(alter|create|drop|rename|truncate|call|delete|do|handler|insert|load|replace|select|update)).*$/i", $myGP["oldDocId"])) {
					$oldOk = TRUE;
					$filter["document"] = strval(intval($myGP["oldDocId"]));
					$oldDoc->loadFromDbById(intval($myGP["oldDocId"]));
				}
			}
		}
		if(isset($myGP["newDocId"])) {
			if(intval($myGP["newDocId"])>0) {
				if(preg_match("/^(?!.*(alter|create|drop|rename|truncate|call|delete|do|handler|insert|load|replace|select|update)).*$/i", $myGP["newDocId"])) {
					$newOK = TRUE;
					$newDoc->loadFromDbById(intval($myGP["newDocId"]));
				}
			}
		}
		if(isset($myGP["steal"])&&($newOK)&&($oldOk)) {
			if(strlen($myGP["steal"])>0) {
				if(strncasecmp($myGP["steal"], "yes", strlen("yes"))==0) {
					$steal = TRUE;
					$res02 = $stealBatch->steal($oldDoc->getVar("id"), $newDoc->getVar("id"));
				}
			}
		}
		if(isset($myGP["orderby"])&&(!$steal)) {
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
		
		if(!$steal) {
			$tmpRes = $itemDenormMan->getModels($requestedPage,$filter,$orderby);
			$actualPage = $tmpRes["actualPage"];
			$rowsPerPage = $tmpRes["rowsPerPage"];
			$totalRows = $tmpRes["totalRows"];
			$totalPages = $tmpRes["totalPages"];
			$itemDenorms = $tmpRes["result"];
			$max = count($itemDenorms);
		}

		$ret .= "<div id=\"body\">";
		if($steal) {
			if($res02["success"]) {
				$ret .= "<div>Trasferimento Lotti avvenuto correttamente.</div>";
			} else {
				$ret .= "<div>Errore nel trasferimento Lotti.</div>";
			}
		} else {
			$ret .= "<div>Vuoi trasferire i seguenti lotti:</div>";
			$ret .= "<div id=\"listDocKinds\" class=\"list\">";
			$ret .= "<table id=\"tabDocKinds\" class=\"tab\">";
			$ret .= "<tr class=\"tab\">";
			$ret .= "<th class=\"tab\">Nazione</th>";
			$ret .= "<th class=\"tab\">";
			$ret .= "Lotto";
			$ret .= "</th>";
			$ret .= "<th class=\"tab\">";
			$ret .= "Lotto Macellazione";
			$ret .= "</th>";
			$ret .= "<th class=\"tab\">";
			$ret .= "Categoria";
			$ret .= "</th>";
			$ret .= "<th class=\"tab\">";
			$ret .= "Kg";
			$ret .= "</th>";
			$ret .= "</tr>";
			for($i = 0; $i < $max; ++$i) {
				$ret .= "<tr class=\"tab\">";
				$ret .= "<td class=\"tab\">";
				$country->loadFromDbById(intval($itemDenorms[$i]->getVar('country')));
				$ret .= $country->getVar('codealpha2');
				$ret .= "</td>";
				$ret .= "<td class=\"tab\">";
				$ret .= $itemDenorms[$i]->getVar('batch');
				$ret .= "</td>";
				$ret .= "<td class=\"tab\">";
				$ret .= $itemDenorms[$i]->getVar('batch_orig');
				$ret .= "</td>";
				$ret .= "<td class=\"tab\">";
				$ret .= $itemDenorms[$i]->getVar('kind');
				$ret .= "</td>";
				$ret .= "<td class=\"tab\">";
				$ret .= number_format($itemDenorms[$i]->getVar('kg'),2,',','');
				$ret .= "</td>";
				$ret .= "</tr>";
			}
			$ret .= "</table>";
			$ret .= "</div>";
			if($totalPages > 1) {
				$ret .= "<div id=\"listItemPaging\" class=\"paging\">";
				$ret .= "<div id=\"listItemFirstPage\" class=\"firstPage\">";
				$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
				$ret .= "?op=batchSteal&newDocId=". strval(intval($myGP["newDocId"])) . "&oldDocId=" . strval(intval($myGP["oldDocId"])) ."&page=0" . $getWherePrefix . $myGetWhere;
				$ret .= $getOrderPrefix . $myGetOrder . "\"\">Pagina 1</a>";
				$ret .= "</div>";
				$ret .= "<div id=\"listItemPrevPage\" class=\"prevPage\">";
				$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
				$ret .= "?op=batchSteal&newDocId=". strval(intval($myGP["newDocId"])) . "&oldDocId=" . strval(intval($myGP["oldDocId"])) ."&page=" . strval(max((intval($actualPage)-1),0)) . $getWherePrefix . $myGetWhere;
				$ret .= $getOrderPrefix . $myGetOrder . "\"\">Pagina " . strval((max((intval($actualPage)-1),0))+1) . "</a>";
				$ret .= "</div>";
				$ret .= "<div id=\"listItemActualPage\" class=\"actualPage\">";
				$ret .= "Pagina ";
				$ret .= strval(intval($actualPage)+1) . " di " . strval(intval($totalPages));
				$ret .= "</div>";
				$ret .= "<div id=\"listItemNextPage\" class=\"nextPage\">";
				$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
				$ret .= "?op=batchSteal&newDocId=". strval(intval($myGP["newDocId"])) . "&oldDocId=" . strval(intval($myGP["oldDocId"])) ."&page=" . strval(min((intval($actualPage)+1),(intval($totalPages)-1))) . $getWherePrefix . $myGetWhere;
				$ret .= $getOrderPrefix . $myGetOrder . "\"\">Pagina " . strval((min((intval($actualPage)+1),(intval($totalPages)-1)))+1) . "</a>";
				$ret .= "</div>";
				$ret .= "<div id=\"listItemLastPage\" class=\"lastPage\">";
				$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
				$ret .= "?op=batchSteal&newDocId=". strval(intval($myGP["newDocId"])) . "&oldDocId=" . strval(intval($myGP["oldDocId"])) . "&page=" . strval((intval($totalPages)-1)) . $getWherePrefix . $myGetWhere;
				$ret .= $getOrderPrefix . $myGetOrder . "\"\">Pagina " . strval(intval($totalPages)) . "</a>";
				$ret .= "</div>";
				$ret .= "</div>";
				$ret .= "<br />";
			}
			$ret .= "<div id=\"oldDocInfo\" >";
			$ret .= "<div>Da questo documento:</div>";
			$ret .= "<table id=\"tabDocKinds\" class=\"tab\">";
			$ret .= "<tr class=\"tab\">";
			$ret .= "<th class=\"tab\">";
			$ret .= "data";
			$ret .= "</th>";
			$ret .= "<th class=\"tab\">";
			$ret .= "numero/codice";
			$ret .= "</th>";
			$ret .= "</th>";
			$ret .= "<th class=\"tab\">";
			$ret .= "fornitore";
			$ret .= "</th>";
			$ret .= "<th class=\"tab\">";
			$ret .= "tipo";
			$ret .= "</th>";
			$ret .= "</tr>";
			$ret .= "<tr>";
			$ret .= "<td class=\"tab\">";
			$ret .= $oldDoc->getVar("date");
			$ret .= "</td>";
			$ret .= "<td class=\"tab\">";
			$ret .= $oldDoc->getVar("code");
			$ret .= "</td>";
			$ret .= "<td class=\"tab\">";
			$ret .= $oldDoc->getVar("contractor");
			$ret .= "</td>";
			$ret .= "<td class=\"tab\">";
			$ret .= $oldDoc->getVar("kind");
			$ret .= "</td>";
			$ret .= "</tr>";
			$ret .= "</table>";
			$ret .= "</div>";
			$ret .= "<div id=\"newDocInfo\" >";
			$ret .= "<div>A questo documento:</div>";
			$ret .= "<table id=\"tabDocKinds\" class=\"tab\">";
			$ret .= "<tr class=\"tab\">";
			$ret .= "<th class=\"tab\">";
			$ret .= "data";
			$ret .= "</th>";
			$ret .= "<th class=\"tab\">";
			$ret .= "numero/codice";
			$ret .= "</th>";
			$ret .= "</th>";
			$ret .= "<th class=\"tab\">";
			$ret .= "fornitore";
			$ret .= "</th>";
			$ret .= "<th class=\"tab\">";
			$ret .= "tipo";
			$ret .= "</th>";
			$ret .= "</tr>";
			$ret .= "<tr>";
			$ret .= "<td class=\"tab\">";
			$ret .= $newDoc->getVar("date");
			$ret .= "</td>";
			$ret .= "<td class=\"tab\">";
			$ret .= $newDoc->getVar("code");
			$ret .= "</td>";
			$ret .= "<td class=\"tab\">";
			$ret .= $newDoc->getVar("contractor");
			$ret .= "</td>";
			$ret .= "<td class=\"tab\">";
			$ret .= $newDoc->getVar("kind");
			$ret .= "</td>";
			$ret .= "</tr>";
			$ret .= "</table>";
			$ret .= "</div>";
			$ret .= "<div id=\"stealBatchOk\" class=\"where\">";
			$ret .= "<form method=\"post\" action=\"";
			$ret .= $_SERVER["PHP_SELF"];
			$ret .= "?op=batchSteal&oldDocId=". $myGP["oldDocId"] ."&newDocId=". $myGP["newDocId"] ."&steal=yes\"> ";
			$ret .= "<div class=\"submit\">";
			$ret .= "<input type=\"submit\" value=\"Si sono sicuro, trasferisci\" />";
			$ret .= "</div>";
			$ret .= "</form>";
			$ret .= "</div>";
		}
		$ret .= "</div>";

		return $ret;
	}
}