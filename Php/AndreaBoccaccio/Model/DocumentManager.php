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
class Php_AndreaBoccaccio_Model_DocumentManager extends Php_AndreaBoccaccio_Model_ManagerAbstract {
	
	public function __construct() {
		$this->setKind("document");
		$this->init();
	}
	
	protected function getModel() {
		return new Php_AndreaBoccaccio_Model_Document();
	}
	
	public function getModels($page = 0, &$filter=null, $orderby=null) {
		$ret = array();
		$tmpArray = array();
		$tmpModel;
		$tmpRow;
		$i = -1;
		$where = 0;
		$joined = FALSE;
		$setting = Php_AndreaBoccaccio_Settings_SettingsFactory::getInstance()->getSettings('xml');
		$db = Php_AndreaBoccaccio_Db_DbFactory::getInstance()->getDb($setting->getSettingFromFullName('classes.db'));
		$dateFormat = $setting->getSettingFromFullName('date.sqlFormat');
		$rowsPerPage = $setting->getSettingFromFullName('memory.rowsPerPage');
		$strSQLCount = "SELECT COUNT(*) AS totalRows, CEIL(COUNT(*)/";
		$strSQL = "SELECT T01.* FROM ";
		$strSQLInnerJoin = " INNER JOIN (SELECT document FROM ITEM_DENORM";
		$strSQLOptional = '';
		$strSQLOrderBy = '';
		$strSQLLimit = ' LIMIT ';
		$totalRows = -1;
		$totalPages = -1;
		$offset = -1;
		$orderbyTmp = '';
		$nameTmp = '';
		$opTmp = '';
	
		$strSQL .= $this->mappingModel->getDbTabName();
		$strSQL .= " AS T01";
		$rowsPerPage = strval(intval($rowsPerPage));
		$strSQLCount .= $rowsPerPage . ") AS totalPages FROM ";
		$strSQLCount .= $this->mappingModel->getDbTabName();
		$strSQLCount .= " AS T01";
		if($filter != null) {
			if(is_array($filter)) {
				if(count($filter)> 0) {
					foreach ($filter as $name => $value) {
						if(substr_compare($name, '_f_', 0, strlen('_f_'))==0) {
							$opTmp = ' >= ';
						} elseif(substr_compare($name, '_t_', 0, strlen('_t_'))==0) {
							$opTmp = ' <= ';
						} else {
							$opTmp = ' = ';
						}
						$nameTmp = preg_replace('(_f_|_t_)', '', $name);
						if(array_key_exists($nameTmp, $this->mappingModel->getDefaults())) {
							if($where == 0) {
								$strSQLOptional .= " WHERE (";
							} else if ($where >0) {
								$strSQLOptional .= " AND ";
							}
							$strSQLOptional .= "(";
							
							switch ($this->mappingModel->getVarKind($nameTmp)) {
								case "int":
									$strSQLOptional .= $this->mappingModel->getVarName($nameTmp,null);
									$strSQLOptional .= $opTmp;
									$strSQLOptional .= intval($value);
									break;
								case "float":
									$strSQLOptional .= $this->mappingModel->getVarName($nameTmp,null);
									$strSQLOptional .= $opTmp;
									$strSQLOptional .= floatval($value);
									break;
								case "stringDate":
									$strSQLOptional .= 'STR_TO_DATE(';
									$strSQLOptional .= $this->mappingModel->getVarName($nameTmp,null);
									$strSQLOptional .= ",'";
									$strSQLOptional .= $dateFormat;
									$strSQLOptional .= "')";
									$strSQLOptional .= $opTmp;
									$strSQLOptional .= "STR_TO_DATE('";
									$strSQLOptional .= $db->sanitize($value);
									$strSQLOptional .= "','";
									$strSQLOptional .= $dateFormat;
									$strSQLOptional .= "')";
									break;
								default:
									$strSQLOptional .= $this->mappingModel->getVarName($nameTmp,null);
									$strSQLOptional .= " COLLATE latin1_general_ci LIKE '%";
									$strSQLOptional .= $db->sanitize($value);
									$strSQLOptional .= "%'";
							}
							$strSQLOptional .= ")";
							++$where;
						}
						else if(strncmp($name,"item",strlen("item"))== 0) {
							if(!$joined) {
								$joined = TRUE;
								$strSQLInnerJoin .= "  WHERE (";
							} else
							{
								$strSQLInnerJoin .= " AND ";
							}
							$strSQLInnerJoin .= "(";
							$strSQLInnerJoin .= substr($name,4);
							$strSQLInnerJoin .= " COLLATE latin1_general_ci LIKE '%";
							$strSQLInnerJoin .= $db->sanitize($value);
							$strSQLInnerJoin .= "%'";
							$strSQLInnerJoin .= ")";
						}
					}
					if($where > 0) {
						$strSQLOptional .= ")";
					}
					if($joined) {
						$strSQLInnerJoin .= ") GROUP BY document) AS IT";
						$strSQLInnerJoin .= " ON (T01.id = IT.document) ";
						$strSQL .= $strSQLInnerJoin;
						$strSQLCount .= $strSQLInnerJoin;
					}
				}
			}
		}
		if($orderby != null) {
			$strSQLOrderBy .= " ORDER BY ";
			$posDESC = stripos($orderby, ' DESC');
			if($posDESC !== false) {
				$orderbyTmp = rtrim(preg_replace('/DESC/', '', $orderby));
 			} else {
 				$orderbyTmp = $orderby;
 			}
 			if(strcasecmp($this->mappingModel->getVarKind($orderbyTmp),'stringDate')==0) {
 				$orderbyTmp = 'STR_TO_DATE('
 						. $this->mappingModel->getVarName($orderbyTmp,null)
 						. ",'"
 						. $dateFormat
 						."')";
 			} else {
 				$orderbyTmp = $this->mappingModel->getVarName($orderbyTmp,null);
 			}
			$strSQLOrderBy .= $orderbyTmp;
			if($posDESC !== false) {
				$strSQLOrderBy .= ' DESC';
			}
		}
		$strSQLCount .= $strSQLOptional . ";";
		//var_dump($strSQLCount);
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
		$ret["requestedPage"] = $page;
		$ret["rowsPerPage"] = intval($rowsPerPage);
		$ret["totalRows"] = $totalRows;
		$ret["totalPages"] = $totalPages;
		if(intval($totalRows) > 0) {
			$page = abs(intval($page))%$totalPages;
			$offset = $page*intval($rowsPerPage);
			$ret["actualPage"] = $page;
			$ret["actualOffset"] = $offset;
			$strSQLLimit .= $offset . "," . $rowsPerPage;
			$strSQL .= $strSQLOptional . $strSQLOrderBy . $strSQLLimit . ";";
			//var_dump($strSQL);
			$res = $db->execQuery($strSQL);
			if($res["success"] == TRUE) {
				if($res["numrows"] > 0) {
					for ($i = 0; $i < $res["numrows"]; ++$i) {
						$tmpRow = $res["result"][$i];
						$tmpModel = $this->getModel();
						foreach ($res["fields"] as $dbname) {
							$tmpArray[$this->mappingModel->getVarName(null,$dbname)] = $tmpRow[$dbname];
						}
						$tmpModel->init($tmpArray);
						$this->modelArray[$i] = $tmpModel;
					}
				}
			}			
			$ret["result"] = $this->modelArray;
		}
		else {
			$ret["actualPage"] = 0;
			$ret["actualOffset"] = 0;
			$ret["result"] = array();
		}
	
		return $ret;
	}
}