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
class Php_AndreaBoccaccio_View_ViewBatchInsert extends Php_AndreaBoccaccio_View_ViewConsistentAbstract {

	private static $instance = null;

	private function __clone() {

	}

	private function __construct() {
		$this->setKind('batchNew');
	}

	public static function getInstance() {
		if(self::$instance == null) {
			self::$instance = new Php_AndreaBoccaccio_View_ViewBatchInsert();
		}
		return self::$instance;
	}

	public function getMenu() {
		$ret = parent::getMenu();

		$ret .= "<div id=\"causeNewMain\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=main\">Principale</a>";
		$ret .= "</div>\n";
		$ret .= "<div id=\"causeNewCauseList\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=batchList\">Lista Lotti</a>";
		$ret .= "</div>\n";
		$ret .= "</div>\n";

		return $ret;
	}

	public function getBody() {
		$ret = '';
		$settingsFact = Php_AndreaBoccaccio_Settings_SettingsFactory::getInstance();
		$dbFact = Php_AndreaBoccaccio_Db_DbFactory::getInstance();
		$settings = $settingsFact->getSettings('xml');
		$db = $dbFact->getDb($settings->getSettingFromFullName('classes.db'));
		$cause = new Php_AndreaBoccaccio_Model_Batch();
		$batchMan = new Php_AndreaBoccaccio_Model_BatchManager();
		$initArray = array();
		$koBitArray = 0x0;
		
		if(isset($_GET["toDo"])) {
			if(!is_null($_GET["toDo"])) {
				if(strncmp($_GET["toDo"],'save',strlen('save'))==0) {
					if(isset($_POST["batch"])) {
						$initArray["batch"] = $db->sanitize($_POST["batch"]);
					}
					if(isset($_POST["vt_start"])) {
						if(strlen($_POST["vt_start"]) > 0) {
							if(preg_match("/^(0[1-9]|[12][0-9]|3[01])[- \/\.](0[1-9]|1[012])[- \/\.](19|20)\d\d$/", $_POST["vt_start"])) {
								$koBitArray = $koBitArray & 0x7ffffffe;
								$initArray["vt_start"] = $db->sanitize($_POST["vt_start"]);
							}
							else {
								$koBitArray = $koBitArray | 0x1;
							}
						}
					}
					else {
						$koBitArray = $koBitArray | 0x1;
					}
					if(isset($_POST["vt_end"])) {
						if(strlen($_POST["vt_end"]) > 0) {
							if(preg_match("/^(0[1-9]|[12][0-9]|3[01])[- \/\.](0[1-9]|1[012])[- \/\.](19|20)\d\d$/", $_POST["vt_end"])) {
								$koBitArray = $koBitArray & 0x7ffffffd;
								$initArray["vt_end"] = $db->sanitize($_POST["vt_end"]);
							}
							else {
								$koBitArray = $koBitArray | 0x2;
							}
						}
					}
					else {
						$koBitArray = $koBitArray | 0x2;
					}
					if(isset($_POST["description"])) {
						if(preg_match("/^[a-zA-Z1-9 ]{0,255}$/", $_POST["description"])) {
							$koBitArray = $koBitArray & 0x7ffffffb;
							$initArray["description"] = $db->sanitize($_POST["description"]);
						}
						else {
							$koBitArray = $koBitArray | 0x4;
						}
					}
					else {
						$koBitArray = $koBitArray | 0x4;
					}
					if($koBitArray == 0x0) {
						$cause->init($initArray);
						$cause->saveToDb();
					}
				}
			}
		}

		$ret .= "<div id=\"body\">";
		$ret .= "<form method=\"post\" action=\"";
		$ret .= $_SERVER["PHP_SELF"];
		$ret .= "?op=batchNew&toDo=save\"> ";
		$ret .= "<div class=\"label\">Lotto:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"batch\"";
		if(isset($_GET["batch"])) {
			if(!is_null($_GET["batch"])) {
				if(strlen($_GET["batch"])>0) {
					$ret .= " value=\"" . $_GET["batch"] . "\"";
				}
			}
		}
		$ret .= " readonly=\"readonly\" />";
		$ret .= "</div><br />";
		$ret .= "<div class=\"label\">Data arrivo:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"arrival\"";
		if(isset($_GET["batch"])) {
			if(!is_null($_GET["batch"])) {
				if(strlen($_GET["batch"])>0) {
					$ret .= " value=\"" . $batchMan->getFirstArrival($_GET["batch"]) . "\"";
				}
			}
		}
		$ret .= " readonly=\"readonly\" />";
		$ret .= "</div><br />";
		if(($koBitArray & 0x1) == 0x1) {
			$ret .= "<div class=\"error\">Data inizio comm. errata</div>";
			$ret .= "<br />";
		}
		$ret .= "<div class=\"label\">Data Inizio Comm.:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"vt_start\"";
		if(($koBitArray & 0x1) == 0x1) {
			$ret .= " value=\"";
			$ret .= $_POST["vt_start"];
			$ret .= "\" />";
		}
		else {
			$ret .= " />";
		}
		$ret .= "</div>";
		$ret .= "<br />";
		if(($koBitArray & 0x2) == 0x2) {
			$ret .= "<div class=\"error\">Data fine comm. errata</div>";
			$ret .= "<br />";
		}
		$ret .= "<div class=\"label\">Data fine comm.:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"vt_end\"";
		if(($koBitArray & 0x2) == 0x2) {
			$ret .= " value=\"";
			$ret .= $_POST["vt_end"];
			$ret .= "\" />";
		}
		else {
			$ret .= " />";
		}
		$ret .= "</div>";
		$ret .= "<br />";
		if(($koBitArray & 0x4) == 0x4) {
			$ret .= "<div class=\"error\">Descrizione errata</div>";
			$ret .= "<br />";
		}
		$ret .= "<div class=\"label\">Descrizione:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"description\"";
		if(($koBitArray & 0x4) == 0x4) {
			$ret .= " value=\"";
			$ret .= $_POST["description"];
			$ret .= "\" />";
		}
		else {
			$ret .= " />";
		}
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"submit\">";
		$ret .= "<input type=\"submit\" value=\"Salva\" />";
		$ret .= "</div>";
		$ret .= "</form>";
		$ret .= "</div>";

		return $ret;
	}
}