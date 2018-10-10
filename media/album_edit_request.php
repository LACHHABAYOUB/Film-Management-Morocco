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
****************************************************************  «  »
*/

require_once("media/abc.php") ;
// Acquisition albumid
if (isset ($_GET['albumid'])) $albumid = $_GET['albumid'] ;
else error_popup (ERR_UNDEFINED) ;
// Recherche album correspondant
$ARRAY_album = search_row_by_id ("album", "album_id", $albumid, $db, TABLEAU) ;
if (is_array($ARRAY_album)) foreach ($ARRAY_album as $cle => $valeur) $ARRAY_album[$cle] = html_convert($valeur);
// Si album inexistante on arrete la
else error_popup (ERR_NO_OBJECT) ;

// autorisation de poursuivre
if (($RIGHTS != "administrator") and ($RIGHTS != "private")) error_popup (ERR_ACCESS) ;

$bandeau = EDIT_ALBUM." : ".$ARRAY_album['album_title'] ;
$page_head = EDIT_ALBUM." : <span class=\"jaune\">".$ARRAY_album['album_title']."</span>" ;

// Debut de page en html
popup_header ($bandeau, "look_popup") ;

echo "<div id=\"page_head\">\r\n" ;
echo "\t<h3>" . $page_head . "</h3>\r\n" ;
echo "</div>\r\n" ;

if (!isset($_GET['status'])) {
	// affiche le formulaire pour la saisie
	album_edit_form ($ARRAY_album, "one", $db) ;
} elseif ($_GET['status'] == "submit") {
	if (isset ($_POST['edit_album_control'])) {
		if ($_POST['edit_album_control'] == "in_progress") {
			$feedback = $warning = $keywords = "" ;
			// préparation et écriture des données reçues dans le _POST
			if (isset($_POST['album_title'])) $album_title = $db->real_escape_string($_POST['album_title']);
			else $album_title = UNKNOWN ;
			if (isset($_POST['album_author'])) $album_author = $db->real_escape_string($_POST['album_author']);
			else $album_author = UNDEFINED ;
			if (isset($_POST['comment'])) $comment = $db->real_escape_string($_POST['comment']);
			else $comment = "" ;
			$album_visibility = $_POST['album_visibility'] ;
			if ($album_title != UNKNOWN) $keywords .= $album_title." " ;
			if ($album_author != UNDEFINED) $keywords .= $album_author." " ;
			$album_revised_by = $USER ;
			$album_revised_on = date("U") ;
			$sql_update = "UPDATE `album` SET `album_keywords` = 'album_$keywords', `album_title` = '$album_title', `album_author` = '$album_author', `album_comment` = '$comment', `album_visibility` = '$album_visibility' WHERE `album`.`album_id` = '$albumid' LIMIT 1 ;" ;
			$db->query ($sql_update);
			if (mysql_affected_rows() == 1) {
				$sql_revised = "UPDATE `album` SET `album_revised_on` = '$album_revised_on', `album_revised_by` = '$album_revised_by' WHERE `album`.`album_id` = '$albumid' LIMIT 1 ;" ;
				$db->query ($sql_revised);
				$feedback = OK_UPDATE ;
			} else $warning = NO_UPDATE_DONE ;
			// Affichage du message de feedback
			display_in_box ($feedback, $warning) ;
		}
	} else popup_footer () ;
	// affiche le formulaire pour relecture
	$ARRAY_album = search_row_by_id ("album", "album_id", $albumid, $db, TABLEAU) ;
	if (is_array($ARRAY_album)) foreach ($ARRAY_album as $cle => $valeur) $ARRAY_album[$cle] = html_convert($valeur);
	// Si album non trouvé on arrête là
	else error_popup (ERR_NO_RECORD) ;
	album_edit_form ($ARRAY_album, "two", $db) ;
}

// Fin de page html
popup_footer () ;

function album_edit_form ($ARRAY_album, $step, $db) {
	$albumid = $ARRAY_album['album_id'] ;
	startform ("edit_album", "index.php?location=media&amp;action=album_edit_request&amp;status=submit&amp;albumid=".$albumid) ;
	hidden_field ("edit_album_control", "in_progress") ;
	hidden_field ("albumid", $albumid) ;
	hidden_field ("album_visibility", $ARRAY_album['album_visibility']) ;
	echo "\t\t\t<p>&nbsp;</p>" ;
	text_field (LAB_ALBUM_TITLE, "album_title",  $ARRAY_album['album_title'], 3) ;
	text_field (LAB_ALBUM_AUTHOR, "album_author",  $ARRAY_album['album_author'], 2) ;
	echo textarea_field (LAB_COMMENT, "comment", $ARRAY_album['album_comment']) ;
	// liste des options de visibilité
	$option_list = array ("everyone"=>"<span class=\"jaune\">".EVERYONE."</span>", "public"=>MEMBER00, "private"=>MEMBER01) ;
	echo radio_field (ACCESS_CONTROL, "album_visibility", $ARRAY_album['album_visibility'], $option_list) ;
	if ($step == "one") submit_field ("&nbsp;", "validate", CONFIRM_UPDATE) ;
	stopform () ;
	echo "<div id=\"information\">\r\n" ;
	if ($ARRAY_album['album_visibility'] == "everyone") $info_visibility = EVERYONE ;
	elseif ($ARRAY_album['album_visibility'] == "public") $info_visibility = MEMBER1 ;
	elseif ($ARRAY_album['album_visibility'] == "private") $info_visibility = MEMBER2 ;
	echo "\t\t\t<p>".THIS_ALBUM_IS_Y.$info_visibility.".</span></strong></p>\r\n" ;
	echo "\t\t\t<p>".CLICKS_Y.$ARRAY_album['album_clicks']."</span></p>\r\n" ;
	if ($ARRAY_album['album_revised_by'] != "") {
		$updated_on = timestamp_to_date_fr ($ARRAY_album['album_revised_on']) ;
		echo "\t\t\t<p>".UPDATED_ON_Y.$updated_on.BY_Y.$ARRAY_album['album_revised_by']."</span></p>\r\n" ;
	}
	$maked_on = timestamp_to_date_fr ($ARRAY_album['album_maked_on']) ;
	echo "\t\t\t<p>".CREATED_ON_Y.$maked_on.BY_Y.$ARRAY_album['album_maked_by']."</span></p>\r\n" ;
	echo "</div>\r\n" ;
}
?>