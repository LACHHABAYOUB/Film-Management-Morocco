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
if (($RIGHTS != "administrator") and ($RIGHTS != "private")) error_popup (ERR_ACCESS) ;

$bandeau = DELETE_MEDIA." : ".$ARRAY_media['original_name'] ;
$page_head = DELETE_MEDIA." : <span class=\"jaune\">".$ARRAY_media['original_name']."</span>" ;

// Debut de page en html
popup_header ($bandeau, "look_popup") ;

echo "<div id=\"page_head\">\r\n" ;
echo "\t<h3>" . $page_head . "</h3>\r\n" ;
echo "\t<h1>&nbsp;</h1>\r\n" ;
echo "</div>\r\n" ;

if (!isset($_GET['status'])) {
	// affichage du message d'avertissement et du formulaire de validation
	$warning = "Attention !<br />Vous êtes sur le point de supprimer le média :<br />".$ARRAY_media['original_name']."<br />Cette action est irréversible !" ;
	display_in_box ("", $warning) ;
	media_delete_form ($ARRAY_media, $db) ;
} elseif ($_GET['status'] == "submit") {
	$feedback = $warning = "" ;
	$mid = $_POST['mid'] ;
	$itemid = $_POST['itemid'] ;
	$album_id = $_POST['albumid'] ;
	$media_name = $_POST['media_name'] ;
	// construction des chemins vers les fichiers à supprimer
	$directory = UPLDP.generate_album_dir_name ($album_id) ;
	$media_to_delete = $directory."/".$media_name ;
	$thumb_to_delete = $directory."/thumbnail/min".$media_name ;
	// suppression du média et de la miniature associée (le cas échéant)
	$warn1 = $warn2 = "" ;
	if (!unlink ($media_to_delete)) $warn1 = "no good" ;
	if (($itemid > 1) and ($itemid < 5)) {
		if (!unlink ($thumb_to_delete)) $warn2 = "no good" ;
	}
	// mise à jour de la ligne concernée dans la table media (si les fichiers ont bien été supprimés)
	if (($warn1 == "") and ($warn2 == "")) {
		$delete_sql = "DELETE FROM `media` WHERE `media`.`mid` = '$mid' LIMIT 1 ;" ;
		$db->query ($delete_sql);
		$album_title = get_field_by_id ("album", "album_title", "album_id", $album_id, $db) ;
		if (mysql_affected_rows() == 1) $feedback = "Le media <span class=\"jaune\"> ".$_POST['original_name']." </span><br />a bien été supprimé<br />de l'album : <i>".$album_title."</i>" ;
	} else $warning = $warn1."<br />".$warn2 ;
	
	// Affichage du message de feedback
	display_in_box ($feedback, $warning) ;
	
	
	
/*	echo "<div id=\"page_head\"><h3>media_name : ".$media_name."</h3>\r\n" ;
	echo "\t\t\t<h3>directory : ".$directory."</h3>\r\n" ;
	echo "\t\t\t<h3>media : ".$media_to_delete."</h3>\r\n" ;
	echo "\t\t\t<h3>thumb : ".$thumb_to_delete."</h3>\r\n" ;
	echo "\t\t\t<h3>delete_sql_sql : ".$delete_sql."</h3>\r\n</div>" ;
*/
}

popup_footer () ;

function media_delete_form ($ARRAY_media, $db) {
	$mid = $ARRAY_media['mid'] ;
	startform ("delete_media", "index.php?location=media&amp;action=delete_request&amp;status=submit&amp;mid=".$mid) ;
	hidden_field ("mid", $mid) ;
	hidden_field ("itemid", $ARRAY_media['itemid']) ;
	hidden_field ("albumid", $ARRAY_media['albumid']) ;
	hidden_field ("media_name", $ARRAY_media['media_name']) ;
	hidden_field ("original_name", $ARRAY_media['original_name']) ;
	// affichage de l'album concerné
	$album_title = get_field_by_id ("album", "album_title", "album_id", $ARRAY_media['albumid'], $db) ;
	lib_field ("Album concerné :", $album_title) ;
	// champs select pour confirmer
	submit_field ("&nbsp;", "validate", CONFIRM_DELETE) ;
	stopform () ;
}




















?>