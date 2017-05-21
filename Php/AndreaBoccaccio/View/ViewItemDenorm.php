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
class Php_AndreaBoccaccio_View_ViewItemDenorm extends Php_AndreaBoccaccio_View_ViewConsistentAbstract {

	private static $instance = null;

	private function __clone() {

	}

	private function __construct() {
		$this->setKind('item');
	}

	public static function getInstance() {
		if(self::$instance == null) {
			self::$instance = new Php_AndreaBoccaccio_View_ViewItemDenorm();
		}
		return self::$instance;
	}

	public function getMenu() {
		$ret = parent::getMenu();

		$ret .= "<div id=\"itemMain\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=main\">Principale</a>";
		$ret .= "</div>\n";
		$ret .= "<div id=\"itemDoc\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=doc&id=";
		$ret .= $_GET["docId"];
		$ret .= "\">Documento</a>";
		$ret .= "</div>\n";
		$ret .= "<div id=\"itemItemList\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=itemList&docId=";
		$ret .= $_GET["docId"];
		$ret .= "\">Lista Articoli</a>";
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
		$itemDenorm = new Php_AndreaBoccaccio_Model_ItemDenorm();
		$docDenormManager = new Php_AndreaBoccaccio_Model_ItemDenormManager();
		$itemId = -1;
		$eraser = 0;

		if(isset($_GET["id"])) {
			if(!is_null($_GET["id"])) {
				$itemId = intval($db->sanitize($_GET["id"]));
				if($itemId>0) {
					$itemDenorm->loadFromDbById($itemId);
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
					$itemDenorm->init($db->sanitize($_POST["itemDenormId"])
							,$db->sanitize($_POST["docDenormId"])
							,$db->sanitize($_POST["kind"])
							,$db->sanitize($_POST["code"])
							,$db->sanitize($_POST["name"])
							,$db->sanitize($_POST["qty"])
							,0
							,$db->sanitize(str_replace(",", ".", $_POST["cost"]))
							,$db->sanitize(str_replace(",", ".", $_POST["price"]))
							,$db->sanitize($_POST["description"])
							);
					$itemDenorm->saveToDb();
				}
				else if(strncmp($_GET["toDo"],'erase',strlen('erase'))==0)
				{
					$docDenorm = new Php_AndreaBoccaccio_Model_DocumentDenorm();
					$docDenormManager->eraseItemDenorm($db->sanitize($_POST["itemDenormId"]));
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
			$ret .= "?op=item&toDo=erase&docId=";
			$ret .= $_GET["docId"] . "\"> ";
		}
		else {
			$ret .= "?op=item&toDo=modify&docId=";
			$ret .= $_GET["docId"] . "\"> ";
		}
		$ret .= "<input type=\"hidden\" name=\"itemDenormId\" value=\"" . $itemDenorm->getId() . "\" />";
		$ret .= "<input type=\"hidden\" name=\"docDenormId\" value=\"" . $itemDenorm->getDocument() . "\" />";
		$ret .= "<div class=\"label\">Categoria:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"kind\" value=\"" . $itemDenorm->getKind() . "\" />";
		$ret .= "</div><br />";
		$ret .= "<div class=\"label\">Codice:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"code\" value=\"" . $itemDenorm->getCode() . "\" />";
		$ret .= "</div><br />";
		$ret .= "<div class=\"label\">Nome:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"name\" value=\"" . $itemDenorm->getName() . "\" />";
		$ret .= "</div><br />";
		$ret .= "<div class=\"label\">Quantita':</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"qty\" value=\"" . $itemDenorm->getQty() . "\" />";
		$ret .= "</div><br />";
		$ret .= "<div class=\"label\">Costo:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"cost\" value=\"" . number_format($itemDenorm->getCost(),2,',','') . "\" />";
		$ret .= "</div><br />";
		$ret .= "<div class=\"label\">Prezzo:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"price\" value=\"" . number_format($itemDenorm->getPrice(),2,',','') . "\" />";
		$ret .= "</div><br />";
		$ret .= "<div class=\"label\">Descrizione:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"description\" value=\"" . $itemDenorm->getDescription() . "\" />";
		$ret .= "</div><br />";
		$ret .= "<div class=\"submit\">";
		if($eraser) {
			$ret .= "<input type=\"submit\" value=\"Si, sono sicuro, cancella!\" />";
		}
		else {
			$ret .= "<input type=\"submit\" value=\"Modifica\" />";
		}
		$ret .= "</div>";
		$ret .= "</form>";
		$ret .= "</div>";

		return $ret;
	}
}