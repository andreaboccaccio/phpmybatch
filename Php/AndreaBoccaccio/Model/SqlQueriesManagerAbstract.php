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
abstract class Php_AndreaBoccaccio_Model_SqlQueriesManagerAbstract implements Php_AndreaBoccaccio_Model_SqlQueriesManagerInterface {
	
	private $sqlQueries = array();
	
	protected function addQuery($queryId, $displayName, $strSQL) {
		$tmpQuery = array();
		
		if(!array_key_exists($queryId, $this->sqlQueries)) {
			$tmpQuery["displayName"] = $displayName;
			$tmpQuery["strSQL"] = $strSQL;
			$this->sqlQueries[$queryId] = $tmpQuery;
		}
	}
	
	protected function getQuery($queryId) {
		$ret = '';
		
		if(array_key_exists($queryId, $this->sqlQueries)) {
			$ret = $this->sqlQueries[$queryId]["strSQL"];
		}
		
		return $ret;
	}
	
	public function getSqlQueries() {
		$ret = array();
		
		foreach ($this->sqlQueries as $queryId => $sqlQuery) {
			$ret[$queryId] = $sqlQuery["displayName"];
		}
		
		return $ret;
	}
	
	public function getDisplayName($queryId) {
		$ret = '';
		
		if(array_key_exists($queryId, $this->sqlQueries)) {
			$ret = $this->sqlQueries[$queryId]["displayName"];
		}
		
		return $ret;
	}
	
