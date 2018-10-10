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

define ("MAX_SESSION_TIME" ,28800) ;

function access_control ($db, $id_session, $POST_log_entry) {
	$nok = "" ;
	$OBJET_session = search_session ($id_session, $db);
	// Session existante : vérification de la validité
	if (is_object($OBJET_session)) {
		if (is_valid_session ($OBJET_session, $db)) {
			$ARRAY_who_is = get_login_info ($OBJET_session, $db) ;
			return $ARRAY_who_is ;
		}
		else $nok = OBSOLETE_SESSION ;
	}
	// Aucune session : vérification de l'option de connexion
	if (isset ($POST_log_entry['login_option'])) {
		if ($POST_log_entry['login_option'] == "registered") {
			// vérification du couple identifiant - mot de passe ($POST_log_entry) si existant
			if (isset($POST_log_entry['entered_name'])) {
				// tentative de création d'une session
				$nok = create_session ($db, $POST_log_entry['entered_name'], $POST_log_entry['entered_password'], "registered", $id_session) ;
				if ($nok === "") { 
					$OBJET_session = search_session ($id_session, $db);
					$ARRAY_who_is = get_login_info ($OBJET_session, $db) ;
					return $ARRAY_who_is ;
				}
				else $nok .= FAIL_IDENT ;
			}
		// cas du visiteur anonyme
		} elseif ($POST_log_entry['login_option'] == "un_registered") {
			$entered_name = ANONYMOUS ;
			$entered_password = "0123456789" ;
			create_session ($db, $entered_name, $entered_password, "un_registered_visitor", $id_session) ;
			$OBJET_session = search_session ($id_session, $db);
			$ARRAY_who_is = get_login_info ($OBJET_session, $db) ;
			return $ARRAY_who_is ;
		}
	}
	// échec : renvoi formulaire avec entered_name par défaut + message erreur si existant
	if (isset($POST_log_entry['entered_name'])) $entered_name = $POST_log_entry['entered_name'] ;
	else $entered_name = "" ;
	// voir à quelle fenêtre on a affaire
	$current_script = $_SERVER['REQUEST_URI'] ;
	if (strstr($current_script, 'logout') or strstr($current_script, 'request') or strstr($current_script, 'exif')) {
		popup_header (LOGOUT, "look_popup") ;
		display_in_box ("", $nok) ;
		recognition_form ($current_script, $entered_name, $nok);
		popup_footer () ;
	} else {
		page_header ("", 2, $db) ;
		echo make_left_column ("log_in_progress", "", IDENT_IN_PROGRESS) ;
		display_in_box ("", $nok) ;
		recognition_form ($current_script, $entered_name, $nok);
		page_footer (IDENT_IN_PROGRESS) ;
	}
}
// Affichage formulaire avec url du script courant et nom entré (si défini)
function recognition_form ($current_script, $entered_name, $nok) {
	startform ("login", $current_script, "Formulaire d'identification") ;
	$LOGIN_option["registered"] = REGISTERED_YES ;
	$LOGIN_option["un_registered"] = REGISTERED_NO ;
	echo select_field (LOGIN_OPTION, "login_option", REGISTERED_YES, $LOGIN_option) ;
	text_field (USERNAME, "entered_name", $entered_name, 2) ;
	password_field (PASSWORD, "entered_password", "", "") ;
	end_entete() ;
	submit_field ("&nbsp;", "validate", SEND) ;
	stopform () ;
	// message d'acceuil
	display_in_box ("", "", INFO_ANONYMOUS) ;
}
// recherche des attributs de l'hôte qui a ouvert une session
function get_login_info ($OBJET_session, $db) { 
	// initialisation
	$ARRAY_who_is = array() ;
	// calcul du temps de session restant
	$just_now = date ("U");
	$delta = $OBJET_session->session_time_limit - $just_now ;
	$hour = floor ($delta / 3600) ;
	$remaining = $delta % 3600 ;
	$minute = floor ($remaining / 60) ;
	if ($minute < 10) $minute = "0" . $minute ;
	$seconde = $remaining % 60 ;
	if ($seconde < 10) $seconde = "0" . $seconde ;
	if ($delta > 1800) $time_remaining = TIME_REMAINING . "$hour H $minute ' $seconde \"" ;
	else $time_remaining = "<span class=\"jaune\"><b>" . TIME_REMAINING . "$hour H $minute ' $seconde \"</b></span>" ;
	// comptage des sessions valides en cours
	$limit_timestamp = date ("U") - MAX_SESSION_TIME ;
	$result = $db->query ("SELECT * FROM session WHERE session_time_limit > '$limit_timestamp' ;") ;
	$v = $m = $p = $a = $somme = 0 ;
	while ($row = $db->fetch_object($result)) {
		if ($row->session_type == "visitor") $v++  ;
		if ($row->session_type == "member") $m++  ;
		if ($row->session_type == "private") $p++  ;
		if ($row->session_type == "administrator") $a++  ;
		$somme++ ;
	}
	$left_lib = CONNECTED.$v.VISITOR."<br />".$m.MEMBER."<br />".$p.PRIVAT."<br />".$a.ADMINISTRATOR ;
	$left_lib = CONNECTED ;
	if ($v > 0) $left_lib .= $v.VISITOR."<br />" ;
	if ($m > 0) $left_lib .= $m.MEMBER."<br />" ;
	if ($p > 0) $left_lib .= $p.PRIVAT."<br />" ;
	if ($a > 0) $left_lib .= $a.ADMINISTRATOR ;
	// acquisition des droits et limitation des requêtes en conséquence
	$session_type = $OBJET_session->session_type ;
	if (($session_type === "administrator") or ($session_type === "private")) $request_limit = "album.album_visibility LIKE '%%%'" ;
	elseif ($session_type === "member") $request_limit = "(album.album_visibility LIKE '%everyone%' OR  album.album_visibility LIKE '%public%')" ;
	elseif ($session_type === "visitor") $request_limit = "album.album_visibility LIKE '%everyone%'" ;
	// renvoi des infos sous forme de tableau
	$ARRAY_who_is['session_login'] = $OBJET_session->session_login ;
	$ARRAY_who_is['session_email'] = $OBJET_session->session_email ;
	$ARRAY_who_is['session_type'] = $session_type ;
	$ARRAY_who_is['session_ip_adress'] = $OBJET_session->session_ip_adress ;
	$ARRAY_who_is['time_remaining'] = $time_remaining ;
	$ARRAY_who_is['request_limit'] = $request_limit ;
	$ARRAY_who_is['valid_session_qty'] = $somme ;
	$ARRAY_who_is['header_lib'] = "" ;
	$ARRAY_who_is['left_lib'] = $left_lib ;
	$ARRAY_who_is['footer_lib'] = $time_remaining ;
	return $ARRAY_who_is ;
}
// Tentative de création d'une session
function create_session ($db, $entered_name, $entered_password, $entered_option, $id_session) {
	$nok = "" ;
	$just_now = date ("U");
	$time_limit = $just_now + MAX_SESSION_TIME ;
	$ip_user = true_ip() ;
	if ($entered_option == "registered") {
		$GUEST = search_user ($entered_name, $db);
		// l'hôte est-il enregistré ?
		if (is_object($GUEST)) {
			// verification du mot de passe
			if ($GUEST->user_pass_md5 == md5($entered_password)) {
				$entered_name = $db->real_escape_string($entered_name);
				// acquisition des droits (visitor		member		private		administrator)
				$rights = "" ;
				if ($GUEST->user_rights == "administrator") $rights = "administrator" ;
				if ($GUEST->user_rights == "private") $rights = "private" ;
				if ($GUEST->user_rights == "member") $rights = "member" ;
				$email = $GUEST->user_email ;
				// création et enregistrement de la session
				$make_session = "INSERT INTO session (session_id, session_login, session_email, session_time_limit, session_type, session_ip_adress, session_date) VALUES ('$id_session', '$entered_name', '$email', '$time_limit', '$rights', '$ip_user', NOW());";
				$db->query ($make_session);
				// incrementation du compteur user_log
				$increment_log = "UPDATE user SET user_logs = user_logs + 1 WHERE user_login = '$entered_name' LIMIT 1";
				$db->query ($increment_log);
				return $nok ;
			} else {
				$nok .= NOK_PASSWORD ;
				return $nok ;
			}
		} else {
			$nok .= NOK_USERNAME ;
			return $nok ;
		}
	} else {
		// cas du visiteur anonyme
		// création et enregistrement de la session anonyme
		$make_session = "INSERT INTO session (session_id, session_login, session_email, session_time_limit, session_type, session_ip_adress, session_date) VALUES ('$id_session', 'anonymous', 'no_email', '$time_limit', 'visitor', '$ip_user', NOW());";
		$db->query ($make_session);
		return $nok ;
	}
}
// vérification de la validité d'une session
function is_valid_session ($OBJET_session, $db) {
	// vérification du temps limite
	$just_now = date ("U");
	if (($OBJET_session->session_time_limit < $just_now)) {
		// Destruction de la session locale
		session_destroy();
		// Suppression de la session distante
		$request  = "DELETE FROM session WHERE session_id='$OBJET_session->session_id'";
		$result = $db->query ($request);
		return FALSE;
	} else return TRUE;
}
// Recherche d'un visiteur par son login
function search_user ($log_in_name, $db, $format=OBJET) {
	$log_in_sane = $db->real_escape_string($log_in_name) ;
	$result = $db->query ("SELECT * FROM user WHERE user_login = '$log_in_sane'");
	if ($format == OBJET) return $db->fetch_object($result);
	else return $db->fetch_assoc($result) ;   
}
?>