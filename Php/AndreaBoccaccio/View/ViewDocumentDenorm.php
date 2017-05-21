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
class Php_AndreaBoccaccio_View_ViewDocumentDenorm extends Php_AndreaBoccaccio_View_ViewConsistentAbstract {

	private static $instance = null;

	private function __clone() {

	}

	private function __construct() {
		$this->setKind('doc');
	}

	public static function getInstance() {
		if(self::$instance == null) {
			self::$instance = new Php_AndreaBoccaccio_View_ViewDocumentDenorm();
		}
		return self::$instance;
	}

	public function getMenu() {
		$ret = parent::getMenu();

		$ret .= "<div id=\"docMain\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=main\">Principale</a>";
		$ret .= "</div>\n";
		$ret .= "<div id=\"docDocList\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=docList\">Lista Documenti</a>";
		$ret .= "</div>\n";
		$ret .= "<div id=\"docItemList\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=itemList&docId=";
		$ret .= $_GET["id"];
		$ret .= "\">Articoli</a>";
		$ret .= "</div>\n";
		$ret .= "<div id=\"docItemNew\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=itemNew&docId=";
		$ret .= $_GET["id"];
		$ret .= "\">Nuovo Articolo</a>";
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
		$docDenormManager = new Php_AndreaBoccaccio_Model_DocumentDenormManager();
		$docId = -1;
		$eraser = 0;

		if(isset($_GET["id"])) {
			if(!is_null($_GET["id"])) {
				$docId = intval($db->sanitize($_GET["id"]));
				if($docId>0) {
					$docDenorm->loadFromDbById($docId);
				}
			}
		}
		
		if(isset($_GET["delete"])) {
			if(!is_null($_GET["delete"])) {
				if(strncmp($_GET["delete"],'maybe',strlen('maybe'))==0) {
					$eraser = 1;
				}
			}
		}
		
		if(isset($_GET["toDo"])) {
			if(!is_null($_GET["toDo"])) {
				if(strncmp($_GET["toDo"],'modify',strlen('modify'))==0) {
					$docDenorm->init($db->sanitize($_POST["docDenormId"])
							,$db->sanitize($_POST["year"])
							,$db->sanitize($_POST["kind"])
							,$db->sanitize($_POST["code"])
							,$db->sanitize($_POST["contractor_kind"])
							,$db->sanitize($_POST["contractor_code"])
							,$db->sanitize($_POST["contractor"])
							,$db->sanitize($_POST["warehouse"])
							,$db->sanitize($_POST["date"])
							,$db->sanitize($_POST["vt_start"])
							,$db->sanitize($_POST["vt_end"])
							,$db->sanitize($_POST["description"])
							);
					$docDenorm->saveToDb();
				}
				else if(strncmp($_GET["toDo"],'erase',strlen('erase'))==0)
				{
					$docDenorm = new Php_AndreaBoccaccio_Model_DocumentDenorm();
					$docDenormManager->eraseDocumentDenorm($db->sanitize($_POST["docDenormId"]));
				}
			}
		}

		$ret .= "<div id=\"body\">";
		if($eraser) {
			$ret .= "<div >Sicuro di voler cancellare:</div>";
		}
		$ret .= "<form method=\"post\" action=\"";
		$ret .= $_SERVER["PHP_SELF"];
		if($eraser) {
			$ret .= "?op=doc&toDo=erase&id=" . $docDenorm->getId() . "\"> ";
		}
		else {
			$ret .= "?op=doc&toDo=modify&id=" . $docDenorm->getId() . "\"> ";
		}
		$ret .= "<input type=\"hidden\" name=\"docDenormId\" value=\"" . $docDenorm->getId() . "\" />";
		$ret .= "<div class=\"label\">Anno:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"year\" value=\"" . $docDenorm->getYear() . "\" />";
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"label\">Tipo Documento:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"kind\" value=\"" . $docDenorm->getKind() . "\" />";
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"label\">Numero/Codice:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"code\" value=\"" . $docDenorm->getCode() . "\" />";
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"label\">Data documento:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"date\" value=\"" . $docDenorm->getDate() . "\" />";
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"label\">Tipo Contraente:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"contractor_kind\" value=\"" . $docDenorm->getContractorKind() . "\" />";
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"label\">P.IVA/CF Contraente:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"contractor_code\" value=\"" . $docDenorm->getContractorCode() . "\" />";
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"label\">Contraente:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"contractor\" value=\"" . $docDenorm->getContractor() . "\" />";
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"label\">Magazzino:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"warehouse\" value=\"" . $docDenorm->getWarehouse() . "\" />";
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"label\">Inizio Validita':</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"vt_start\" value=\"" . $docDenorm->getVtStart() . "\" />";
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"label\">Fine Validita':</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"vt_end\" value=\"" . $docDenorm->getVtEnd() . "\" />";
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"label\">Descrizione:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"description\" value=\"" . $docDenorm->getDescription() . "\" />";
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "<div class=\"submit\">";
		if($eraser) {
			$ret .= "<input type=\"submit\" value=\"Si, sono sicuro, cancella!\" />";
		}
		else {
			$ret .= "<input type=\"submit\" value=\"Modifica\" />";
		}
		$ret .= "</div>";
		$ret .= "<br />";
		$ret .= "</form>";
		$ret .= "</div>";

		return $ret;
	}
}