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

require_once ("localconf.php");
require_once ("control.php");
require_once ("html/template.php");
require_once ("class/db.class.php");

define ("OBJET", "objet");
define ("TABLEAU", "tableau");
define ("INSERE", "insere");
define ("MAJ", "maj");
define ("VOIR", "voir");

// supprimer l'échappement automatique
function supprime_echap_auto($tableau) {
	foreach ($tableau as $cle => $valeur) {
		if (!is_array($valeur)) $tableau[$cle] = stripslashes($valeur);
		else $tableau[$cle] = supprime_echap_auto($valeur);
	}
	return $tableau;
}
if (get_magic_quotes_gpc()) {
	$_POST = supprime_echap_auto($_POST);
	$_GET = supprime_echap_auto($_GET);
	$_REQUEST = supprime_echap_auto($_REQUEST);
	$_COOKIE = supprime_echap_auto($_COOKIE);
}

// connexion à la base de données et instanciation de l'objet $db
$db = new ceckdb (DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NOUN) ;
// charset de communication php <> mysql
$db->listen_talk("utf8") ;

// ouverture de session
session_start();
$OBJET_session = search_session (session_id(), $db);

if (is_object($OBJET_session)) {
	$login_user = $OBJET_session->session_login ;
	// acquisition de la langue de l'utilisateur
	$params = get_params ($login_user, $db) ;
	$lang_user = $params->user_lang ;
	if ($lang_user === "fr") require_once ("lang/fr.php");
	if ($lang_user === "en") require_once ("lang/en.php");
	// acquisition des parametres utilisateur
	$user_screen_width = $params->param_screen_width ;
	$user_screen_height = $params->param_screen_height ;
	define ("SCREEN_WIDTH", $user_screen_width);
	define ("SCREEN_HEIGHT", $user_screen_height);
} else {
	$langue = autoSelectLanguage(array('fr','en'), 'en') ;
	if ($langue === "fr") require_once ("lang/fr.php");
	else require_once ("lang/en.php");
}
// Recherche d'une session - retour OBJET -
function search_session ($id_session, $db) {
	$result = $db->query ("SELECT * FROM session WHERE session_id = '$id_session' ;") ;
	return $db->fetch_object($result) ;
}
// Recherche des paramêtres utilisateur
function get_params ($login_user, $db, $format=OBJET) {
	$result = $db->query ("SELECT * FROM settings, user WHERE settings.param_login = '$login_user' AND user.user_login = '$login_user'");
	if ($format == OBJET) return $db->fetch_object($result);
	else return $db->fetch_assoc($result) ;   
}
// Detection automatique de la langue du navigateur
// author Hugo Hamon (version 0.1)
function autoSelectLanguage($aLanguages, $sDefault) {
	if(!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
		$aBrowserLanguages = explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);    
		foreach($aBrowserLanguages as $sBrowserLanguage) {
			$sLang = strtolower(substr($sBrowserLanguage,0,2));      
			if(in_array($sLang, $aLanguages)) {
				return $sLang;
			}    
		}  
	}
	return $sDefault;
}
// Acquisition de la tache a effectuer
function scriptorun ($location, $action) {
	$to_do = "" ;
	if ($location === "admin") {
		switch ($action) {
			case "admins": $to_do = "admin/home.php" ; break ;
			case "logout": $to_do = "admin/logout.php" ; break ;
			case "register_init": $to_do = "admin/register_init.php" ; break ;
			case "register": $to_do = "admin/register.php" ; break ;
			case "set_rights": $to_do = "admin/account_rights.php" ; break ;
			case "my_account": $to_do = "admin/account_my.php" ; break ;
			case "manage_account": $to_do = "admin/account_manage.php" ; break ;
			case "who_is_online": $to_do = "admin/who_is_online.php" ; break ;
			case "password_change": $to_do = "admin/password.php" ; break ;
		}
	}
	if ($location === "media") {
		switch ($action) {
			case "none": $to_do = "media/home.php" ; break ;
			case "top_album": $to_do = "media/top_album.php" ; break ;
			case "top_media": $to_do = "media/top_media.php" ; break ;
			case "find": $to_do = "media/main_search.php" ; break ;
			case "help": $to_do = "media/help.php" ; break ;
			case "edit_request": $to_do = "media/request_edit.php" ; break ;
			case "move_request": $to_do = "media/request_move.php" ; break ;
			case "delete_request": $to_do = "media/request_delete.php" ; break ;
			case "download_request": $to_do = "media/request_download.php" ; break ;
			case "browse_album_form": $to_do = "media/album_browse_form.php" ; break ;
			case "browse_album": $to_do = "media/album_browse.php" ; break ;
//			case "manage_album": $to_do = "media/album_manage.php" ; break ;
			case "album_edit_request": $to_do = "media/album_edit_request.php" ; break ;
			case "album_delete_request": $to_do = "media/album_delete_request.php" ; break ;
			case "deep_search_form": $to_do = "media/deep_search_form.php" ; break ;
			case "deep_search": $to_do = "media/deep_search.php" ; break ;
			case "display_album": $to_do = "media/album_display.php" ; break ;
			case "add_media": $to_do = "media/media_add.php" ; break ;
			case "show_album": $to_do = "media/album_show.php" ; break ;
			case "slideshow": $to_do = "media/slideshow.php" ; break ;
			case "play_media": $to_do = "media/media_play.php" ; break ;
			case "poster": $to_do = "media/poster.php" ; break ;
			case "show_exif": $to_do = "media/exif_show.php" ; break ;
			case "toto": $to_do = "media/toto.php" ; break ;
			case "admin": $to_do = "admin/home.php" ; break ;
			case "logout": $to_do = "media/logout.php" ; break ;
			case "debug": $to_do = "debug/home.php" ; break ;
		}
	}
	if ($location === "settings") {
		switch ($action) {
			case "preference": $to_do = "settings/preference.php" ; break ;
//			case "my_account": $to_do = "settings/account_my.php" ; break ;
		}
	}
	if ($location === "debug") {
		switch ($action) {
			case "debugs": $to_do = "debug/home.php" ; break ;
			case "php_info": $to_do = "debug/php_info.php" ; break ;
			case "jpg_transfert": $to_do = "debug/transfert_jpg.php" ; break ;
			case "exif_transfert": $to_do = "debug/transfert_exif.php" ; break ;
			case "media_transfert": $to_do = "debug/transfert_media.php" ; break ;
		}
	}
	if ($to_do != "") return $to_do ;
	else return FALSE ;
}
// construction du menu lateral
function make_left_column ($location, $RIGHTS, $left_lib="tbd") {
	switch ($location) {
		case "admin":
			$menu[0] = "media|none|".LIBM_HOME;
			if ($RIGHTS == "administrator") {
				$menu[1] = "admin|manage_account|".LIBM_MANAGE_ACCOUNT;
				$menu[2] = "admin|who_is_online|".LIBM_WHOS_ONLINE;
				$menu[3] = "admin|my_account|".LIBM_MY_ACCOUNT;
			}
		break ;
		case "media":
			$menu[0] = "media|none|".LIBM_HOME;
			if ($RIGHTS != "visitor") $menu[1] = "media|add_media|".LIBM_NEW_MEDIA;
			$menu[2] = "media|browse_album_form|".LIBM_BROWSE_ALBUM;
			$menu[3] = "media|deep_search_form|".LIBM_DEEP_SEARCH;
			if ($RIGHTS == "visitor") $menu[4] = "admin|register_init|".LIBM_REGISTER;
			else $menu[5] = "settings|preference|".LIBM_SETTINGS;
			if ($RIGHTS == "administrator") $menu[6] = "admin|admins|".LIBM_ADMIN;
			elseif ($RIGHTS != "visitor") $menu[7] = "admin|my_account|".LIBM_MY_ACCOUNT;
		break ;
		case "debug":
			$menu[0] = "media|none|".LIBM_HOME;
		break ;
		case "log_in_progress":
			$menu[0] = "media|none|".LIBM_HOME;
		break ;
	}
	$nav = "\t\t\t<div id=\"left_menu\">\r\n\t\t\t\t<ul>\r\n" ;
	foreach ($menu as $clef => $valeur) {
		list($menu_location, $menu_action, $menu_item) = explode("|", $valeur);
		$nav .= "\t\t\t\t\t<li><a href=\"index.php?location=" . $menu_location . "&amp;action=" . $menu_action . "\">" . $menu_item . "</a></li>\r\n" ;
	}
	$nav .= "\t\t\t\t</ul>\r\n\t\t\t</div><!-- #left_menu -->\r\n" ;
	$nav .= "\t\t\t<div id=\"left_lib\">\r\n" ;
		$nav .= "\t\t\t\t<p>".$left_lib."</p>\r\n" ;
	$nav .= "\t\t\t</div><!-- #left_lib -->\r\n" ;
	$nav .= "\t\t</div><!-- #left_content -->\r\n" ;
	$nav .= "\t\t<div id=\"right_content\">\r\n" ;
	return $nav ;
}
// preparation avant affichage
function html_convert ($string) {
	return stripslashes(htmlspecialchars($string));
}

