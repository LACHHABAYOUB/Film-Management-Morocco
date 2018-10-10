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

/* 4 types d'utilisateurs (user_rights) :
visitor		member		private		administrator
*/
function register_form ($option, $ARRAY_hote, $ARRAY_error, $db) {
	foreach ($ARRAY_hote as $clef => $valeur) $ARRAY_hote[$clef] = htmlspecialchars(stripslashes($valeur));
	if ($option == "new"){
		startform ("register_form", "index.php?location=admin&amp;action=register", I_REGISTER) ;
		hidden_field ("option", $option) ;
		text_field (MY_EMAIL, "user_email",  $ARRAY_hote['user_email'], 2, $ARRAY_error['user_email']) ;
		text_field (MY_USERNAME, "user_login",  $ARRAY_hote['user_login'], 1, $ARRAY_error['user_login']) ;
		end_entete () ;
		submit_field ("&nbsp;", "validate", CREATE_MY_ACCOUNT) ;
	} elseif ($option == "view") {
		startform ("view", "index.php") ;
		lib_field (MY_EMAIL, $ARRAY_hote['user_email']) ;
		echo "<p>&nbsp;</p>" ;
		lib_field (MY_USERNAME, $ARRAY_hote['user_login']) ;
		echo "\t\t\t<p>&nbsp;</p>\r\n" ;
	}
	stopform () ;
}

function account_control ($ARRAY_account, $db) {
	// initialisation de toutes les erreurs  «  »
	$ERROR_account['user_email'] = $ERROR_account['user_login'] = "" ;
	// contrôle de l'email
	if (strlen ($ARRAY_account['user_email']) < 9) $ERROR_account['user_email'] .= NOK_EMAIL_LEN ;
	if (!filter_var($ARRAY_account['user_email'], FILTER_VALIDATE_EMAIL)) $ERROR_account['user_email'] .= NOK_EMAIL ;
	// contrôle d'existence du nom d'utilisateur
	if (is_object (search_row_by_string ("user", "user_login", $ARRAY_account['user_login'], $db))) $ERROR_account['user_login'] .= SORY.": <strong><span class=\"blue\">« ".$ARRAY_account['user_login'].ALRDY_USED.THX_OTHER_USERNAME ;
	// autres contrôles
	if(preg_match('/[^0-9A-Za-z_@:]/',$ARRAY_account['user_login'])) $ERROR_account['user_login'] .= NOK_CHAR ;
	if (strlen ($ARRAY_account['user_login']) < 4) $ERROR_account['user_login'] .= NOK_USER_LEN ;
	return $ERROR_account ;
}

function account_write ($ARRAY_data, $option, $db) {
	// Extraction des variables, avec échappement des apostrophes
	if (isset($ARRAY_data['user_email'])) $user_email = $db->real_escape_string($ARRAY_data['user_email']);
   else $user_email = "" ;
	if (isset($ARRAY_data['user_login'])) $user_login = $db->real_escape_string($ARRAY_data['user_login']);
   else $user_login = "" ;
	$feedback = $warning = "";
	if ($option == "new") {
		// génération du mot de passe envoyé au client
		$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz" ;
		$user_pass = rand_string($chars, 8) ;
		$user_pass_md5 = md5($user_pass) ;
		$user_rights = "member" ;
		$user_make_date = date("U") ;
		$user_ip = true_ip() ;
		$user_lang = LANG ;
		$creation_date = timestamp_to_datetime_fr ($user_make_date) ;
		$user_history = "##### Créé le : ".$creation_date." #####" ;
		// insertion dans la table user
		$user_insert = "INSERT INTO user (user_login, user_email, user_pass_md5, user_rights, user_make_date, user_ip, user_history, user_lang) VALUES ('$user_login', '$user_email', '$user_pass_md5', '$user_rights', '$user_make_date', '$user_ip', '$user_history', '$user_lang');" ;
		$db->query ($user_insert);
		// insertion dans la table settings avec paramètres prédéfinis
		$settings_insert = "INSERT INTO `settings` (`param_login`, `param_screen_width`, `param_screen_height`, `param_max_width`, `param_max_height`, `param_upload_form`) VALUES ('$user_login', 1280, 1024, 1200, 900, 'detail');" ;
		$db->query ($settings_insert);
		$new_user_login = html_convert($user_login) ;
		$feedback = OK_NEW_MEMBER1.$new_user_login.OK_NEW_MEMBER2 ;
		// envoi du mail contenant le mot de passe
		$subject = REGISTER_MAIL_SUBJECT ;
		$message = "<html><head><title>".REGISTER_MAIL_MESSAGE1."</title></head><body>\r\n" ;
		$message .= "<p>".REGISTER_MAIL_MESSAGE2.$user_login."</p>\r\n" ;
		$message .= "<p>".REGISTER_MAIL_MESSAGE3.$user_pass."</p>\r\n" ;
		$message .= "<p>".REGISTER_MAIL_MESSAGE4."</p>\r\n" ;
		$message .= "</body></html>\r\n" ;
//		echo "\t\t\t<p>".$user_pass."</p>\r\n" ;
		send_mail ($user_email, $subject, $message, $user_login) ;
	} else {
		// update
	}
	// retour des informations sur le déroulement de la création ou de la modification
	$retour = array ("0"=>"$feedback", "1"=>"$warning", "2"=>"$user_login") ;
	return $retour ;
}
?>