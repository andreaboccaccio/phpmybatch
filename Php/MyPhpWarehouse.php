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
class Php_MyPhpWarehouse implements Php_AppInterface {
	
	public function myMain() {
		$securityLevel = 100;
		$sessionCode = '';
		$newSessionCode = '';
		$usr = '';
		$pwd = '';
		$loggedIn = 0;
		$settingsFact = Php_AndreaBoccaccio_Settings_SettingsFactory::getInstance();
		$loginFact = Php_AndreaBoccaccio_Login_LoginFactory::getInstance();
		$dbFact = Php_AndreaBoccaccio_Db_DbFactory::getInstance();
		$viewFact = Php_AndreaBoccaccio_View_ViewFactory::getInstance();
		$settings = $settingsFact->getSettings('xml');
		$db = $dbFact->getDb($settings->getSettingFromFullName('classes.db'));
		$login = $loginFact->getLogin($settings->getSettingFromFullName('classes.login'));
		$view;
		$strToShow = '';
		$opToDo = '';
		if(isset($_COOKIE["phpmywhssession"])) {
				$sessionCode = $_COOKIE["phpmywhssession"];
			if(isset($_GET["doLogout"])) {
				$login->logout($sessionCode);
				$newSessionCode = '';
			}
			else {
				$sessionCode = $db->sanitize($sessionCode);
				$newSessionCode = $login->getNewSessionCode(null,null,$sessionCode);
			}
		}
		else if((isset($_GET["doLogin"]))&&(isset($_POST["usr"]))&&(isset($_POST["pwd"]))) {
			if((!is_null($_GET["doLogin"]))&&(!is_null($_POST["usr"]))&&(!is_null($_POST["pwd"]))) {
				if((strncmp($_GET["doLogin"],'yes',strlen('yes'))==0)&&(strlen($_POST["usr"])>0)&&(strlen($_POST["pwd"])>0)) {
					$usr = $db->sanitize($_POST["usr"]);
					$pwd = $db->sanitize($_POST["pwd"]);
					$newSessionCode = $login->getNewSessionCode($usr,$pwd,null);
				}
			}
		}
		if($login->getUserLevel($newSessionCode)>=$securityLevel)
		{
			setcookie("phpmywhssession",$newSessionCode,time()+intval($settings->getSettingFromFullName('session.persistence')));
			$loggedIn = 1;
		}
		else {
			setcookie("phpmywhssession",'',time()-(60*60*24*30));
			$loggedIn = 0;
		}
		
		$strToShow = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n";
		$strToShow .= "\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">";
		$strToShow .= "<html xmlns=\"http://www.w3.org/1999/xhtml\">";
		$strToShow .= "<head>";
		$strToShow .= "<title>phpmybatch by Andrea Boccaccio</title>";
		$strToShow .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"css/main.css\" />";
		$strToShow .= "</head>";
		$strToShow .= "<body>\n";
		
		if($loggedIn) {
			if((isset($_GET["op"]))&&(!isset($_GET["doLogin"]))) {
				if(!is_null($_GET["op"])) {
					if(strlen($_GET["op"])>0) {
						$opToDo = $db->sanitize($_GET["op"]);
					}
					else {
						$opToDo = 'main';
					}
				}
				else {
					$opToDo = 'main';
				}
			}
			else {
				$opToDo = 'main';
			}
			$view = $viewFact->getView($opToDo);
		}
		else {
			$view = $viewFact->getView('login');
		}
		
		$strToShow .= $view->getBanner();
		$strToShow .= $view->getMenu();
		$strToShow .= $view->getBody();
		$strToShow .= $view->getFooter();
		
		$strToShow .= "</body>\n";
		$strToShow .= "</html>\n";
		echo $strToShow;
	}
}