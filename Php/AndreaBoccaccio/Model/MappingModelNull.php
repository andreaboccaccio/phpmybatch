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
class Php_AndreaBoccaccio_Model_MappingModelNull extends Php_AndreaBoccaccio_Model_MappingModelAbstract {
	
	private static $instance = null;
	
	private function __clone() {
	
	}
	
	private function __construct() {
		$this->setKind('null');
	}
	
	public static function getInstance() {
		if(self::$instance == null) {
			self::$instance = new Php_AndreaBoccaccio_Model_MappingModelNull();
		}
		return self::$instance;
	}
	
	public function getDbTabName() {
		return null;
	}
	
	public function getVarName($appName=null, $dbName=null) {
		return null;
	}
	
	public function getVarKind($appName) {
		return null;
	}
	
	public function getDefaults() {
		return null;
	}
}