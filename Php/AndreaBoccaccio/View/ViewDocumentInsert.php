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
class Php_AndreaBoccaccio_View_ViewDocumentInsert extends Php_AndreaBoccaccio_View_ViewConsistentAbstract {

	private static $instance = null;

	private function __clone() {

	}

	private function __construct() {
		$this->setKind('docNew');
	}

	public static function getInstance() {
		if(self::$instance == null) {
			self::$instance = new Php_AndreaBoccaccio_View_ViewDocumentInsert();
		}
		return self::$instance;
	}

	public function getMenu() {
		$ret = parent::getMenu();

		$ret .= "<div id=\"docNewMain\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=main\">Principale</a>";
		$ret .= "</div>\n";
		$ret .= "<div id=\"docNewDocList\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=docList\">Lista Documenti</a>";
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
		$docDenorm = new Php_AndreaBoccaccio_Model_Document();
		$docDenormManager = new Php_AndreaBoccaccio_Model_DocumentManager();
		$initArray = array();
		$koBitArray = 0x0;
		$docId = -1;
		$eraser = 0;
		
		if(isset($_GET["toDo"])) {
			if(!is_null($_GET["toDo"])) {
				if(strncmp($_GET["toDo"],'save',strlen('save'))==0) {
					if(isset($_POST["year"])) {
						if(preg_match("/^\d{4}$/", $_POST["year"])) {
							$koBitArray = $koBitArray & 0x7ffffffe;
							$initArray["year"] = $db->sanitize($_POST["year"]);
						}
						else {
							$koBitArray = $koBitArray | 0x1;
						}
					}
					else {
						$koBitArray = $koBitArray | 0x1;
					}
					if(isset($_POST["kind"])) {
						if(preg_match("/^[a-zA-Z]{2,50}$/", $_POST["kind"])) {
							$koBitArray = $koBitArray & 0x7ffffffd;
							$initArray["kind"] = $db->sanitize($_POST["kind"]);
						}
						else {
							$koBitArray = $koBitArray | 0x2;
						}
					}
					else {
						$koBitArray = $koBitArray | 0x2;
					}
					if(isset($_POST["code"])) {
						if(preg_match("/^\w{1,20}$/", $_POST["code"])) {
							$koBitArray = $koBitArray & 0x7ffffffb;
							$initArray["code"] = $db->sanitize($_POST["code"]);
						}
						else {
							$koBitArray = $koBitArray | 0x4;
						}
					}
					else {
						$koBitArray = $koBitArray | 0x4;
					}
					if(isset($_POST["contractor_kind"])) {
						if(preg_match("/^[a-zA-Z0-9 ]{2,50}$/", $_POST["contractor_kind"])) {
							$koBitArray = $koBitArray & 0x7ffffff7;
							$initArray["contractor_kind"] = $db->sanitize($_POST["contractor_kind"]);
						}
						else {
							$koBitArray = $koBitArray | 0x8;
						}
					}
					else {
						$koBitArray = $koBitArray | 0x8;
					}
					if(isset($_POST["contractor_code"])) {
						if(preg_match("/^\w{1,25}$/", $_POST["contractor_code"])) {
							$koBitArray = $koBitArray & 0x7fffffef;
							$initArray["contractor_code"] = $db->sanitize($_POST["contractor_code"]);
						}
						else {
							$koBitArray = $koBitArray | 0x10;
						}
					}
					else {
						$koBitArray = $koBitArray | 0x10;
					}
					if(isset($_POST["contractor"])) {
						if(preg_match("/^[a-zA-Z0-9 \-_]{1,25}$/", $_POST["contractor"])) {
							$koBitArray = $koBitArray & 0x7fffffdf;
							$initArray["contractor"] = $db->sanitize($_POST["contractor"]);
						}
						else {
							$koBitArray = $koBitArray | 0x20;
						}
					}
					else {
						$koBitArray = $koBitArray | 0x20;
					}
					if(isset($_POST["warehouse"])) {
						if(preg_match("/^\w{1,50}$/", $_POST["warehouse"])) {
							$koBitArray = $koBitArray & 0x7fffffbf;
							$initArray["warehouse"] = $db->sanitize($_POST["warehouse"]);
						}
						else {
							$koBitArray = $koBitArray | 0x40;
						}
					}
					else {
						$koBitArray = $koBitArray | 0x40;
					}
					if(isset($_POST["date"])) {
						if(preg_match("/^\d{2}.\d{2}.\d{4}$/", $_POST["date"])) {
							$koBitArray = $koBitArray & 0x7fffff7f;
							$initArray["date"] = $db->sanitize($_POST["date"]);
						}
						else {
							$koBitArray = $koBitArray | 0x80;
						}
					}
					else {
						$koBitArray = $koBitArray | 0x80;
					}
					if(isset($_POST["vt_start"])) {
						if(preg_match("/^([a-zA-Z0-9 \-_:]{8,20}|.{0})$/", $_POST["vt_start"])) {
							$koBitArray = $koBitArray & 0x7ffffeff;
							$initArray["vt_start"] = $db->sanitize($_POST["vt_start"]);
						}
						else {
							$koBitArray = $koBitArray | 0x100;
						}
					}
					else {
						$koBitArray = $koBitArray | 0x100;
					}
					if(isset($_POST["vt_end"])) {
						if(preg_match("/^([a-zA-Z0-9 \-_:]{8,20}|.{0})$/", $_POST["vt_end"])) {
							$koBitArray = $koBitArray & 0x7ffffdff;
							$initArray["vt_end"] = $db->sanitize($_POST["vt_end"]);
						}
						else {
							$koBitArray = $koBitArray | 0x200;
						}
					}
					else {
						$koBitArray = $koBitArray | 0x200;
					}
					if(isset($_POST["description"])) {
						if(preg_match("/^[a-zA-Z0-9 \-_:]{0,255}$/", $_POST["description"])) {
							$koBitArray = $koBitArray & 0x7ffffbff;
							$initArray["description"] = $db->sanitize($_POST["description"]);
						}
						else {
							$koBitArray = $koBitArray | 0x400;
						}
					}
					else {
						$koBitArray = $koBitArray | 0x400;
					}
					if($koBitArray == 0x0) {
						$docDenorm->init($initArray);
						$docDenorm->saveToDb();
					}
				}
				else if(strncmp($_GET["toDo"],'erase',strlen('erase'))==0)
				{
					$docDenorm = new Php_AndreaBoccaccio_Model_Document();
					$docDenormManager->eraseModel($db->sanitize($_POST["docDenormId"]));
				}
			}
		}

		$ret .= "<div id=\"body\">";
		$ret .= "<form method=\"post\" action=\"";
		$ret .= $_SERVER["PHP_SELF"];
		$ret .= "?op=docNew&toDo=save\"> ";
		if(($koBitArray & 0x1) == 0x1) {
			$ret .= "<div class=\"error\">Anno errato</div>";
			$ret .= "<br />";
		}
		$ret .= "<div class=\"label\">Anno:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"year\"";
		if($koBitArray != 0x0) {
			$ret .= " value=\"" . $_POST["year"] . "\"";
		} else if(isset($_GET["year"])) {
			if(!is_null($_GET["year"])) {
				if(strlen($_GET["year"])>0) {
					$ret .= " value=\"" . $_GET["year"] . "\"";
				}
			}
		}
		$ret .= " />";
		$ret .= "</div>";
		$ret .= "<br />";
		if(($koBitArray & 0x2) == 0x2) {
			$ret .= "<div class=\"error\">Tipo Documento errato</div>";
			$ret .= "<br />";
		}
		$ret .= "<div class=\"label\">Tipo Documento:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"kind\"";
		if($koBitArray != 0x0) {
			$ret .= " value=\"" . $_POST["kind"] . "\"";
		} else if(isset($_GET["kind"])) {
			if(!is_null($_GET["kind"])) {
				if(strlen($_GET["kind"])>0) {
					$ret .= " value=\"" . $_GET["kind"] . "\"";
				}
			}
		}
		$ret .= " />";
		$ret .= "</div>";
		$ret .= "<br />";
		if(($koBitArray & 0x4) == 0x4) {
			$ret .= "<div class=\"error\">Numero/Codice errato</div>";
			$ret .= "<br />";
		}
		$ret .= "<div class=\"label\">Numero/Codice:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"code\"";
		if($koBitArray != 0x0) {
			$ret .= " value=\"" . $_POST["code"] . "\"";
		} else if(isset($_GET["code"])) {
			if(!is_null($_GET["code"])) {
				if(strlen($_GET["code"])>0) {
					$ret .= " value=\"" . $_GET["code"] . "\"";
				}
			}
		}
		$ret .= " />";
		$ret .= "</div>";
		$ret .= "<br />";
		if(($koBitArray & 0x80) == 0x80) {
			$ret .= "<div class=\"error\">Data documento errata</div>";
			$ret .= "<br />";
		}
		$ret .= "<div class=\"label\">Data documento:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"date\"";
		if($koBitArray != 0x0) {
			$ret .= " value=\"" . $_POST["date"] . "\"";
		}
		$ret .= " />";
		$ret .= "</div>";
		$ret .= "<br />";
		if(($koBitArray & 0x8) == 0x8) {
			$ret .= "<div class=\"error\">Tipo Contraente errato</div>";
			$ret .= "<br />";
		}
		$ret .= "<div class=\"label\">Tipo Contraente:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"contractor_kind\"";
		if($koBitArray != 0x0) {
			$ret .= " value=\"" . $_POST["contractor_kind"] . "\"";
		} else if(isset($_GET["contractor_kind"])) {
			if(!is_null($_GET["contractor_kind"])) {
				if(strlen($_GET["contractor_kind"])>0) {
					$ret .= " value=\"" . $_GET["contractor_kind"] . "\"";
				}
			}
		}
		$ret .= " />";
		$ret .= "</div>";
		$ret .= "<br />";
		if(($koBitArray & 0x10) == 0x10) {
			$ret .= "<div class=\"error\">P.IVA/CF Contraente errata</div>";
			$ret .= "<br />";
		}
		$ret .= "<div class=\"label\">P.IVA/CF Contraente:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"contractor_code\"";
		if($koBitArray != 0x0) {
			$ret .= " value=\"" . $_POST["contractor_code"] . "\"";
		} else if(isset($_GET["contractor_code"])) {
			if(!is_null($_GET["contractor_code"])) {
				if(strlen($_GET["contractor_code"])>0) {
					$ret .= " value=\"" . $_GET["contractor_code"] . "\"";
				}
			}
		}
		$ret .= " />";
		$ret .= "</div>";
		$ret .= "<br />";
		if(($koBitArray & 0x20) == 0x20) {
			$ret .= "<div class=\"error\">Contraente errato</div>";
			$ret .= "<br />";
		}
		$ret .= "<div class=\"label\">Contraente:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"contractor\"";
		if($koBitArray != 0x0) {
			$ret .= " value=\"" . $_POST["contractor"] . "\"";
		} else if(isset($_GET["contractor"])) {
			if(!is_null($_GET["contractor"])) {
				if(strlen($_GET["contractor"])>0) {
					$ret .= " value=\"" . $_GET["contractor"] . "\"";
				}
			}
		}
		$ret .= " />";
		$ret .= "</div>";
		$ret .= "<br />";
		if(($koBitArray & 0x40) == 0x40) {
			$ret .= "<div class=\"error\">Magazzino errato</div>";
			$ret .= "<br />";
		}
		$ret .= "<div class=\"label\">Magazzino:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"warehouse\"";
		if($koBitArray != 0x0) {
			$ret .= " value=\"" . $_POST["warehouse"] . "\"";
		} else if(isset($_GET["warehouse"])) {
			if(!is_null($_GET["warehouse"])) {
				if(strlen($_GET["warehouse"])>0) {
					$ret .= " value=\"" . $_GET["warehouse"] . "\"";
				}
			}
		}
		$ret .= " />";
		$ret .= "</div>";
		$ret .= "<br />";
		if(($koBitArray & 0x100) == 0x100) {
			$ret .= "<div class=\"error\">Inizio Validita' errata</div>";
			$ret .= "<br />";
		}
		$ret .= "<div class=\"label\">Inizio Validita':</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"vt_start\"";
		if($koBitArray != 0x0) {
			$ret .= " value=\"" . $_POST["vt_start"] . "\"";
		}
		$ret .= " />";
		$ret .= "</div>";
		$ret .= "<br />";
		if(($koBitArray & 0x200) == 0x200) {
			$ret .= "<div class=\"error\">Fine Validita' errata</div>";
			$ret .= "<br />";
		}
		$ret .= "<div class=\"label\">Fine Validita':</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"vt_end\"";
		if($koBitArray != 0x0) {
			$ret .= " value=\"" . $_POST["vt_end"] . "\"";
		}
		$ret .= " />";
		$ret .= "</div>";
		$ret .= "<br />";
		if(($koBitArray & 0x400) == 0x400) {
			$ret .= "<div class=\"error\">Descrizione errata</div>";
			$ret .= "<br />";
		}
		$ret .= "<div class=\"label\">Descrizione:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"description\"";
		if($koBitArray != 0x0) {
			$ret .= " value=\"" . $_POST["description"] . "\"";
		} else if(isset($_GET["description"])) {
			if(!is_null($_GET["description"])) {
				if(strlen($_GET["description"])>0) {
					$ret .= " value=\"" . $_GET["description"] . "\"";
				}
			}
		}
		$ret .= " />";
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"submit\">";
		$ret .= "<input type=\"submit\" value=\"Salva\" />";
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "</form>";
		$ret .= "</div>";

		return $ret;
	}
}