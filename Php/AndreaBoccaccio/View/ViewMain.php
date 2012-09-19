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
class Php_AndreaBoccaccio_View_ViewMain extends Php_AndreaBoccaccio_View_ViewConsistentAbstract {
	
	private static $instance = null;
	
	private function __clone() {
	
	}
	
	private function __construct() {
		$this->setKind('main');
	}
	
	public static function getInstance() {
		if(self::$instance == null) {
			self::$instance = new Php_AndreaBoccaccio_View_ViewMain();
		}
		return self::$instance;
	}
	
	public function getMenu() {
		$ret = parent::getMenu();
		
		$ret .= "<div id=\"mainDocList\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=docList\">Documento</a>";
		$ret .= "</div>\n";
		$ret .= "<div id=\"mainItemOutList\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=itemOutList\">Scarichi</a>";
		$ret .= "</div>\n";
		$ret .= "<div id=\"mainItemOutList\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=itemOutWizard\">Scarichi Guidati</a>";
		$ret .= "</div>\n";
		$ret .= "<div id=\"mainCauseList\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=causeList\">Causali</a>";
		$ret .= "</div>\n";
		$ret .= "<div id=\"mainSqlQueriesList\" class=\"menuentry\">\n";
		$ret .= "<a href=\"" . $_SERVER["PHP_SELF"] . "?op=sqlQueries\">Interrogazioni</a>";
		$ret .= "</div>\n";
		$ret .= "</div>\n";
		
		return $ret;
	}
	
	public function getBody() {
		$ret = '';
		$mytime = time();
		
		$ret .= "<div id=\"body\">";
		$ret .= "Benvenuti in phpmywhs";
		$ret .= "<p>Chiamata a time()" . $mytime ."</p>";
		$ret .= "<p>Local server datetime " . strftime('%Y%m%d %H%M%S',$mytime) ."</p>";
		$ret .= "<p>UTC datetime " . gmstrftime('%Y%m%d %H%M%S',$mytime) ."</p>";
		$ret .= "</div>";
		
		return $ret;
	}
}