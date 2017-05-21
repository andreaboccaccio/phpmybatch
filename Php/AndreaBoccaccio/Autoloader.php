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
 * along with phpmywhs.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
Class Php_AndreaBoccaccio_Autoloader {
	
	static private $instance = null;
	
	private function __construct()
	{
		spl_autoload_register(array($this,'loader'));
	}
	
	private function __clone() {
		
	}
	
	static public function getInstance()
	{
		if (self::$instance == null)
		{
			self::$instance = new Php_AndreaBoccaccio_Autoloader();
		}
		return self::$instance;
	}
	
	public function loader($className) {
		if(strncmp($className,'FPDF',strlen('FPDF')) == 0) {
			$fileToRequire = 'Php/lib/fpdf/' . strtolower($className) . '.php';
		}
		else {
			$fileToRequire = str_replace('_','/',$className) . '.php';
		}
		require_once $fileToRequire;
	}
}

Php_AndreaBoccaccio_Autoloader::getInstance();