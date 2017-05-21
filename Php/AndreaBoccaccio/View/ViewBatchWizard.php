<?php
/*
 * phpmybatch - An open source batches of goods management system software.
 * Copyright (C)2012 Andrea Boccaccio
 * contact email: andrea@andreaboccaccio.it
 * 
 * This file is part of phpmybatch.
 * 
 * phpmybatch is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * phpmybatch is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with phpmybatch. If not, see <http://www.gnu.org/licenses/>.
 * 
 */
class Php_AndreaBoccaccio_View_ViewBatchWizard extends Php_AndreaBoccaccio_View_ViewWizardAbstract {
	
	private static $instance = null;
	
	private function __clone() {
	
	}
	
	private function __construct() {
		$this->setKind('batchWizard');
		$this->setWizard(Php_AndreaBoccaccio_Model_BatchWizard::getInstance());
		$this->setQueryId('newBatch');
	}
	
	public static function getInstance() {
		if(self::$instance == null) {
			self::$instance = new Php_AndreaBoccaccio_View_ViewBatchWizard();
		}
		return self::$instance;
	}
}