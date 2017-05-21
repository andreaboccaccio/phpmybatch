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
abstract class Php_AndreaBoccaccio_SingletonFactoryAbstract extends Php_AndreaBoccaccio_FactoryAbstract {
	
	protected function init() {
	
		foreach ($this->getClasses() as $tmpStrCl) {
			$tmpCl = $tmpStrCl::getInstance();
			$this->classArray[$tmpCl->getKind()] = $tmpCl;
		}
	}
	
	public function getClass($kind) {
		$ret = null;
		if(array_key_exists($kind, $this->classArray)) {
			$ret = $this->classArray[$kind];
		}
		else {
			$ret = $this->classArray[$this->classDefault];
		}
		return $ret;
	}
}