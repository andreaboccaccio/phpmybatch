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
abstract class Php_AndreaBoccaccio_View_ViewParamsWizardAbstract extends Php_AndreaBoccaccio_View_ViewWizardAbstract {
	
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
		$fieldsToInsert = array();
		$fieldsFilter = array();
		$nFieldsFilter = -1;
		$tmpArrayKeys = array();
		$tmpParams = array();
		$tmpField = '';
		$orderby = '';
		$requestedPage = -1;
		$tmpQueryId = '';
		$selectQuery = 0;
		$querySuccess = FALSE;
		$tmpWizard = $this->getWizard();
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
		$linkHref = '';
		$ret = '';
		$tmpArrKey = '';
	
		$tmpQueryId = $this->getQueryId();
		$selectQuery = 0;
	
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
	
		$tmpWizard->init();
		$fieldsToInsert = $tmpWizard->getFieldsMapping();
		$tmpParams = $tmpWizard->getParams();
	
		$myGP = array_merge($_GET,$_POST);
	
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
			} else if (strncmp($tmpKey, 'orderby', strlen('orderby'))!=0) {
				if(array_key_exists($tmpKey, $tmpParams)) {
					$this->setCustomGet($this->getCustomGet() . "&" . $tmpParams[$tmpKey] . "=" . $tmpValue);
				}
			}
		}
	
		$ret .= "<div id=\"body\">";
	
		$getContent = $tmpWizard->getRes($tmpQueryId,$requestedPage,$filter,$orderby);
		//var_dump($getContent);
	
	
		$querySuccess = $getContent["result"]["success"];
		if($querySuccess) {
			$actualPage = $getContent["actualPage"];
			$rowsPerPage = $getContent["rowsPerPage"];
			$totalRows = $getContent["totalRows"];
			$totalPages = $getContent["totalPages"];
			$fields = $getContent["result"]["fields"];
			$fields = $tmpWizard->getFieldsView();
			$nFields = count($fields);
			$tmpArrayKeys = array_keys($fields);
			$ret .= "<div>Seleziona " . $tmpWizard->getDisplayName($tmpQueryId) . "</div>";
			$ret .= "<div id=\"listItemOutWizard\" class=\"list\">";
			$ret .= "<table id=\"tabItemOutWizard\" class=\"tab\">";
			$ret .= "<tr class=\"tab\">";
			for($i = 0; $i < $nFields; ++$i) {
				$tmpArrKey = $tmpArrayKeys[$i];
				$ret .= "<th class=\"tab\">";
				$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
				$ret .= "?op=" . $this->getKind() . $this->getCustomGet() . "&page=0&queryId=" . $tmpQueryId . $getWherePrefix . $myGetWhere;
				$ret .= "&orderby=" . $this->getNewOrder($tmpArrKey) . "\"\">$fields[$tmpArrKey]</a>";
				$ret .= "</th>";
			}
			$ret .= "</tr>";
			$rows = $getContent["result"]["result"];
			$nRows = count($rows);
			for($i = 0; $i < $nRows; ++$i){
				$linkHref = $_SERVER["PHP_SELF"];
				$linkHref .= "?op=" . $tmpWizard->getInsView();
				$linkHref .= $this->getCustomGet();
				foreach ($fieldsToInsert as $fieldSql => $fieldToInsert) {
					$linkHref .= "&" . $fieldToInsert . "=" . $rows[$i][$fieldSql];
				}
				$ret .= "<tr class=\"tab\">";
				for($j = 0; $j < $nFields; ++$j) {
					$tmpField = $tmpArrayKeys[$j];
					$ret .= "<td class=\"tab\">";
					$ret .= "<a href=\"" . $linkHref . "\">";
					$ret .= $rows[$i][$tmpField];
					$ret .= "</a>";
					$ret .= "</td>";
				}
				$ret .= "</tr>";
			}
			$ret .= "</table>";
			$ret .= "</div>";
			if($totalPages > 1) {
				$ret .= "<div id=\"itemOutWizardPaging\" class=\"paging\">";
				$ret .= "<div id=\"itemOutWizardFirstPage\" class=\"firstPage\">";
				$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
				$ret .= "?op=" . $this->getKind() . $this->getCustomGet() . "&page=0&queryId=" . $tmpQueryId . $getWherePrefix . $myGetWhere;
				$ret .= $getOrderPrefix . $myGetOrder . "\"\">Pagina 1</a>";
				$ret .= "</div>";
				$ret .= "<div id=\"itemOutWizardPrevPage\" class=\"prevPage\">";
				$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
				$ret .= "?op=" . $this->getKind() . $this->getCustomGet() . "&queryId=" . $tmpQueryId . "&page=" . strval(max((intval($actualPage)-1),0)) . $getWherePrefix . $myGetWhere;
				$ret .= $getOrderPrefix . $myGetOrder . "\"\">Pagina " . strval((max((intval($actualPage)-1),0))+1) . "</a>";
				$ret .= "</div>";
				$ret .= "<div id=\"itemOutWizardActualPage\" class=\"actualPage\">";
				$ret .= "Pagina ";
				$ret .= strval(intval($actualPage)+1) . " di " . strval(intval($totalPages));
				$ret .= "</div>";
				$ret .= "<div id=\"itemOutWizardNextPage\" class=\"nextPage\">";
				$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
				$ret .= "?op=" . $this->getKind() . $this->getCustomGet() . "&queryId=" . $tmpQueryId . "&page=" . strval(min((intval($actualPage)+1),(intval($totalPages)-1))) . $getWherePrefix . $myGetWhere;
				$ret .= $getOrderPrefix . $myGetOrder . "\"\">Pagina " . strval((min((intval($actualPage)+1),(intval($totalPages)-1)))+1) . "</a>";
				$ret .= "</div>";
				$ret .= "<div id=\"itemOutWizardLastPage\" class=\"lastPage\">";
				$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
				$ret .= "?op=" . $this->getKind() . $this->getCustomGet() . "&queryId=" . $tmpQueryId . "&page=" . strval((intval($totalPages)-1)) . $getWherePrefix . $myGetWhere;
				$ret .= $getOrderPrefix . $myGetOrder . "\"\">Pagina " . strval(intval($totalPages)) . "</a>";
				$ret .= "</div>";
				$ret .= "</div>";
				$ret .= "<br />";
			}
			$ret .= "<div id=\"itemOutWizardWhere\" class=\"where\">";
			$ret .= "<form method=\"post\" action=\"";
			$ret .= $_SERVER["PHP_SELF"];
			$ret .= "?op=" . $this->getKind() . $this->getCustomGet() . "&queryId=" . $tmpQueryId . "&page=0\"> ";
			$fieldsFilter = $tmpWizard->getFieldsFilter();
			$nFieldsFilter = count($fieldsFilter);
			$tmpArrayKeys = array_keys($fieldsFilter);
			for($i = 0; $i < $nFieldsFilter; ++$i) {
				$tmpArrKey = $tmpArrayKeys[$i];
				$ret .= "<div class=\"label\">" . $fieldsFilter[$tmpArrKey] . ":</div>";
				$ret .= "<div class=\"input\">";
				$ret .= "<input type=\"text\" name=\"w" . $tmpArrayKeys[$i] . "\" />";
				$ret .= "</div>";
				$ret .= "<br />";
			}
			$ret .= "<div class=\"submit\">";
			$ret .= "<input type=\"submit\" value=\"Filtra\" />";
			$ret .= "</div>";
			$ret .= "</form>";
			$ret .= "</div>";
		}
		else {
			$ret .= "Errore nel wizard " . $tmpQueryId;
		}
		$ret .= "</div>";
	
		return $ret;
	}
}