	public function getRes($queryId, $requestedPage=0, &$filter=null, $orderby=null) {
		$ret = array();
		$tmpArray = array();
		$tmpModel;
		$tmpRow;
		$i = -1;
		$where = 0;
		$setting = Php_AndreaBoccaccio_Settings_SettingsFactory::getInstance()->getSettings('xml');
		$db = Php_AndreaBoccaccio_Db_DbFactory::getInstance()->getDb($setting->getSettingFromFullName('classes.db'));
		$rowsPerPage = $setting->getSettingFromFullName('memory.rowsPerPage');
		$dateFormat = $setting->getSettingFromFullName('date.sqlFormat');
		$strSQLCount = "SELECT COUNT(*) AS totalRows, CEIL(COUNT(*)/";
		$strSQL = "SELECT T01.* FROM (";
		$strSQLOptional = '';
		$strSQLOrderBy = '';
		$strSQLLimit = ' LIMIT ';
		$totalRows = -1;
		$totalPages = -1;
		$offset = -1;
		$dateLowLimCode = $setting->getSettingFromFullName('date.lowerLimitCode');
		$dateUpLimCode = $setting->getSettingFromFullName('date.upperLimitCode');
		$dateOrderCode = $setting->getSettingFromFullName('date.orderCode');
		$orderbyDate = '';
		
		$strSQL .= $this->getQuery($queryId);
		$rowsPerPage = strval(intval($rowsPerPage));
		$strSQLCount .= $rowsPerPage . ") AS totalPages FROM (";
		$strSQLCount .= $this->getQuery($queryId);
		if($filter != null) {
			if(is_array($filter)) {
				if(count($filter)> 0) {
					foreach ($filter as $name => $value) {
						if($where == 0) {
							$strSQLOptional .= " WHERE (";
						} else if ($where >0) {
							$strSQLOptional .= " AND ";
						}
						$strSQLOptional .= "(";
						if(substr_compare($name, $dateLowLimCode, 0, strlen($dateLowLimCode))==0) {
							$strSQLOptional .= "STR_TO_DATE(T01.";
							$strSQLOptional .= substr($name,strlen($dateLowLimCode));
							$strSQLOptional .= ",'". $dateFormat . "')";
							$strSQLOptional .= " >= STR_TO_DATE('";
							$strSQLOptional .= $db->sanitize($value);
							$strSQLOptional .= "','". $dateFormat . "')";
						} else if (substr_compare($name, $dateUpLimCode, 0, strlen($dateUpLimCode))==0) {
							$strSQLOptional .= "STR_TO_DATE(T01.";
							$strSQLOptional .= substr($name,strlen($dateUpLimCode));
							$strSQLOptional .= ",'". $dateFormat . "')";
							$strSQLOptional .= " <= STR_TO_DATE('";
							$strSQLOptional .= $db->sanitize($value);
							$strSQLOptional .= "','" . $dateFormat . "')";
						}else {
							$strSQLOptional .= "CONVERT(T01.";
							$strSQLOptional .= $name;
							$strSQLOptional .= " USING latin1) COLLATE latin1_general_ci LIKE '%";
							$strSQLOptional .= $db->sanitize($value);
							$strSQLOptional .= "%'";
						}
						$strSQLOptional .= ")";
						++$where;
					}
					if($where > 0) {
						$strSQLOptional .= ")";
					}	
				}
			}
		}	
		if($orderby != null) {
			$strSQLOrderBy .= " ORDER BY ";
 			if(substr_compare($orderby, $dateOrderCode, 0, strlen($dateOrderCode))==0) {
 				$orderbyDate = substr($orderby,strlen($dateOrderCode));
 				$posDESC = stripos($orderbyDate, ' DESC');
 				if($posDESC !== false) {
 					$orderbyDate = preg_replace('/DESC/', '', $orderbyDate);
 				}
 				$strSQLOrderBy .= "STR_TO_DATE(T01.";
 				$strSQLOrderBy .= $orderbyDate;
 				$strSQLOrderBy .= ",'". $dateFormat . "')";
 				if($posDESC !== false) {
 					$strSQLOrderBy .= " DESC";
 				}
 			} else {
 				$strSQLOrderBy .= $orderby;
 			}
 			//var_dump($strSQLOrderBy);
		}
		$strSQLCount .= ") AS T01 " . $strSQLOptional . ";";
		$res = $db->execQuery($strSQLCount);
		if($res["success"] == TRUE) {
			if($res["numrows"] > 0) {
				$totalRows = intval($res["result"][0]["totalRows"]);
				$totalPages = intval($res["result"][0]["totalPages"]);
			}
			else {
				var_dump($strSQLCount);
				var_dump($res);
			}
		}
		else {
			var_dump($strSQLCount);
			var_dump($res);
		}
		if($requestedPage !== null) {
			$ret["requestedPage"] = $requestedPage;
		} else {
			$ret["requestedPage"] = -1;
		}
		$ret["rowsPerPage"] = intval($rowsPerPage);
		$ret["totalRows"] = $totalRows;
		$ret["totalPages"] = $totalPages;
		if(intval($totalRows) >= 0) {
			if($requestedPage !== null) {
				if(intval($totalRows) > 0) {
					$requestedPage = abs(intval($requestedPage))%$totalPages;
					$offset = $requestedPage*intval($rowsPerPage);
				}
				else {
					$requestedPage = 0;
					$offset = 0;
				}
				$ret["actualPage"] = $requestedPage;
				$ret["actualOffset"] = $offset;
				$strSQLLimit .= $offset . "," . $rowsPerPage;
				$strSQL .= ") AS T01 " . $strSQLOptional . $strSQLOrderBy . $strSQLLimit . ";";
			} else {
				$ret["actualPage"] = 0;
				$ret["actualOffset"] = 0;
				$ret["totalPages"] = 1;
				$strSQL .= ") AS T01 " . $strSQLOptional . $strSQLOrderBy . ";";
			}
			//var_dump($strSQL);
			$res = $db->execQuery($strSQL);
			$ret["result"] = $res;
		}
		else {
			$ret["actualPage"] = 0;
			$ret["actualOffset"] = 0;
			$ret["result"] = array();
			$ret["result"]["success"] = FALSE;
			$ret["result"]["result"] = array();
		}
		
		return $ret;
	}
}