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
class Php_AndreaBoccaccio_View_ViewCauseInsert extends Php_AndreaBoccaccio_View_ViewConsistentAbstract {

	private static $instance = null;

	private function __clone() {

	}

	private function __construct() {
		$this->setKind('causeNew');
	}

	public static function getInstance() {
		if(self::$instance == null) {
			self::$instance = new Php_AndreaBoccaccio_View_ViewCauseInsert();
		}
		return self::$instance;
	}

	public function getMenu() {
		$ret = parent::getMenu();

		$ret .= "<div id=\"causeNewMain\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=main\">Principale</a>";
		$ret .= "</div>\n";
		$ret .= "<div id=\"causeNewCauseList\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=causeList\">Lista Causali</a>";
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
		$docDenorm = new Php_AndreaBoccaccio_Model_DocumentDenorm();
		$cause = new Php_AndreaBoccaccio_Model_Cause();
		$initArray = array();
		$koBitArray = 0x0;
		
		if(isset($_GET["toDo"])) {
			if(!is_null($_GET["toDo"])) {
				if(strncmp($_GET["toDo"],'save',strlen('save'))==0) {
					if(isset($_POST["in_out"])) {
						if(preg_match("/^(I|O)$/", $_POST["in_out"])) {
							$koBitArray = $koBitArray & 0x7ffffffe;
							$initArray["in_out"] = $db->sanitize($_POST["in_out"]);
						}
						else {
							$koBitArray = $koBitArray | 0x1;
						}
					}
					else {
						$koBitArray = $koBitArray | 0x1;
					}
					if(isset($_POST["name"])) {
						if(preg_match("/^[a-zA-Z]{2,50}$/", $_POST["name"])) {
							$koBitArray = $koBitArray & 0x7ffffffd;
							$initArray["name"] = $db->sanitize($_POST["name"]);
						}
						else {
							$koBitArray = $koBitArray | 0x2;
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
		$ret .= "?op=causeNew&toDo=save\"> ";
		if(($koBitArray & 0x1) == 0x1) {
			$ret .= "<div class=\"error\">Errore nella scelta Carico/Scarico</div>";
			$ret .= "<br />";
		}
		$ret .= "<div class=\"label\">Carico/Scarico:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<select name=\"in_out\">";
		$ret .= "<option value=\"\" selected=\"selected\">Scegli un'opzione</option>";
		$ret .= "<option value=\"I\">Carico</option>";
		$ret .= "<option value=\"O\">Scarico</option>";
		$ret .= "</select>";
		$ret .= "</div>";
		$ret .= "<br />";
		if(($koBitArray & 0x2) == 0x2) {
			$ret .= "<div class=\"error\">Nome errato</div>";
			$ret .= "<br />";
		}
		$ret .= "<div class=\"label\">Nome:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"name\"";
		if(($koBitArray & 0x2) == 0x2) {
			$ret .= " value=\"";
			$ret .= $_POST["name"];
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