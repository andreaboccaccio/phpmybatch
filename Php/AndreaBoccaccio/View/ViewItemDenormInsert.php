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
class Php_AndreaBoccaccio_View_ViewItemDenormInsert extends Php_AndreaBoccaccio_View_ViewConsistentAbstract {

	private static $instance = null;

	private function __clone() {

	}

	private function __construct() {
		$this->setKind('itemNew');
	}

	public static function getInstance() {
		if(self::$instance == null) {
			self::$instance = new Php_AndreaBoccaccio_View_ViewItemDenormInsert();
		}
		return self::$instance;
	}

	public function getMenu() {
		$ret = parent::getMenu();

		$ret .= "<div id=\"itemNewMain\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=main\">Principale</a>";
		$ret .= "</div>\n";
		$ret .= "<div id=\"itemNewItemList\" class=\"menuentry\">\n";
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
		
		if(isset($_GET["toDo"])) {
			if(!is_null($_GET["toDo"])) {
				if(strncmp($_GET["toDo"],'save',strlen('save'))==0) {
					$itemDenorm->init(-1
							,$db->sanitize($_GET["docId"])
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
			}
		}

		$ret .= "<div id=\"body\">";
		$ret .= "<form method=\"post\" action=\"";
		$ret .= $_SERVER["PHP_SELF"];
		$ret .= "?op=itemNew&toDo=save&docId=";
		$ret .= $_GET["docId"] ."\"> ";
		$ret .= "<div class=\"label\">Categoria:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"kind\" />";
		$ret .= "</div><br />";
		$ret .= "<div class=\"label\">Codice:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"code\" />";
		$ret .= "</div><br />";
		$ret .= "<div class=\"label\">Nome:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"name\" />";
		$ret .= "</div><br />";
		$ret .= "<div class=\"label\">Quantita':</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"qty\" />";
		$ret .= "</div><br />";
		$ret .= "<div class=\"label\">Costo:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"cost\" />";
		$ret .= "</div><br />";
		$ret .= "<div class=\"label\">Prezzo:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"price\" />";
		$ret .= "</div><br />";
		$ret .= "<div class=\"label\">Descrizione:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"description\" />";
		$ret .= "</div><br />";
		$ret .= "<div class=\"submit\">";
		$ret .= "<input type=\"submit\" value=\"Salva\" />";
		$ret .= "</div><br />";
		$ret .= "</form>";
		$ret .= "</div>";

		return $ret;
	}
}