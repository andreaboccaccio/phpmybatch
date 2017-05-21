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
class Php_AndreaBoccaccio_View_ViewItem extends Php_AndreaBoccaccio_View_ViewConsistentAbstract {

	private static $instance = null;

	private function __clone() {

	}

	private function __construct() {
		$this->setKind('item');
	}

	public static function getInstance() {
		if(self::$instance == null) {
			self::$instance = new Php_AndreaBoccaccio_View_ViewItem();
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
		$ret .= "\">Lista Lotti</a>";
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
		$itemDenorm = new Php_AndreaBoccaccio_Model_Item();
		$itemDenormManager = new Php_AndreaBoccaccio_Model_ItemManager();
		$initArray = array();
		$countries = array();
		$filterCountries = array();
		$countryMan = new Php_AndreaBoccaccio_Model_CountryManager();
		$gotCountry;
		$tmpArray = array();
		$koBitArray = 0x0;
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
					if(isset($_POST["docDenormId"])) {
						if(preg_match("/^\d+$/", $_POST["docDenormId"])) {
							$koBitArray = $koBitArray & 0x7ffffffe;
							$initArray["document"] = $db->sanitize($_POST["docDenormId"]);
						}
						else {
							$koBitArray = $koBitArray | 0x1;
						}
					}
					else {
						$koBitArray = $koBitArray | 0x1;
					}
					if(isset($_POST["batch"])) {
						$initArray["batch"] = $db->sanitize($_POST["batch"]);
					}
					if(isset($_POST["batch_orig"])) {
						$initArray["batch_orig"] = $db->sanitize($_POST["batch_orig"]);
					}
					if(isset($_POST["country"])) {
						$initArray["country"] = $db->sanitize($_POST["country"]);
					}
					if(isset($_POST["district"])) {
						$initArray["district"] = $db->sanitize($_POST["district"]);
					}
					if(isset($_POST["stabCEE"])) {
						$initArray["stabCEE"] = $db->sanitize($_POST["stabCEE"]);
					}
					if(isset($_POST["kind"])) {
						if(preg_match("/^[a-zA-Z -]{0,50}$/", $_POST["kind"])) {
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
					
					if(isset($_POST["qty"])) {
						if(preg_match("/^\d+$/", $_POST["qty"])) {
							$koBitArray = $koBitArray & 0x7fffffef;
							$initArray["qty"] = $db->sanitize($_POST["qty"]);
						}
						else {
							$koBitArray = $koBitArray | 0x10;
						}
					}
					else {
						$koBitArray = $koBitArray | 0x10;
					}
					if(isset($_POST["kg"])) {
						$initArray["kg"] = $db->sanitize(str_replace(",", ".", $_POST["kg"]));
					}
					if(isset($_POST["arrival"])) {
						if(preg_match("/^(0[1-9]|[12][0-9]|3[01])[- \/\.](0[1-9]|1[012])[- \/\.](19|20)\d\d$/", $_POST["arrival"])) {
							$koBitArray = $koBitArray & 0x7fffffdf;
							$initArray["arrival"] = $db->sanitize($_POST["arrival"]);
						}
						else {
							$koBitArray = $koBitArray | 0x20;
						}
					}
					else {
						$koBitArray = $koBitArray | 0x20;
					}
					if(isset($_POST["description"])) {
						if(preg_match("/^[a-zA-Z0-9 \-_:]{0,255}$/", $_POST["description"])) {
							$koBitArray = $koBitArray & 0x7fffff7f;
							$initArray["description"] = $db->sanitize($_POST["description"]);
						}
						else {
							$koBitArray = $koBitArray | 0x80;
						}
					}
					else {
						$koBitArray = $koBitArray | 0x80;
					}
					if($koBitArray == 0x0) {
						$itemDenorm->init($initArray);
						$itemDenorm->saveToDb();
					}
				}
				else if(strncmp($_GET["toDo"],'erase',strlen('erase'))==0)
				{
					$itemDenormManager->eraseModel($db->sanitize($_POST["itemDenormId"]));
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
			$ret .= "?op=item&toDo=erase&id=". $itemDenorm->getVar('id') ."&docId=";
			$ret .= $_GET["docId"] . "\"> ";
		}
		else {
			$ret .= "?op=item&toDo=modify&id=". $itemDenorm->getVar('id') ."&docId=";
			$ret .= $_GET["docId"] . "\"> ";
			$ret .= "<div>";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=itemNew&docId=" . $_GET["docId"];
			$ret .= "&country=" . $itemDenorm->getVar('country');
			$ret .= "&district=" . $itemDenorm->getVar('district');
			$ret .= "&stabCEE=" . $itemDenorm->getVar('stabCEE');
			$ret .= "&batch_orig=" . $itemDenorm->getVar('batch_orig');
			$ret .= "&batch=" . $itemDenorm->getVar('batch');
			$ret .= "&kind=" . $itemDenorm->getVar('kind');
			$ret .= "&qty=" . $itemDenorm->getVar('qty');
			$ret .= "&kg=" . $itemDenorm->getVar('kg');
			$ret .= "&arrival=" . $itemDenorm->getVar('arrival');
			$ret .= "\">Copia come nuovo";
			$ret .= "</a>";
			$ret .= "</div>";
		}
		$ret .= "<input type=\"hidden\" name=\"itemDenormId\" value=\"" . $itemDenorm->getVar('id') . "\" />";
		$ret .= "<input type=\"hidden\" name=\"docDenormId\" value=\"" . $itemDenorm->getVar('document') . "\" />";
		$ret .= "<div class=\"label\">Nazione:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<select name=\"country\">";
		$tmpArr = $countryMan->getModels(null,$filterCountries,'codealpha2');
		$countries = $tmpArr["result"];
		foreach ($countries as $gotCountry) {
			$ret .= "<option value=\"". $gotCountry->getVar('id');
			if($itemDenorm->getVar('country') == $gotCountry->getVar('id')) {
				$ret .= "\" selected=\"selected";
			}
			$ret .= "\">". $gotCountry->getVar('codealpha2') . '-' . $gotCountry->getVar('enname') . "</option>";
		}
		$ret .= "</select>";
		$ret .= "</div><br />";
		$ret .= "<div class=\"label\">Dipartimento di prov.:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"district\" value=\"" . $itemDenorm->getVar('district') . "\" />";
		$ret .= "</div><br />";
		$ret .= "<div class=\"label\">N. stab. CEE:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"stabCEE\" value=\"" . $itemDenorm->getVar('stabCEE') . "\" />";
		$ret .= "</div><br />";
		$ret .= "<div class=\"label\">Lotto Macellazione:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"batch_orig\" value=\"" . $itemDenorm->getVar('batch_orig') . "\" />";
		$ret .= "</div><br />";
		$ret .= "<div class=\"label\">Lotto:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"batch\" value=\"" . $itemDenorm->getVar('batch') . "\" />";
		$ret .= "</div><br />";
		if(($koBitArray & 0x2) == 0x2) {
			$ret .= "<div class=\"error\">Categoria errata</div>";
			$ret .= "<br />";
		}
		$ret .= "<div class=\"label\">Categoria:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"kind\" value=\"" . $itemDenorm->getVar('kind') . "\" />";
		$ret .= "</div><br />";
		
		if(($koBitArray & 0x10) == 0x10) {
			$ret .= "<div class=\"error\">Colli errati</div>";
			$ret .= "<br />";
		}
		$ret .= "<div class=\"label\">Colli:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"qty\" value=\"" . $itemDenorm->getVar('qty') . "\" />";
		$ret .= "</div><br />";
		$ret .= "<div class=\"label\">Kg:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"kg\" value=\"" . number_format($itemDenorm->getVar('kg'),2,',','') . "\" />";
		$ret .= "</div><br />";
		if(($koBitArray & 0x20) == 0x20) {
			$ret .= "<div class=\"error\">Data di arrivo errata formato corretto GG/MM/AAAA</div>";
			$ret .= "<br />";
		}
		$ret .= "<div class=\"label\">Data di arrivo:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"arrival\" value=\"" . $itemDenorm->getVar('arrival') . "\" />";
		$ret .= "</div><br />";
		if(($koBitArray & 0x80) == 0x80) {
			$ret .= "<div class=\"error\">Descrizione errata</div>";
			$ret .= "<br />";
		}
		$ret .= "<div class=\"label\">Descrizione:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"description\" value=\"" . $itemDenorm->getVar('description') . "\" />";
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