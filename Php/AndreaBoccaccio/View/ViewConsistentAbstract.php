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
abstract class Php_AndreaBoccaccio_View_ViewConsistentAbstract extends Php_AndreaBoccaccio_View_ViewAbstract {

	public function getBanner() {
		$ret = '';
		
		$ret = "<div id=\"banner\">\n";
		$ret .= "phpmybatch: un'applicazione libera in divenire riguardo la rintracciabilita' dei lotti\n";
		$ret .= "</div>\n";
		
		return $ret;
	}
	
	public function getMenu() {
		$ret = '';
		
		$ret = "<div id=\"menu\">\n";
		$ret .= "<div id=\"logout\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?doLogout=yes\">Esci</a>";
		$ret .= "</div>\n";
		
		return $ret;
	}
	
	public function getFooter() {
		
		$ret = '';
		
		$ret = "<div id=\"footer\">";
		$ret .= "<a href=\"https://github.com/andreaboccaccio/phpmybatch\">";
		$ret .= "qui";
		$ret .= "</a>";
		$ret .= " si trova il codice sorgente di questo applicativo.";
		$ret .= "</div>";
		
		return $ret;
	}
}