// #######################################################################
// #####							Fonctions de recherche							#####
// #######################################################################
function search_row_by_id ($table, $index, $id, $db, $format=OBJET) {
	$search = "SELECT * FROM $table WHERE $index = $id LIMIT 1" ;
	$find = $db->query($search) ;
	if ($format == OBJET) return $db->fetch_object($find);
	else return $db->fetch_assoc($find) ;
}
function get_field_by_id ($table, $field, $index, $id, $db) {
	$searched_row = search_row_by_id ($table, $index, $id, $db) ;
	if (is_object($searched_row)) {
		$searched_field = html_convert($searched_row->$field) ;
		return $searched_field ;
	} else return false ;
}
function search_row_by_string ($table, $criteria, $string, $db, $format=OBJET) {
	$sane_string = $db->real_escape_string($string) ; 
	$search = "SELECT * FROM $table WHERE $criteria = '$sane_string' LIMIT 1" ;
	$find = $db->query($search) ;
	if ($format == OBJET) return $db->fetch_object($find);
	else return $db->fetch_assoc($find) ;
}
// fonctionne avec des caractères sans accents
function get_field_by_string ($table, $field, $criteria, $string, $db) {
	$searched_row = search_row_by_string ($table, $criteria, $string, $db) ;
	if (is_object($searched_row)) {
		$searched_field = html_convert($searched_row->$field) ;
		return $searched_field ;
	} else return false ;
}
// #######################################################################
// #####					Fonctions de requêtes dans la bdd					#####
// #######################################################################
function generate_index($field, $db) {
	$requete = "UPDATE constants SET $field = $field + 1 "
				. "WHERE constant_id = 1" ;
	$db->query ($requete);
	$scan = "SELECT * FROM constants WHERE constant_id = 1 " ;
	$result = $db->query ($scan) ;  
	$cstt = $db->fetch_object($result) ;
	$index = $cstt->$field ;
	return $index ;
}
function increment_click ($id, $table, $val, $db) {
	if ($table == "album") {
		$index = "album_id" ;
		$field = "album_clicks" ;
	} elseif ($table == "media") {
		$index = "mid" ;
		$field = "clicks" ;
	}
	$incremente = "UPDATE $table SET $field = $field + $val WHERE $index = $id LIMIT 1";
	$db->query ($incremente);
}
function get_info ($hid, $db) {
	$result = $db->query ("SELECT * FROM `info` WHERE info_id = $hid") ;
	$data = $db->fetch_object($result) ;
	if (!is_object ($data)) return false ;
	if (LANG == "fr") {
		$label = $data->info_label_fr ;
		$help = $data->info_fr ;
	} else {
		$label = $data->info_label_en ;
		$help = $data->info_en ;
	}
	$retour = array ("0"=>"$label", "1"=>"$help") ;
	return $retour ;
}
// #######################################################################
// #####							Fonctions de formulaire							#####
// #######################################################################
function startform ($name, $action, $entete="none") {
	echo "\t\t\t<div class=\"form_container\">\r\n" ;
	echo "\t\t\t<form id=\"".$name."\" name=\"".$name."\" enctype=\"multipart/form-data\" action=\"".$action."\" method=\"post\">\r\n" ;
	if ($entete != "none") {
		echo "\t\t\t<fieldset>\r\n" ;
		echo "\t\t\t<legend>".$entete."</legend>\r\n" ;
	}
}
function hidden_field ($name, $value) {
	echo "\t\t\t\t<input type=\"hidden\" name=\"" . $name . "\" value=\"" . $value . "\" />\r\n" ;
}
function lib_field ($label, $value) {
			// à approfondir : décalage du premier label quand on affiche un message
	echo "\t\t\t\t<p>\r\n" ;
	echo "\t\t\t\t<label for=\"lib_field\">" . $label . "</label>\r\n" ;
	echo "\t\t\t\t<span class=\"lib_field\">&nbsp;&nbsp;" . $value . "</span>\r\n" ;
	echo "\t\t\t\t</p>\r\n" ;
//	echo "\t\t\t\t<p>&nbsp;</p>\r\n" ;			// à approfondir : décalage du premier label quand on affiche un message
}
function text_field ($label, $name,  $value, $taille, $error="", $read_only="") {
	if ($read_only == "read_only") $ro = " readonly=\"readonly\"" ;
	else $ro = "" ;
	echo "\t\t\t\t<p>\r\n" ;
	echo "\t\t\t\t\t<label for=\"".$name."\">".$label."</label>\r\n" ;
	if ($error != "") echo "\t\t\t\t\t<span class=\"error\">".$error."</span>\r\n" ;
	if ($taille == 1) {
		$maxlength = 16 ;
		$class = "small" ;
	} elseif ($taille == 2) {
		$maxlength = 32 ;
		$class = "medium" ;
	} else {
		$maxlength = 512 ;
		$class = "large" ;
	}
	echo "\t\t\t\t\t<input class=\"".$class."\" type=\"text\" name=\"".$name."\" title=\"".$label."\" value=\"".$value."\" maxlength=\"".$maxlength."\"".$ro." />\r\n" ;
	echo "\t\t\t\t</p>\r\n" ;
	if ($error != "") echo "\t\t\t\t".fill_it(500,12)."\r\n" ;
}
function password_field ($label, $name,  $value, $error) {
	echo "\t\t\t\t<p>\r\n" ;
	echo "\t\t\t\t<label for=\"".$name."\">".$label."</label>\r\n" ;
	if ($error != "") echo "\t\t\t\t<span class=\"error\">" . $error . "</span>\r\n" ;
	echo "\t\t\t\t<input class=\"medium\" type=\"password\" name=\"".$name."\" value=\"".$value."\" maxlength=\"16\" />\r\n" ;
	echo "\t\t\t\t</p>\r\n" ;
}
function textarea_field ($label, $name, $value,  $rows=2, $cols=48) {
	$txtarea = "\t\t\t\t<p>\r\n" ;
	$txtarea .= "\t\t\t\t<label for=\"".$name."\">".$label."</label>\r\n" ;
	$txtarea .= "\t\t\t\t<textarea style=\"margin: 10px 0 ;\" id=\"".$name."\" name=\"".$name."\" title=\"".$label."\" rows=\"".$rows."\" cols=\"".$cols."\">".$value."</textarea>\r\n" ;
	return $txtarea .= "\t\t\t\t</p>\r\n\t\t\t\t<p>&nbsp;</p>\r\n" ;
}
function select_field ($label, $name, $value, $ARRAY_liste, $error="", $taille=1) {
	$s = "" ;
	if ($label != "") {
		$etiquette = clean($label) ;
		$s .= "\t\t\t\t<p>\r\n" ;
		$s .= "\t\t\t\t<label for=\"".$etiquette."\">".$label."</label>\r\n" ;
	}
	if ($error != "") $s .= "\t\t\t\t<span class=\"error\">".$error."</span>\r\n" ;
	$s .= "\t\t\t\t<select name=\"".$name."\" title=\"".$label."\" size=\"".$taille."\">\r\n" ;
	$content = $key = "" ;
	while (list ($content, $key) = each ($ARRAY_liste)) {
		$content = htmlspecialchars($content);
		$value = htmlspecialchars($value);
		if ($content != $value) $s .= "\t\t\t\t\t<option value=\"".$content."\">".$key."</option>\r\n" ;
		else $s .= "\t\t\t\t\t<option value=\"".$content."\" selected=\"selected\">".$key."</option>\r\n" ;
	}
	$s .= "\t\t\t\t</select>\r\n" ;
	if ($taille > 1) $s .= "\t\t\t\t<p>&nbsp;<br />&nbsp;<br />&nbsp;<br /></p>\r\n" ;
	if ($label != "") return $s . "\t\t\t\t</p>\r\n";
	else return $s ;
}
function file_field ($label, $name, $value, $error) {
	$ff = "\t\t\t\t<p>\r\n" ;
	$ff .= "\t\t\t\t<label for=\"".$name."\">".$label."</label>\r\n" ;
	if ($error != "") $ff .= "\t\t\t\t<span class=\"error\">".$error."</span>\r\n" ;
	$ff .= "\t\t\t\t<input type=\"file\" name=\"".$name."\" title=\"".$label."\" value=\"".$value."\" size=\"40\" maxlength=\"512\" />" ;
	$ff .= "\r\n\t\t\t\t</p>\r\n" ;
//	if ($error != "") $ff .= "\t\t\t<p>&nbsp;</p>\r\n" ;
	if ($error != "") $ff .= "\t\t\t\t".fill_it(500,12)."\r\n" ;
	echo $ff ;
}
function _______________file_field ($lib, $name, $value, $error, $help=false) {
	$ff = "\t\t\t\t<p>\r\n" ;
	$ff .= "\t\t\t\t<label for=\"" . $name . "\">" . $lib . "</label>\r\n" ;
	if ($error != "") $ff .= "\t\t\t\t<span class=\"error\">" . $error . "</span>\r\n" ;
	$ff .= "\t\t\t\t<input type=\"file\" name=\"" . $name . "\" value=\"" . $value . "\" size=\"40\" maxlength=\"512\" />" ;
	if ($help == true) $ff .= "<img src=\"zyx/img/actions/action_help.png\" alt=\"help\" width=\"44\" height=\"24\" title=\"".HELP."\" onclick=\"get_help('index.php?location=home&amp;topic=get_help&amp;hid=1165')\" />" ;
	$ff .= "\r\n\t\t\t\t</p>\r\n" ;
	echo $ff ;
}
function radio_field ($label, $name, $value, $ARRAY_liste) {
	$result =  "\t\t\t\t<p>\r\n" ;
	$result .= "\t\t\t\t\t<label for=\"radio_field\">".$label."</label>\r\n" ;
	$nbval = 0 ;
	while (list ($content, $key) = each ($ARRAY_liste)) {
		if ($content == $value) $checked = " checked=\"checked\"" ;
		else $checked = "" ;
		$result .= "\t\t\t\t\t&nbsp; ".$key."<input type=\"radio\" name=\"".$name."\" value=\"".$content."\"".$checked." /> &nbsp; \r\n" ;
		$nbval++ ;
	}
	return $result .= "\t\t\t\t</p>\r\n" ;
}
function date_field ($wording, $ARRAY_preset, $error, $prefix="date_", $extended=true) {
	$df = "\t\t\t\t<p>\r\n" ;
	$label = clean ($wording) ;
	$df .= "\t\t\t\t<label for=\"".$label."\">".$wording."</label>\r\n" ;
	if (LANG == "fr") {				// DATE_FORMAT = "dmy"
		// 1 - jour de 01 a 31
		$df .= (select_field ("", $prefix."day", $ARRAY_preset[$prefix."day"], day_list(), $error)) ;
		// 2 - mois de janvier a décembre
		$df .= (select_field ("", $prefix."month", $ARRAY_preset[$prefix."month"], month_list())) ;
	} else {
		$df .= (select_field ("", $prefix."month", $ARRAY_preset[$prefix."month"], month_list(), $error)) ;
		$df .= (select_field ("", $prefix."day", $ARRAY_preset[$prefix."day"], day_list())) ;
	}
	if ($extended === true) {
		// 3 - annee#1 de 180 a 202
		$df .= (select_field ("", $prefix."yea", $ARRAY_preset[$prefix."yea"], yea_list())) ;
		// 4 - annee#2 de 1 a 9
		$df .= (select_field ("", $prefix."r", $ARRAY_preset[$prefix."r"], r_list())) ;
	// === false 3 - année de 2000 à 2030
	} else $df .= (select_field ("", $prefix."year", $ARRAY_preset[$prefix."year"], year_list())) ;
	$df .= "\t\t\t\t</p>\r\n" ;
	return $df ;
}
function _______________date_field ($wording, $ARRAY_preset, $error, $prefix="date_", $extended=true) {
	$df = "\t\t\t\t<p>\r\n" ;
	$label = clean ($wording) ;
	$df .= "\t\t\t\t<label for=\"".$label."\">".$wording."</label>\r\n" ;
	if (LANG == "fr") {				// DATE_FORMAT = "dmy"
		// 1 - jour de 01 a 31
		$df .= (select_field (0, "day", "", $prefix."day", day_list(), $ARRAY_preset[$prefix."day"], $error)) ;
		// 2 - mois de janvier a décembre
		$df .= (select_field (0, "month", "", $prefix."month", month_list(), $ARRAY_preset[$prefix."month"])) ;
	} else {
		$df .= (select_field (0, "month", "", $prefix."month", month_list(), $ARRAY_preset[$prefix."month"], $error)) ;
		$df .= (select_field (0, "day", "", $prefix."day", day_list(), $ARRAY_preset[$prefix."day"])) ;
	}
	if ($extended === true) {
		// 3 - annee#1 de 180 a 202
		$df .= (select_field (0, "yea", "", $prefix."yea", yea_list(), $ARRAY_preset[$prefix."yea"])) ;
		// 4 - annee#2 de 1 a 9
		$df .= (select_field (0, "r", "", $prefix."r", r_list(), $ARRAY_preset[$prefix."r"])) ;
	// === false 3 - année de 2000 à 2030
	} else $df .= (select_field (0, "year", "", $prefix."year", year_list(), $ARRAY_preset[$prefix."year"])) ;
	$df .= "\t\t\t\t</p>\r\n" ;
	return $df ;
}
function submit_field ($label, $name, $value) {
	echo "\t\t\t\t<p>\r\n" ;
//	echo "\t\t\t\t\t<label for=\"".$name."\">".$label."</label>\r\n" ;
	echo "\t\t\t\t\t<input class=\"soumet\" type=\"submit\" name=\"".$name."\" value=\"&nbsp;".$value."\" size=\"0\" maxlength=\"0\" />\r\n" ;
	echo "\t\t\t\t</p>\r\n\t\t\t\t<p>&nbsp;</p>\r\n" ;
}
function end_entete () {
	echo "\t\t\t</fieldset><h1>&nbsp;</h1>\r\n" ;
}
function stopform () {
	echo "\t\t\t</form><!--  #Formulaire de saisie -->\r\n" ;
	echo "\t\t\t</div><!--  #form_container -->\r\n" ;
}
// #######################################################################
// #####							Fonctions d'affichage							#####
// #######################################################################
function display_in_box ($feedback, $warning, $information="") {
	if ($feedback != "") {
		echo "\t\t\t<div class=\"feed_can\">\r\n" ;
		echo "\t\t\t\t<div class=\"feedback\">\r\n" ;
			if (!strstr($feedback, "table")) echo "\t\t\t\t\t<p>" . $feedback .  "</p>\r\n" ;
			else echo $feedback ;
		echo "\t\t\t\t</div>\r\n" ;
		echo "\t\t\t</div>\r\n" ;
	}
	if ($warning != "") {
		echo "\t\t\t<div class=\"warn_can\">\r\n" ;
		echo "\t\t\t\t<div class=\"warning\">\r\n" ;
			if (!strstr($warning, "table")) echo "\t\t\t\t\t<p>" . $warning .  "</p>\r\n" ;
			else echo $warning ;
		echo "\t\t\t\t</div>\r\n" ;
		echo "\t\t\t</div>\r\n" ;
	}
	if ($information != "") {
		echo "\t\t\t<div class=\"info_can\">\r\n" ;
		echo "\t\t\t\t<div class=\"information\">\r\n" ;
			if (!strstr($information, "table")) echo "\t\t\t\t\t<p>" . $information .  "</p>\r\n" ;
			else echo $information ;
		echo "\t\t\t\t</div>\r\n" ;
		echo "\t\t\t</div>\r\n" ;
	}
}
function fill_it ($width=44, $height=12) {
	$alt = "fill_it_".$width."x".$height ;
	return "<img src=\"img/clear.gif\" width=\"".$width."\" height=\"".$height."\" alt=\"".$alt."\" />" ;
}
function error_page ($error, $location, $RIGHTS, $footer_lib, $db ) {
	page_header ($error, 2, $db) ;
	echo make_left_column ($location, $RIGHTS, $error) ;
	// affichage erreur
	echo "\t\t\t<h1>&nbsp;</h1><h1>&nbsp;</h1>\r\n" ;
	echo "\t\t\t<h1><span class=\"rouge\">&nbsp;" . $error . "</span></h1>\r\n" ;
	page_footer($footer_lib) ;
	exit ;
}
function error_popup ($error) {
	require ("html/html_meta.inc");
?>
<title><?php echo $error ; ?></title>
<style type="text/css">
.entete {
font-family:Verdana, Arial, Helvetica, sans-serif;
font-size: 16px;
font-weight: bold;
line-height: 18px;
color: #ff0000;
padding-top: 80px;
padding-left: 40px;
}
</style>
</head>
<body>
<br />
<div class="entete"><?php echo $error ; ?></div>
</body>
</html>
<?php
	exit ;
}
// #######################################################################
// #####					Fonctions de chaînes et diverses						#####
// #######################################################################
function clean ($string){
	$a = ' ()ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞ
ßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
	$b = '___aaaaaaaceeeeiiiidnoooooouuuuy
bsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
	$string = utf8_decode($string);    
	$string = strtr($string, utf8_decode($a), $b);
	$string = strtolower($string);
	return utf8_encode($string);
}
function cut_string ($string, $size=40) {
	if (strlen ($string) > $size) $shortened = substr ($string, 0, ($size - 4)) . "..." ;
	else $shortened = $string ;
	return $shortened ;
}
function true_ip() {
 //recupere l adresse ip de l ordi de l utilisateur
 //http://php.developpez.com/sources/?page=securite#ipreelle
 if (isSet($_SERVER)) {
   if (isSet($_SERVER["HTTP_X_FORWARDED_FOR"])) $true_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
   elseif (isSet($_SERVER["HTTP_CLIENT_IP"])) $true_ip = $_SERVER["HTTP_CLIENT_IP"];
   else $true_ip = $_SERVER["REMOTE_ADDR"];
 } else {
   if (getenv( 'HTTP_X_FORWARDED_FOR' ) ) $true_ip = getenv( 'HTTP_X_FORWARDED_FOR' );
   elseif ( getenv( 'HTTP_CLIENT_IP' ) ) $true_ip = getenv( 'HTTP_CLIENT_IP' );
   else $true_ip = getenv( 'REMOTE_ADDR' );
 }
 return $true_ip;
}
function send_mail ($email, $subject, $message, $To, $Cc="", $Bcc="") {
	// Pour envoyer un mail HTML, l'en-tête Content-type doit être défini
	$headers  = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
	// En-têtes additionnels
	$headers .= "To: ".$To." <".$email.">\r\n";
	$headers .= "From: webmaster <mmms@ceck.org>" . "\r\n";
//	$headers .= "Cc: anniversaire_archive@example.com" . "\r\n";
//	$headers .= "Bcc: anniversaire_verif@example.com" . "\r\n";
	@mail ($email, $subject, $message, $headers);
}
function rand_string($chars, $len) {
	$string = '';
	for ($i = 0; $i < $len; $i++)	{
		$pos = rand(0, strlen($chars)-1);
		$string .= $chars{$pos};
	}
   return $string;
}
// #######################################################################
// #####				Fonctions de conversion et de formatage				#####
// #######################################################################
function datetime_to_timestamp ($date_time) {
   list($date, $time) = explode(" ", $date_time) ;
   list($year, $month, $day) = explode("-", $date) ;
   list($hour, $minute, $second) = explode(":", $time) ;
   $timestamp = mktime($hour, $minute, $second, $month, $day, $year) ;
   return $timestamp ;
}
function timestamp_to_datetime ($time_stamp) {
   return date("Y-m-d H:i:s", $time_stamp);
}
function timestamp_to_date_fr ($time_stamp) {
   return date("d M Y", $time_stamp);
}
function timestamp_to_datetime_fr ($time_stamp) {
   return date("d M Y - H:i:s", $time_stamp);
}

