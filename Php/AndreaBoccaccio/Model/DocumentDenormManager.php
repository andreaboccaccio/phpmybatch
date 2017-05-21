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
class Php_AndreaBoccaccio_Model_DocumentDenormManager {
	
	private $docDenormArray = array();
	
	public function getDocs($page = 0
			,$year = null
			,$kind = null
			,$code = null
			,$contractor_kind = null
			,$contractor_code = null
			,$contractor = null
			,$warehouse = null
			,$date = null
			,$vt_start = null
			,$vt_end = null
			,$description = null
			,$orderby = null
			) {
		$ret = array();
		$tmpDocDenorm;
		$tmpRow;
		$i = -1;
		$where = 0;
		$setting = Php_AndreaBoccaccio_Settings_SettingsFactory::getInstance()->getSettings('xml');
		$db = Php_AndreaBoccaccio_Db_DbFactory::getInstance()->getDb($setting->getSettingFromFullName('classes.db'));
		$rowsPerPage = $setting->getSettingFromFullName('memory.rowsPerPage');
		$strSQLCount = "SELECT COUNT(*) AS totalRows, CEIL(COUNT(*)/";
		$strSQL = "SELECT * FROM DOCUMENT_DENORM";
		$strSQLOptional = '';
		$strSQLOrderBy = '';
		$strSQLLimit = ' LIMIT ';
		$totalRows = -1;
		$totalPages = -1;
		$offset = -1;
		
		$rowsPerPage = strval(intval($rowsPerPage));
		$strSQLCount .= $rowsPerPage . ") AS totalPages FROM DOCUMENT_DENORM";
		if(($year != null)
				||($kind != null)
				||($code != null)
				||($contractor_kind != null)
				||($contractor_code != null)
				||($contractor != null)
				||($warehouse != null)
				||($date != null)
				||($vt_start != null)
				||($vt_end != null)
				||($description != null)
				) {
			$strSQLOptional .= " WHERE (";
			if($year != null) {
				if(strlen($year) > 0) {
					if($where > 0) {
						$strSQLOptional .= " AND ";
					}
					$strSQLOptional .= "(year COLLATE latin1_general_ci LIKE '%";
					$strSQLOptional .= $db->sanitize($year);
					$strSQLOptional .= "%')";
					++$where;
				}
			}
			if($kind != null) {
				if(strlen($kind) > 0) {
					if($where > 0) {
						$strSQLOptional .= " AND ";
					}
					$strSQLOptional .= "(kind COLLATE latin1_general_ci LIKE '%";
					$strSQLOptional .= $db->sanitize($kind);
					$strSQLOptional .= "%')";
					++$where;
				}
			}
			if($code != null) {
				if(strlen($code) > 0) {
					if($where > 0) {
						$strSQLOptional .= " AND ";
					}
					$strSQLOptional .= "(code COLLATE latin1_general_ci LIKE '%";
					$strSQLOptional .= $db->sanitize($code);
					$strSQLOptional .= "%')";
					++$where;
				}
			}
			if($contractor_kind != null) {
				if(strlen($contractor_kind) > 0) {
					if($where > 0) {
						$strSQLOptional .= " AND ";
					}
					$strSQLOptional .= "(contractor_kind COLLATE latin1_general_ci LIKE '%";
					$strSQLOptional .= $db->sanitize($contractor_kind);
					$strSQLOptional .= "%')";
					++$where;
				}
			}
			
			if($contractor_code != null) {
				if(strlen($contractor_code) > 0) {
					if($where > 0) {
						$strSQLOptional .= " AND ";
					}
					$strSQLOptional .= "(contractor_code COLLATE latin1_general_ci LIKE '%";
					$strSQLOptional .= $db->sanitize($contractor_code);
					$strSQLOptional .= "%')";
					++$where;
				}
			}
			if($contractor != null) {
				if(strlen($contractor) > 0) {
					if($where > 0) {
						$strSQLOptional .= " AND ";
					}
					$strSQLOptional .= "(contractor COLLATE latin1_general_ci LIKE '%";
					$strSQLOptional .= $db->sanitize($contractor);
					$strSQLOptional .= "%')";
					++$where;
				}
			}
			if($warehouse != null) {
				if(strlen($warehouse) > 0) {
					if($where > 0) {
						$strSQLOptional .= " AND ";
					}
					$strSQLOptional .= "(warehouse COLLATE latin1_general_ci LIKE '%";
					$strSQLOptional .= $db->sanitize($warehouse);
					$strSQLOptional .= "%')";
					++$where;
				}
			}
			if($date != null) {
				if(strlen($date) > 0) {
					if($where > 0) {
						$strSQL .= " AND ";
					}
					$strSQLOptional .= "(date COLLATE latin1_general_ci LIKE '%";
					$strSQLOptional .= $db->sanitize($date);
					$strSQLOptional .= "%')";
					++$where;
				}
			}
			if($vt_start != null) {
				if(strlen($vt_start) > 0) {
					if($where > 0) {
						$strSQLOptional .= " AND ";
					}
					$strSQLOptional .= "(vt_start ='";
					$strSQLOptional .= $db->sanitize($vt_start);
					$strSQLOptional .= "')";
					++$where;
				}
			}
			if($vt_end != null) {
				if(strlen($vt_end) > 0) {
					if($where > 0) {
						$strSQLOptional .= " AND ";
					}
					$strSQLOptional .= "(vt_end ='";
					$strSQLOptional .= $db->sanitize($vt_end);
					$strSQLOptional .= "')";
					++$where;
				}
			}
			if($description != null) {
				if(strlen($description) > 0) {
					if($where > 0) {
						$strSQLOptional .= " AND ";
					}
					$strSQLOptional .= "(description LIKE COLLATE latin1_general_ci '%";
					$strSQLOptional .= $db->sanitize($description);
					$strSQLOptional .= "%')";
					++$where;
				}
			}
			$strSQLOptional .= ")";
		}
		if($orderby != null) {
			$strSQLOrderBy .= " ORDER BY ";
			$strSQLOrderBy .= $orderby;
		}
		$strSQLCount .= $strSQLOptional . ";";
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
			$res = $db->execQuery($strSQL);
			if($res["success"] == TRUE) {
				if($res["numrows"] > 0) {
					for ($i = 0; $i < $res["numrows"]; ++$i) {
						$tmpRow = $res["result"][$i];
						$tmpDocDenorm = new Php_AndreaBoccaccio_Model_DocumentDenorm();
						$tmpDocDenorm->init(intval($tmpRow["id"])
								,$tmpRow["year"]
								,$tmpRow["kind"]
								,$tmpRow["code"]
								,$tmpRow["contractor_kind"]
								,$tmpRow["contractor_code"]
								,$tmpRow["contractor"]
								,$tmpRow["warehouse"]
								,$tmpRow["date"]
								,$tmpRow["vt_start"]
								,$tmpRow["vt_end"]
								,$tmpRow["description"]
								);
						$this->docDenormArray[$i] = $tmpDocDenorm;
					}
				}
			}
		$ret["result"] = $this->docDenormArray;
		}
		else {
			$ret["actualPage"] = 0;
			$ret["actualOffset"] = 0;
			$ret["result"] = array();
		}
		
		return $ret;
	}
	
	public function eraseDocumentDenorm($id) {
		$setting = Php_AndreaBoccaccio_Settings_SettingsFactory::getInstance()->getSettings('xml');
		$db = Php_AndreaBoccaccio_Db_DbFactory::getInstance()->getDb($setting->getSettingFromFullName('classes.db'));
		$strSQL = "DELETE FROM DOCUMENT_DENORM";
		$res = array();
		
		$strSQL .= " WHERE(id=" .$id .");";
		$res = $db->execQuery($strSQL);
	}
}