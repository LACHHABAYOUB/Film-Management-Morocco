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

page_header ($USER, 2, $db) ;
echo make_left_column ("admin", $RIGHTS, $left_lib) ;

$ARRAY_hote = search_row_by_string ("user", "user_login", $USER, $db, TABLEAU) ;

echo "\t\t\t<h1>".PASSWORD_CHANGE."</h1>\r\n" ;

if (!isset($_GET['status'])) {
	// affichage du formulaire pour la saisie
	$ARRAY_error['current_password'] = $ARRAY_error['new_password'] = $ARRAY_error['new_password_confirm'] = "" ;
	password_form ($ARRAY_hote, $ARRAY_error, $db) ;
} elseif ($_GET['status'] == "submit") {
	if (isset ($_POST['change_password'])) {
		if ($_POST['change_password'] == "in_progress") {
			// contrôle de saisie
			$ARRAY_error = password_change_control ($_POST) ;
			if (($ARRAY_error['current_password'] != "") or ($ARRAY_error['new_password'] != "") or ($ARRAY_error['new_password_confirm'] != "")) {
				// réaffichage du formulaire avec message(s) d'erreur en rouge
				password_form ($ARRAY_hote, $ARRAY_error, $db) ;
			} else {
				$feedback = $warning = "" ;
				$user_login = $_POST['user_login'] ;
				$md5pwd = md5 ($_POST['new_password']) ;
				// authentification de l'utilisateur	($error, $location, $footer_lib, $db )
				if ($user_login != $USER) error_page ("fatal error", "admin", $footer_lib, $db) ;
				// l'ancien mot de passe est correct
				$sql_pwd = "UPDATE user SET user_pass_md5 = '$md5pwd' WHERE user_login = '$user_login' LIMIT 1 " ;
				$db->query ($sql_pwd);
				if (mysql_affected_rows() == 1) $feedback = $user_login." , ".OK_PWD_UPDATE ;
				else $warning = NOK_PWD_UPDATE ;
				// affichage du message de feedback
				display_in_box ($feedback, $warning) ;
			}
		}
	}
}

page_footer($footer_lib) ;

function password_form ($ARRAY_hote, $ARRAY_error, $db) {
	// Traitement des caracteres speciaux HTML
	foreach ($ARRAY_hote as $clef => $valeur) $ARRAY_hote[$clef] = htmlspecialchars(stripslashes($valeur));
	startform ("password_form", "index.php?location=admin&amp;action=password_change&amp;status=submit", PASSWORD_MY) ;
	hidden_field ("change_password", "in_progress") ;
	hidden_field ("user_history", $ARRAY_hote['user_history']) ;
	hidden_field ("user_login", $ARRAY_hote['user_login']) ;
	hidden_field ("user_pass_md5", $ARRAY_hote['user_pass_md5']) ;
	lib_field (USERNAME, $ARRAY_hote['user_login']) ;
	password_field (PASSWORD_CURRENT, "current_password",  "", $ARRAY_error['current_password']) ;
	password_field (PASSWORD_NEW, "new_password",  "", $ARRAY_error['new_password']) ;
	password_field (CONFIRM_IT, "new_password_confirm",  "", $ARRAY_error['new_password_confirm']) ;
	end_entete () ;
	submit_field ("&nbsp;", "validate", SAVE_CHANGES) ;
	stopform () ;
}
function password_change_control ($ARRAY_hote) {
	// initialisation de toutes les erreurs
	$ARRAY_error['current_password'] = $ARRAY_error['new_password'] = $ARRAY_error['new_password_confirm'] = "" ;
	if (md5($ARRAY_hote['current_password']) != $ARRAY_hote['user_pass_md5']) {
		$ARRAY_error['current_password'] .= NOK_PASSWORD ;
		return $ARRAY_error ;
	}
	if(preg_match('/[^0-9A-Za-z_@:]/',$ARRAY_hote['new_password'])) $ARRAY_error['new_password'] .= NOK_CHAR ;
	if (strlen ($ARRAY_hote['new_password']) < 8) $ARRAY_error['new_password'] .= NOK_PWD_LEN ;
	if ($ARRAY_hote['new_password_confirm'] != $ARRAY_hote['new_password']) $ARRAY_error['new_password_confirm'] .= NOK_PWD_COMPARE ;
	return $ARRAY_error ;
}
?>