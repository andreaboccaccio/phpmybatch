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
class Php_AndreaBoccaccio_View_ViewItemOut extends Php_AndreaBoccaccio_View_ViewConsistentAbstract {

	private static $instance = null;

	private function __clone() {

	}

	private function __construct() {
		$this->setKind('itemOut');
	}

	public static function getInstance() {
		if(self::$instance == null) {
			self::$instance = new Php_AndreaBoccaccio_View_ViewItemOut();
		}
		return self::$instance;
	}

	public function getMenu() {
		$ret = parent::getMenu();

		$ret .= "<div id=\"itemMain\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=main\">Principale</a>";
		$ret .= "</div>\n";
		$ret .= "<div id=\"itemItemOutList\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=itemOutList";
		$ret .= "\">Lista Scarichi</a>";
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
		$itemDenorm = new Php_AndreaBoccaccio_Model_ItemOut();
		$itemDenormManager = new Php_AndreaBoccaccio_Model_ItemOutManager();
		$causeMan = new Php_AndreaBoccaccio_Model_CauseManager();
		$causes = array();
		$initArray = array();
		$koBitArray = 0x0;
		$itemId = -1;
		$eraser = 0;
		$filterCauses = array('in_out' => 'O');
		$tmpArr = array();

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
					if(isset($_POST["cause"])) {
						if(preg_match("/^\d+$/", $_POST["cause"])) {
							$koBitArray = $koBitArray & 0x7ffffffe;
							$initArray["cause"] = $db->sanitize($_POST["cause"]);
						}
						else {
							$koBitArray = $koBitArray | 0x1;
						}
					}
					else {
						$koBitArray = $koBitArray | 0x1;
					}
					if(isset($_POST["kind"])) {
						if(preg_match("/^[a-zA-Z -]{2,50}$/", $_POST["kind"])) {
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
						if(preg_match("/^\w{1,50}$/", $_POST["code"])) {
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
					if(isset($_POST["name"])) {
						if(preg_match("/^[a-zA-Z0-9 -]{0,50}$/", $_POST["name"])) {
							$koBitArray = $koBitArray & 0x7ffffff7;
							$initArray["name"] = $db->sanitize($_POST["name"]);
						}
						else {
							$koBitArray = $koBitArray | 0x8;
						}
					}
					else {
						$koBitArray = $koBitArray | 0x8;
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
					if(isset($_POST["producer"])) {
						$initArray["producer"] = $db->sanitize($_POST["producer"]);
					}
					if(isset($_POST["yearProd"])) {
						$initArray["yearProd"] = $db->sanitize($_POST["yearProd"]);
					}
					if(isset($_POST["batch"])) {
						$initArray["batch"] = $db->sanitize($_POST["batch"]);
					}
					if(isset($_POST["ownBatch"])) {
						$initArray["ownBatch"] = $db->sanitize($_POST["ownBatch"]);
					}
					if(isset($_POST["ownDocumentYear"])) {
						$initArray["ownDocumentYear"] = $db->sanitize($_POST["ownDocumentYear"]);
					}
					if(isset($_POST["ownDocumentCode"])) {
						$initArray["ownDocumentCode"] = $db->sanitize($_POST["ownDocumentCode"]);
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
			$ret .= "?op=itemOut&toDo=erase&id=". $itemDenorm->getVar('id') . "\"> ";
		}
		else {
			$ret .= "?op=itemOut&toDo=modify&id=". $itemDenorm->getVar('id') . "\"> ";
			$ret .= "<div>";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=itemOutNew&cause=" . $itemDenorm->getVar('cause');
			$ret .= "&batch=" . $itemDenorm->getVar('batch');
			$ret .= "&kind=" . $itemDenorm->getVar('kind');
			$ret .= "&code=" . $itemDenorm->getVar('code');
			$ret .= "&name=" . $itemDenorm->getVar('name');
			$ret .= "&qty=" . $itemDenorm->getVar('qty');
			$ret .= "&producer=" . $itemDenorm->getVar('producer');
			$ret .= "&yearProd=" . $itemDenorm->getVar('yearProd');
			$ret .= "&description=" . $itemDenorm->getVar('description');
			$ret .= "\">Copia come nuovo";
			$ret .= "</a>";
			$ret .= "</div>";
		}
		$ret .= "<input type=\"hidden\" name=\"itemDenormId\" value=\"" . $itemDenorm->getVar('id') . "\" />";
		if(($koBitArray & 0x1) == 0x1) {
			$ret .= "<div class=\"error\">Causale errata</div>";
			$ret .= "<br />";
		}
		$ret .= "<div class=\"label\">Causale:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<select name=\"cause\">";
		$tmpArr = $causeMan->getModels(null,$filterCauses,'name');
		$causes = $tmpArr["result"];
		foreach ($causes as $gotCause) {
			$ret .= "<option value=\"". $gotCause->getVar('id');
			if($itemDenorm->getVar('cause') == $gotCause->getVar('id')) {
				$ret .= "\" selected=\"selected";
			}
			$ret .= "\">". $gotCause->getVar('name') ."</option>";
		}
		$ret .= "</select>";
		$ret .= "</div><br />";
		if(($koBitArray & 0x2) == 0x2) {
			$ret .= "<div class=\"error\">Categoria errata</div>";
			$ret .= "<br />";
		}
		$ret .= "<div class=\"label\">Lotto:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"batch\" value=\"" . $itemDenorm->getVar('batch') . "\" />";
		$ret .= "</div><br />";
		$ret .= "<div class=\"label\">Categoria:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"kind\" value=\"" . $itemDenorm->getVar('kind') . "\" />";
		$ret .= "</div><br />";
		if(($koBitArray & 0x4) == 0x4) {
			$ret .= "<div class=\"error\">Codice errato</div>";
			$ret .= "<br />";
		}
		$ret .= "<div class=\"label\">Codice:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"code\" value=\"" . $itemDenorm->getVar('code') . "\" />";
		$ret .= "</div><br />";
		if(($koBitArray & 0x8) == 0x8) {
			$ret .= "<div class=\"error\">Nome errato</div>";
			$ret .= "<br />";
		}
		$ret .= "<div class=\"label\">Nome:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"name\" value=\"" . $itemDenorm->getVar('name') . "\" />";
		$ret .= "</div><br />";
		if(($koBitArray & 0x10) == 0x10) {
			$ret .= "<div class=\"error\">Quantita' errata</div>";
			$ret .= "<br />";
		}
		$ret .= "<div class=\"label\">Quantita':</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"qty\" value=\"" . $itemDenorm->getVar('qty') . "\" />";
		$ret .= "</div><br />";
		$ret .= "<div class=\"label\">Produttore:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"producer\" value=\"" . $itemDenorm->getVar('producer') . "\" />";
		$ret .= "</div><br />";
		$ret .= "<div class=\"label\">Anno di produzione:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"yearProd\" value=\"" . $itemDenorm->getVar('yearProd') . "\" />";
		$ret .= "</div><br />";
		$ret .= "<div class=\"label\">Anno proprio documento:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"ownDocumentYear\" value=\"" . $itemDenorm->getVar('ownDocumentYear') . "\" />";
		$ret .= "</div><br />";
		$ret .= "<div class=\"label\">Codice/Numero proprio documento:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"ownDocumentCode\" value=\"" . $itemDenorm->getVar('ownDocumentCode') . "\" />";
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