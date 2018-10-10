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
if ($RIGHTS != "administrator") error_popup (ERR_ACCESS) ;

// Acquisition login a modifier
if (isset ($_GET['login'])) $login = $exif_id = $_GET['login'] ;
else error_popup (ERR_UNDEFINED) ;
// Recherche enregistrement a modifier
$ARRAY_user = search_user ($login, $db, TABLEAU);
if (is_array($ARRAY_user)) {
	$user_login = $ARRAY_user['user_login'] ;
	$user_rights = $ARRAY_user['user_rights'] ;
// Si enregistrement inexistant on arrete la
} else error_popup (ERR_NO_ARRAY_user) ;

// Debut de page en html
$bandeau = RIGHTS_SETTING ;
popup_header ($bandeau, "look_popup") ;
$page_head = RIGHTS_SETTING ;
echo "<div id=\"page_head\">\r\n" ;
echo "\t<h3>" . $page_head . "</h3>\r\n" ;
echo "</div>\r\n" ;

// Appel du formulaire
startform ("new_media", "index.php?location=admin&amp;action=set_rights&amp;status=submit") ;
hidden_field ("user_login", $user_login) ;
hidden_field ("user_rights", $user_rights) ;
// Liste des droits
$rights_list = array ("administrator"=>RIGHTS_ADMIN, "private"=>RIGHTS_PRIVATE, "member"=>RIGHTS_MEMBER) ;
echo radio_field (RIGHTS_SETTING, "rights_setting", $user_rights, $rights_list) ;
// Bouton d'envoi
submit_field ("&nbsp;", "validate", CONFIRM_SEND) ;
stopform () ;

// Fin de page html
popup_footer () ;
?>