// #######################################################################
// #####								Listes diverses								#####
// #######################################################################
function day_list () {
	$liste_days = array ("01"=>"01 ","02"=>"02 ","03"=>"03 ","04"=>"04 ","05"=>"05 ","06"=>"06 ","07"=>"07 ","08"=>"08 ","09"=>"09 ","10"=>"10 ","11"=>"11 ","12"=>"12 ","13"=>"13 ","14"=>"14 ","15"=>"15 ","16"=>"16 ","17"=>"17 ","18"=>"18 ","19"=>"19 ","20"=>"20 ","21"=>"21 ","22"=>"22 ","23"=>"23 ","24"=>"24 ","25"=>"25 ","26"=>"26 ","27"=>"27 ","28"=>"28 ","29"=>"29 ","30"=>"30 ","31"=>"31 ") ;
	return $liste_days ;
}
function month_list () {
	if (LANG=="fr") $liste_months = array ("01"=>"Janvier ","02"=>"Février ","03"=>"Mars ","04"=>"Avril ","05"=>"Mai ","06"=>"Juin ","07"=>"Juillet ","08"=>"Août ","09"=>"Septembre ","10"=>"Octobre ","11"=>"Novembre ","12"=>"Décembre ") ;
	if (LANG=="en") $liste_months = array ("01"=>"January ","02"=>"February ","03"=>"March ","04"=>"April ","05"=>"May ","06"=>"June ","07"=>"July ","08"=>"August ","09"=>"September ","10"=>"October ","11"=>"November ","12"=>"December ") ;
	return $liste_months ;
}
function yea_list ($debut=180) {
	if ($debut==180) $liste_yea = array ("180"=>"180 ","181"=>"181 ","182"=>"182 ","183"=>"183 ","184"=>"184 ","185"=>"185 ","186"=>"186 ","187"=>"187 ","188"=>"188 ","189"=>"189 ","190"=>"190 ","191"=>"191 ","192"=>"192 ","193"=>"193 ","194"=>"194 ","195"=>"195 ","196"=>"196 ","197"=>"197 ","198"=>"198 ","199"=>"199 ","200"=>"200 ","201"=>"201 ","202"=>"202 ","203"=>"203 ") ;
	if ($debut==197) $liste_yea = array ("197"=>"197 ","198"=>"198 ","199"=>"199 ","200"=>"200 ","201"=>"201 ","202"=>"202 ","203"=>"203 ") ;
	return $liste_yea ;
}
function r_list () {
	$liste_r = array ("0"=>" 0 ", "1"=>" 1 ", "2"=>" 2 ", "3"=>" 3 ", "4"=>" 4 ", "5"=>" 5 ", "6"=>" 6 ", "7"=>" 7 ", "8"=>" 8 ", "9"=>" 9 ") ;
	return $liste_r ;
}
function year_list () {
	$liste_year = array ("2000"=>" 2000 ","2001"=>" 2001 ","2002"=>" 2002 ","2003"=>" 2003 ","2004"=>" 2004 ","2005"=>" 2005 ","2006"=>" 2006 ","2007"=>" 2007 ","2008"=>" 2008 ","2009"=>" 2009 ","2010"=>" 2010 ","2011"=>" 2011 ","2012"=>" 2012 ","2013"=>" 2013 ","2014"=>" 2014 ","2015"=>" 2015 ","2016"=>" 2016 ","2017"=>" 2017 ","2018"=>" 2018 ","2019"=>" 2019 ","2020"=>" 2020 ","2021"=>" 2021 ","2022"=>" 2022 ","2023"=>" 2023 ","2024"=>" 2024 ","2025"=>" 2025 ","2026"=>" 2026 ","2027"=>" 2027 ","2028"=>" 2028 ","2029"=>" 2029 ","2030"=>" 2030 ") ;
	return $liste_year ;
}
?>