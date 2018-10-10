<?php
/***************************************************************
* Copyright notice - Notice de droits d'auteur
*
* © 2011-2021 Christian ECKENSPIELLER (ce@ceck.org)
* Internet site : ceck.org
* All rights reserved
*
* The GCM project is a free software, you can redistribute it
and/or modify it under the terms of the GNU General Public
License as published by the Free Software Foundation.
*
* This script is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of 
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
General Public License for more details :
[http://www.gnu.org/copyleft/gpl.html].
*
* This copyright notice MUST APPEAR in all copies of the script.
* Cette notice de droits d'auteur DOIT APPARAITRE dans toutes
les copies des scripts.
****************************************************************
*/

require_once("install/installer_tools.php");

if (isset ($_GET['step'])) $step = $_GET['step'] ;
else $step = 1 ;

switch ($step) {
	case 1:
		install_begin ($step) ;
		echo "\t\t<h2>" . ENTETE_1 . "</h2>\r\n" ;
		echo "\t\t<p>" . INTRO_ONE . "</p>\r\n" ;
		$ARRAY_preset = array("site"=>"","db_username"=>"root","db_password"=>"","db_host"=>"localhost","db_noun"=>"") ;
		$ARRAY_error = array("site"=>"","db_username"=>"","db_password"=>"","db_host"=>"","db_noun"=>"") ;
		config_form ($ARRAY_preset, $ARRAY_error) ;
	break ;
	case 2:
		install_begin ($step) ;
		$ARRAY_error = config_control ($_POST) ;
		if (($ARRAY_error['site'] != "") or ($ARRAY_error['db_username'] != "" or ($ARRAY_error['db_password'] != "") or ($ARRAY_error['db_host'] != "") or ($ARRAY_error['db_noun'] != ""))) {
			echo "\t\t<h2>" . ENTETE_1 . "</h2>\r\n" ;
			echo "\t\t<p>" . INTRO_ONE . "</p>\r\n" ;
			config_form ($_POST, $ARRAY_error) ;
		} else {
			echo "\t\t<h2>" . ENTETE_2_1 . "</h2>\r\n" ;
			//	Verif compatibilite systeme
			echo "\t\t\t<p class=\"okicon\">".PHP_VERSION_IS. phpversion() . "<img src=\"install/img/ok.png\" width=\"24\" height=\"24\" alt=\"ok\" /></p>\r\n" ;
			// Construction fichier localconf.php
			$feedback = config_write ($_POST) ;
			display_in_box ($feedback, "") ;
			echo "\t\t<h2>" . ENTETE_2_2 . "</h2>\r\n" ;
			// Test connexion à la base de données
			require_once("localconf.php");
			$feedback = $warning = $error = "" ;
			@ $db = new ceckdb (DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NOUN) ;
			if (is_object($db)) $error = $db->error_code ;
			if ((!is_object($db)) or ($error === 1)) {
				$warning = NOK_DB_CONNECTION ;
				display_in_box ("", $warning) ;
				echo "\t\t\t<h1><a href=\"install.php?step=1\" class=\"red_button\" title=\"".BACK_STEP."\">".BACK_STEP."</a></h1>\r\n" ;
			} else {
				$feedback = OK_DB_CONNECTION ;
				display_in_box ($feedback, "") ;
				echo "\t\t\t<h1><a href=\"install.php?step=3\" class=\"green_button\" title=\"".CONFIRM_STEP."\">".CONFIRM_STEP."</a></h1>\r\n" ;
			}
		}
	break ;
	case 3:
		install_begin ($step) ;
		echo "\t\t<h2>" . ENTETE_3 . "</h2>\r\n" ;
		require_once("localconf.php");
		@ $db = new ceckdb (DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NOUN) ;
		$db->listen_talk("utf8") ;
		// création des tables et insertion des données dans la base
		$retour = create_tables ($db) ;
		$feedback = $retour[0] ;
		$warning = $retour[1] ;
		display_in_box ($feedback, $warning) ;
		echo "\t\t\t<h1><a href=\"install.php?step=4\" class=\"green_button\" title=\"".CONFIRM_STEP."\">".CONFIRM_STEP."</a></h1>\r\n" ;
	break ;
	case 4:
		install_begin ($step) ;
		echo "\t\t<h2>" . ENTETE_4 . "</h2>\r\n" ;
		$ARRAY_preset = array("admin_username"=>"","admin_mail"=>"","password"=>"","password_confirm"=>"") ;
		$ARRAY_error = array("admin_username"=>"","admin_mail"=>"","password"=>"","password_confirm"=>"") ;
		admin_account_form ($ARRAY_preset, $ARRAY_error) ;
	break ;
	case 5:
		install_begin ($step) ;
		$ARRAY_error = admin_account_control ($_POST) ;
		if (($ARRAY_error['admin_username'] != "" or ($ARRAY_error['admin_mail'] != "") or ($ARRAY_error['password'] != "") or ($ARRAY_error['password_confirm'] != ""))) {
			echo "\t\t<h2>" . ENTETE_4 . "</h2>\r\n" ;
			admin_account_form ($_POST, $ARRAY_error) ;
		} else {
			echo "\t\t<h2>" . ENTETE_5_1 . "</h2>\r\n" ;
			require_once("localconf.php");
			@ $db = new ceckdb (DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NOUN) ;
			$db->listen_talk("utf8") ;
			// création du compte administrateur
			$retour = admin_account_write ($_POST, $db) ;
			$feedback = $retour[0] ;
			$warning = $retour[1] ;
			display_in_box ($feedback, $warning) ;
			echo "\t\t<p>" . INTRO_END . "</p>\r\n" ;
			echo "\t\t\t<h1><a href=\"index.php?location=media&action=none\" class=\"green_button\" title=\"".GO_HOME."\">".GO_HOME."</a></h1>\r\n" ;
			echo "\t\t<h2>" . ENTETE_5_2 . "</h2>\r\n" ;
			unlink ("install/ENABLE_INSTALL") ;
		}
	break ;
}
install_end () ;
?>