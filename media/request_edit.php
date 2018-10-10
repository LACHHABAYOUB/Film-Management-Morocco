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

require_once("media/abc.php") ;
// acquisition mid
if (isset ($_GET['mid'])) $mid = $_GET['mid'] ;
else error_popup (ERR_UNDEFINED) ;
// recherche du media correspondant
$ARRAY_media = search_row_by_id ("media", "mid", $mid, $db, TABLEAU);
if (is_array($ARRAY_media)) foreach ($ARRAY_media as $cle => $valeur) $ARRAY_media[$cle] = html_convert($valeur);
// si media inexistant on arrete la
else error_popup (ERR_NO_OBJECT) ;

// autorisation de poursuivre
// ...................................

$bandeau = EDIT_MEDIA." : ".$ARRAY_media['original_name'] ;
$page_head = EDIT_MEDIA." : <span class=\"jaune\">".$ARRAY_media['original_name']."</span>" ;

// Debut de page en html
popup_header ($bandeau, "look_popup") ;

echo "<div id=\"page_head\">\r\n" ;
echo "\t<h3>" . $page_head . "</h3>\r\n" ;
echo "</div>\r\n" ;

if (!isset($_GET['status'])) {
	// affiche le formulaire pour la saisie
	media_edit_form ($ARRAY_media, "one", $db) ;
} elseif ($_GET['status'] == "submit") {
	if (isset ($_POST['edit_media_step'])) {
		if ($_POST['edit_media_step'] == "in_progress") {
			$feedback = $warning = $keywords = "" ;
			// préparation et écriture des données reçues dans le _POST
			if (isset($_POST['title'])) $title = $db->real_escape_string($_POST['title']);
			else $title = UNKNOWN ;
			if (isset($_POST['author'])) $author = $db->real_escape_string($_POST['author']);
			else $author = UNDEFINED ;
			if (isset($_POST['comment'])) $comment = $db->real_escape_string($_POST['comment']);
			else $comment = "" ;
			$event_date = $_POST['date_yea'].$_POST['date_r']."-".$_POST['date_month']."-".$_POST['date_day'] ;
//			if ($url != "") $keywords .= récupérer original_name
			if ($title != UNKNOWN) $keywords .= $title." " ;
			if ($author != UNDEFINED) $keywords .= $author." " ;
			$revised_by = $USER ;
			$revised_on = date("U") ;
			$sql_update = "UPDATE `media` SET `keywords` = '$keywords', `title` = '$title', `author` = '$author', `comment` = '$comment', `event_date` = '$event_date' WHERE `media`.`mid` = '$mid' LIMIT 1 ;" ;
			$db->query ($sql_update);
			if (mysql_affected_rows() == 1) {
				$sql_revised = "UPDATE `media` SET `clicks` = `clicks` + 3, `revised_on` = '$revised_on', `revised_by` = '$revised_by' WHERE `media`.`mid` = '$mid' LIMIT 1 ;" ;
				$db->query ($sql_revised);
				$feedback = OK_UPDATE ;
			} else $warning = NO_UPDATE_DONE ;
			// Affichage du message de feedback
			display_in_box ($feedback, $warning) ;
		}
	} else popup_footer () ;
	// affiche le formulaire pour relecture
	$ARRAY_media = search_row_by_id ("media", "mid", $mid, $db, TABLEAU);
	if (is_array($ARRAY_media)) foreach ($ARRAY_media as $cle => $valeur) $ARRAY_media[$cle] = html_convert($valeur);
	// Si media non trouvé on arrete là
	else error_popup (ERR_NO_OBJECT) ;
	media_edit_form ($ARRAY_media, "two", $db) ;
}

// Fin de page html
popup_footer () ;

function media_edit_form ($ARRAY_media, $step, $db) {
	$mid = $ARRAY_media['mid'] ;
	startform ("new_media", "index.php?location=media&amp;action=edit_request&amp;status=submit&amp;mid=".$mid) ;
	hidden_field ("edit_media_step", "in_progress") ;
	hidden_field ("mid", $mid) ;
//		$album_name = get_the_album_by_id ($ARRAY_media['albumid'], $db) ;
	$album_name = get_field_by_id ("album", "album_title", "album_id", $ARRAY_media['albumid'], $db) ;
	lib_field (ALBUM, $album_name) ;
//		echo "\t\t\t<p>&nbsp;</p>" ;
//		text_field ("large", "title", LAB_MEDIA_TITLE, "title", $ARRAY_media['title'], "", 64) ;
	text_field (LAB_MEDIA_TITLE, "title",  $ARRAY_media['title'], 3) ;
//		text_field ("medium", "author", LAB_MEDIA_AUTHOR, "author", $ARRAY_media['author'], "", 32) ;
	text_field (LAB_MEDIA_AUTHOR, "author",  $ARRAY_media['author'], 2) ;
	list ($date_year, $date_month, $date_day) = explode ("-", $ARRAY_media['event_date']) ;
	$date_yea = substr ($date_year, 0, 3) ;
	$date_r = substr ($date_year, 3, 1) ;
	$DATE_media = array("date_yea"=>"$date_yea","date_r"=>"$date_r","date_month"=>"$date_month","date_day"=>"$date_day") ;
	echo date_field ("event_date", $DATE_media, "") ;						// tbd : implanter un controle de date
//		echo textarea_field ($ARRAY_media['comment'], LAB_COMMENT, 2, 48) ;
	echo textarea_field (LAB_COMMENT, "comment", $ARRAY_media['comment']) ;
//	lib_field ("album : ", $album) ;
	if ($step == "one") submit_field ("&nbsp;", "validate", CONFIRM_UPDATE) ;
	stopform () ;
	echo "<div id=\"information\">\r\n" ;
	$media_size = number_format($ARRAY_media['media_size']/1000, 3, ',', ' ') ;
	echo "\t\t\t<p>".SIZE_Y.$media_size.KB_Y."</p>\r\n" ;
	echo "\t\t\t<p>".CLICKS_Y.$ARRAY_media['clicks']."</span>".fill_it(183,6).DOWNLOADS_Y.$ARRAY_media['downloads']."</p>\r\n" ;
	if ($ARRAY_media['revised_by'] != "") {
		$updated_on = timestamp_to_date_fr ($ARRAY_media['revised_on']) ;
		echo "\t\t\t<p>".UPDATED_ON_Y.$updated_on.BY_Y.$ARRAY_media['revised_by']."</span></p>\r\n" ;
	}
//	echo "\t\t\t<p>".KEYWORDS." : <span class=\"jaune\">".$ARRAY_media['keywords']."</span></p>\r\n" ;
	$maked_on = timestamp_to_date_fr ($ARRAY_media['maked_on']) ;
	echo "\t\t\t<p>".UPLOADED_ON_Y.$maked_on.BY_Y.$ARRAY_media['maked_by']."</span></p>\r\n" ;
	echo "</div>\r\n" ;
	
}
?>






















