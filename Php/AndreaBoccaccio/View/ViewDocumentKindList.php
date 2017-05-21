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
class Php_AndreaBoccaccio_View_ViewDocumentKindList extends Php_AndreaBoccaccio_View_ViewConsistentAbstract {

	private static $instance = null;

	private function __clone() {

	}

	private function __construct() {
		$this->setKind('docKindList');
	}

	public static function getInstance() {
		if(self::$instance == null) {
			self::$instance = new Php_AndreaBoccaccio_View_ViewDocumentKindList();
		}
		return self::$instance;
	}

	public function getMenu() {
		$ret = parent::getMenu();

		$ret .= "<div id=\"tipoDocumento\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=main\">Principale</a>";
		$ret .= "</div>\n";
		$ret .= "<div id=\"tipoDocumento\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=docKindNew\">Nuovo Tipo Documento</a>";
		$ret .= "</div>\n";
		$ret .= "</div>\n";

		return $ret;
	}

	public function getBody() {
		$ret = '';
		$docKindMan = new Php_AndreaBoccaccio_Model_DocumentKindManager();
		$docKinds = $docKindMan->getDocs();
		$i = -1;
		$max = count($docKinds);
		
		$ret .= "<div id=\"body\">";
		$ret .= "<div id=\"listDocKinds\" class=\"list\">";
		$ret .= "<table id=\"tabDocKinds\" class=\"tab\">";
		$ret .= "<tr class=\"tab\">";
		$ret .= "<th class=\"tab\">codice</th>";
		$ret .= "<th class=\"tab\">nome</th>";
		$ret .= "<th class=\"tab\">descrizione</th>";
		$ret .= "<th class=\"tab\">cancellazione</th>";
		$ret .= "</tr>";
		for($i = 0; $i < $max; ++$i) {
			$ret .= "<tr class=\"tab\">";
			$ret .= "<td class=\"tab\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=docKind&id=" . $docKinds[$i]->getId();
			$ret .= "\">" . $docKinds[$i]->getCode() ."</a>";
			$ret .= "</td>";
			$ret .= "<td class=\"tab\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=docKind&id=" . $docKinds[$i]->getId();
			$ret .= "\">" . $docKinds[$i]->getName() ."</a>";
			$ret .= "</td>";
			$ret .= "<td class=\"tab\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=docKind&id=" . $docKinds[$i]->getId();
			$ret .= "\">" . $docKinds[$i]->getDescription() ."</a>";
			$ret .= "</td>";
			$ret .= "<td class=\"tab\">";
			$ret .= "<a href=\"" . $_SERVER["PHP_SELF"];
			$ret .= "?op=docKind&id=" . $docKinds[$i]->getId();
			$ret .= "&delete=maybe\">cancella</a>";
			$ret .= "</td>";
			$ret .= "</tr>";
		}
		$ret .= "</table>";
		$ret .= "</div>";
		$ret .= "</div>";

		return $ret;
	}
}