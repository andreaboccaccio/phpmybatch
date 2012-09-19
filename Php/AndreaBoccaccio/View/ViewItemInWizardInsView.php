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
class Php_AndreaBoccaccio_View_ViewItemInWizardInsView extends Php_AndreaBoccaccio_View_ViewWizardAbstract {
	
	private static $instance = null;
	
	private function __clone() {
	
	}
	
	private function __construct() {
		$tmpCustomGet = '';
		
		$this->setKind('itemInWizard');
		$this->setWizard(Php_AndreaBoccaccio_Model_ItemInWizard::getInstance());
		$this->setQueryId('newItemIn');
		
		if(isset($_GET["docId"])) {
			$tmpCustomGet = "&docId=" . $_GET["docId"];
			$this->setCustomGet($tmpCustomGet);	
		}
	}
	
	public static function getInstance() {
		if(self::$instance == null) {
			self::$instance = new Php_AndreaBoccaccio_View_ViewItemInWizardInsView();
		}
		return self::$instance;
	}
}