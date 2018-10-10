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

// autorisation d'accès au script
if ($RIGHTS == "visitor") error_page (ERR_ACCESS, "media", $RIGHTS, $footer_lib, $db ) ;

require_once("admin/admin.php") ;

page_header ($USER, 2, $db) ;
echo make_left_column ("admin", $RIGHTS, $left_lib) ;

$ARRAY_account = search_row_by_string ("user", "user_login", $USER, $db, TABLEAU) ;

// titre + bouton pour changer le mot de passe
echo "\t\t\t<h1>".LIBM_MY_ACCOUNT.fill_it(50,10)."<a href=\"index.php?location=admin&amp;action=password_change\" class=\"red_button\" title=\"".PASSWORD_CHANGE."\">".PASSWORD_CHANGE."</a></h1>\r\n" ;


if (!isset($_GET['status'])) {
	// affiche le formulaire pour la saisie
	user_form ($ARRAY_account, "one", $db) ;
} elseif ($_GET['status'] == "submit") {
	if (isset ($_POST['edit_user_control'])) {
		if ($_POST['edit_user_control'] == "in_progress") {
			$feedback = $warning = "" ;
			// préparation et écriture des données reçues dans le _POST
			if (isset($_POST['user_first_name'])) $user_first_name = $db->real_escape_string($_POST['user_first_name']) ;
			else $user_first_name = "" ;
			if (isset($_POST['user_last_name'])) $user_last_name = $db->real_escape_string($_POST['user_last_name']) ;
			else $user_last_name = "" ;
			if (isset($_POST['user_adress'])) $user_adress = $db->real_escape_string($_POST['user_adress']) ;
			else $user_adress = "" ;
			if (isset($_POST['user_postcode'])) $user_postcode = $db->real_escape_string($_POST['user_postcode']) ;
			else $user_postcode = "" ;
			if (isset($_POST['user_city'])) $user_city = $db->real_escape_string($_POST['user_city']) ;
			else $user_city = "" ;
			$nom_pays = $_POST['user_country_name'] ;
			$country = "country_" . LANG ;
			$OBJ_country = search_row_by_string ("country", $country, $nom_pays, $db) ;
			$user_country_id = $OBJ_country->country_id ;
			$user_lang = $_POST['user_lang'] ;
			$write_sql = "UPDATE `user` SET `user_first_name` = '$user_first_name', `user_last_name` = '$user_last_name', `user_adress` = '$user_adress', `user_postcode` = '$user_postcode', `user_city` = '$user_city', `user_country_id` = '$user_country_id', `user_lang` = '$user_lang' WHERE `user_login` = '$USER';" ;
			$db->query ($write_sql) ;
			if (mysql_affected_rows() == 1) {
				// historique de ma mise à jour
				$user_update_date = date("U") ;
				$update_date = timestamp_to_datetime_fr ($user_update_date) ;
				$user_history = $_POST['user_history'] . "\r\n##### Mis à jour le : ".$update_date." #####" ;
				$write_done_sql = "UPDATE `user` SET `user_revised_date` = '$user_update_date', `user_history` = '$user_history' WHERE `user_login` = '$USER';" ;
				$db->query ($write_done_sql) ;
				$feedback = OK_UPDATE ;
			} else $warning = NO_UPDATE_DONE ;
			// affichage du message de feedback
			display_in_box ($feedback, $warning) ;
			// affichage du formulaire pour relecture
			$ARRAY_account['user_country_id'] = $user_country_id ;
			$ARRAY_account['user_lang'] = $user_lang ;
			user_form ($ARRAY_account, "two", $db) ;
		}
	} else page_footer($footer_lib) ;
}

page_footer($footer_lib) ;

function user_form ($ARRAY_account, $step, $db) {
	startform ("user_form", "index.php?location=admin&amp;action=my_account&amp;status=submit", MY_INFOS) ;
	hidden_field ("edit_user_control", "in_progress") ;
	hidden_field ("user_history", $ARRAY_account['user_history']) ;
	lib_field (USERNAME, $ARRAY_account['user_login']) ;
	// champs texte éditables
	text_field (LAB_FIRST_NAME, "user_first_name",  $ARRAY_account['user_first_name'], 2) ;
	text_field (LAB_LAST_NAME, "user_last_name",  $ARRAY_account['user_last_name'], 2) ;
	text_field (LAB_ADRESS, "user_adress",  $ARRAY_account['user_adress'], 2) ;
	text_field (LAB_POSTCODE, "user_postcode",  $ARRAY_account['user_postcode'], 2) ;
	text_field (LAB_CITY, "user_city",  $ARRAY_account['user_city'], 2) ;
	// recherche du pays courant et de la liste des pays
	$country = "country_" . LANG ;
	$le_pays = get_field_by_id ("country", $country, "country_id", $ARRAY_account['user_country_id'], $db) ;
	$result = $db->query("SELECT $country FROM country ORDER BY $country ASC ");
	while ($OBJ_row = $db->fetch_object($result)) {;
		if (LANG == "fr") $liste_pays[$OBJ_row->country_fr] = $OBJ_row->country_fr ;
		else $liste_pays[$OBJ_row->country_en] = $OBJ_row->country_en ;
	}
	// champs select pour sélectionner le pays
	echo select_field (SELECT_COUNTRY, "user_country_name", $le_pays, $liste_pays) ;
	// champs radio pour la langue
	$lang_option = array ("en"=>"English", "fr"=>"Fran&ccedil;ais") ;
	echo (radio_field ("PREF_LANG", "user_lang", $ARRAY_account['user_lang'], $lang_option)) ;
	end_entete () ;
	if ($step == "one") submit_field ("&nbsp;", "validate", SAVE_CHANGES) ;
	stopform () ;
}
?>