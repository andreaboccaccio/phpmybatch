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
class Php_AndreaBoccaccio_View_ViewItemInsert extends Php_AndreaBoccaccio_View_ViewConsistentAbstract {

	private static $instance = null;

	private function __clone() {

	}

	private function __construct() {
		$this->setKind('itemNew');
	}

	public static function getInstance() {
		if(self::$instance == null) {
			self::$instance = new Php_AndreaBoccaccio_View_ViewItemInsert();
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
		$ret .= "\">Lotti</a>";
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
				if(strncmp($_GET["toDo"],'save',strlen('save'))==0) {
					if(isset($_GET["docId"])) {
						if(preg_match("/^\d+$/", $_GET["docId"])) {
							$koBitArray = $koBitArray & 0x7ffffffe;
							$initArray["document"] = $db->sanitize($_GET["docId"]);
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
					if(isset($_POST["batch"])) {
						$initArray["batch"] = $db->sanitize($_POST["batch"]);
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
					if(isset($_POST["batch_orig"])) {
						$initArray["batch_orig"] = $db->sanitize($_POST["batch_orig"]);
					}
					if(isset($_POST["kg"])) {
						$initArray["kg"] = $db->sanitize(str_replace(",", ".", $_POST["kg"]));
					}
					if(isset($_POST["arrival"])) {
						$initArray["arrival"] = $db->sanitize($_POST["arrival"]);
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
			}
		}

		$ret .= "<div id=\"body\">";
		$ret .= "<form method=\"post\" action=\"";
		$ret .= $_SERVER["PHP_SELF"];
		$ret .= "?op=itemNew&toDo=save&docId=";
		$ret .= $_GET["docId"] . "\"> ";
		$ret .= "<div class=\"label\">Nazione:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<select name=\"country\">";
		$tmpArr = $countryMan->getModels(null,$filterCountries,'codealpha2');
		$countries = $tmpArr["result"];
		foreach ($countries as $gotCountry) {
			$ret .= "<option value=\"". $gotCountry->getVar('id') . "\"";
			if(isset($_GET["country"])) {
				if(!is_null($_GET["country"])) {
					if(strlen($_GET["country"])>0) {
						if(intval($_GET["country"]) == intval($gotCountry->getVar('id'))) {
							$ret .= " selected=\"selected\"";
						}
					}
				}
			}
			$ret .= "\">". $gotCountry->getVar('codealpha2') . '-' . $gotCountry->getVar('enname') . "</option>";
		}
		$ret .= "</select>";
		$ret .= "</div><br />";
		$ret .= "<div class=\"label\">Dipartimento di prov.:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"district\"";
		if(isset($_GET["district"])) {
			if(!is_null($_GET["district"])) {
				if(strlen($_GET["district"])>0) {
					$ret .= " value=\"" . $_GET["district"] . "\"";
				}
			}
		}
		$ret .= " />";
		$ret .= "</div><br />";
		$ret .= "<div class=\"label\">N. stab. CEE:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"stabCEE\"";
		if(isset($_GET["stabCEE"])) {
			if(!is_null($_GET["stabCEE"])) {
				if(strlen($_GET["stabCEE"])>0) {
					$ret .= " value=\"" . $_GET["stabCEE"] . "\"";
				}
			}
		}
		$ret .= " />";
		$ret .= "</div><br />";
		$ret .= "<div class=\"label\">Lotto Macellazione:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"batch_orig\"";
		if(isset($_GET["batch_orig"])) {
			if(!is_null($_GET["batch_orig"])) {
				if(strlen($_GET["batch_orig"])>0) {
					$ret .= " value=\"" . $_GET["batch_orig"] . "\"";
				}
			}
		}
		$ret .= " />";
		$ret .= "</div><br />";
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
		$ret .= " />";
		$ret .= "</div><br />";
		if(($koBitArray & 0x2) == 0x2) {
			$ret .= "<div class=\"error\">Categoria errata</div>";
			$ret .= "<br />";
		}
		$ret .= "<div class=\"label\">Categoria:</div>";
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
		$ret .= "</div><br />";
		if(($koBitArray & 0x10) == 0x10) {
			$ret .= "<div class=\"error\">Colli errati</div>";
			$ret .= "<br />";
		}
		$ret .= "<div class=\"label\">Colli:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"qty\"";
		if($koBitArray != 0x0) {
			$ret .= " value=\"" . $_POST["qty"] . "\"";
		} else if(isset($_GET["qty"])) {
			if(!is_null($_GET["qty"])) {
				if(strlen($_GET["qty"])>0) {
					$ret .= " value=\"" . $_GET["qty"] . "\"";
				}
			}
		}
		$ret .= " />";
		$ret .= "</div><br />";
		$ret .= "<div class=\"label\">Kg:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"kg\"";
		if(isset($_GET["kg"])) {
			if(!is_null($_GET["kg"])) {
				if(strlen($_GET["kg"])>0) {
					$ret .= " value=\"" . number_format(floatval($_GET["kg"]),2,',','') . "\"";
				}
			}
		}
		$ret .= " />";
		$ret .= "</div><br />";
		if(($koBitArray & 0x20) == 0x20) {
			$ret .= "<div class=\"error\">Data di arrivo errata formato corretto GG/MM/AAAA</div>";
			$ret .= "<br />";
		}
		$ret .= "<div class=\"label\">Data di arrivo:</div>";
		$ret .= "<div class=\"input\">";
		$ret .= "<input type=\"text\" name=\"arrival\"";
		if($koBitArray != 0x0) {
			$ret .= " value=\"" . $_POST["arrival"] . "\"";
		} else if(isset($_GET["arrival"])) {
			if(!is_null($_GET["arrival"])) {
				if(strlen($_GET["arrival"])>0) {
					$ret .= " value=\"" . $_GET["arrival"] . "\"";
				}
			}
		}
		$ret .= " />";
		$ret .= "</div><br />";
		if(($koBitArray & 0x80) == 0x80) {
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
		$ret .= "</div><br />";
		$ret .= "<div class=\"submit\">";
		$ret .= "<input type=\"submit\" value=\"Salva\" />";
		$ret .= "</div>";
		$ret .= "</form>";
		$ret .= "</div>";

		return $ret;
	}
}