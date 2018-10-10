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
// acquisition albumid
if (isset ($_GET['albumid'])) $albumid = $_GET['albumid'] ;
else error_popup (ERR_UNDEFINED) ;
// recherche album correspondant
$ARRAY_album = search_row_by_id ("album", "album_id", $albumid, $db, TABLEAU) ;
if (is_array($ARRAY_album)) foreach ($ARRAY_album as $cle => $valeur) $ARRAY_album[$cle] = html_convert($valeur);
// si album inexistant on arrete la
else error_popup (ERR_NO_RECORD) ;

// autorisation de poursuivre
if (($RIGHTS != "administrator") and ($RIGHTS != "private")) error_popup (ERR_ACCESS) ;

$bandeau = DELETE_ALBUM." : ".$ARRAY_album['album_title'] ;
$page_head = DELETE_ALBUM." : <span class=\"jaune\">".$ARRAY_album['album_title']."</span>" ;

// Debut de page en html
popup_header ($bandeau, "look_popup") ;

echo "<div id=\"page_head\">\r\n" ;
echo "\t<h3>" . $page_head . "</h3>\r\n" ;
echo "\t<h1>&nbsp;</h1>\r\n" ;
echo "</div>\r\n" ;

// recherche du nombre de médias contenus dans l'album
$sql_compte = $db->query("SELECT mid FROM media WHERE albumid = $albumid") ;
$media_qty = $db->num_rows($sql_compte) ;

// message d'avertisement si l'album contient des médias
if ($media_qty > 0) {
	$warning = "Cet album ne peut pas être supprimé dans l'immédiat car il contient ".$media_qty." médias.<br />Vous devez au préalable :<br />- soit déplacer<br />- soit supprimer<br />les médias contenus dans cet album." ;
	display_in_box ("", $warning) ;
} else {
	if (!isset($_GET['status'])) {
		// affichage d'un message d'avertissement et d'un formulaire de validation
		$warning = "Attention !<br />Vous êtes sur le point de supprimer l'album :<br />".$ARRAY_album['album_title']."<br />Cette action est irréversible !" ;
		display_in_box ("", $warning) ;
		album_delete_form ($ARRAY_album, $db) ;
	} elseif ($_GET['status'] == "submit") {
		$feedback = $warning = "" ;
		$album_id = $_POST['album_id'] ;
		// construction des chemins vers les dossiers à supprimer
		$main_directory = UPLDP.generate_album_dir_name ($album_id) ;
		$thumb_directory = $main_directory."/thumbnail" ;
		// suppression des dossiers de l'album
		$warn1 = $warn2 = "" ;
		/*
		if (is_dir($thumb_directory)) {
			chmod($thumb_directory, 0666);
			if (!unlink ($thumb_directory)) $warn2 = "no good thumb" ;
		}
		chmod($main_directory, 0666);
		if (!unlink ($main_directory)) $warn1 = "no good main" ;
		*/
		// mise à jour de la table album (si les dossiers ont bien été supprimés)
		if (($warn1 == "") and ($warn2 == "")) {
			$delete_sql = "DELETE FROM `album` WHERE `album`.`album_id` = '$album_id' LIMIT 1 ;" ;
			$db->query ($delete_sql);
			if (mysql_affected_rows() == 1) $feedback = "L'album <span class=\"jaune\"> ".$_POST['album_title']." </span><br />a bien été supprimé,<br />ainsi que les dossiers le concernant." ;
		} else $warning = $warn1."<br />".$warn2 ;
		// Affichage du message de feedback
		display_in_box ($feedback, $warning) ;
	}
}

// fin de page html
popup_footer () ;

function album_delete_form ($ARRAY_album, $db) {
	$albumid = $ARRAY_album['album_id'] ;
	startform ("delete_album", "index.php?location=media&amp;action=album_delete_request&amp;status=submit&amp;albumid=".$albumid) ;
	hidden_field ("album_id", $ARRAY_album['album_id']) ;
	hidden_field ("album_title", $ARRAY_album['album_title']) ;
	hidden_field ("album_author", $ARRAY_album['album_author']) ;
	lib_field ("Album concerné :", $ARRAY_album['album_title']) ;
	lib_field ("Auteur de cet album :", $ARRAY_album['album_author']) ;
	// champs select pour confirmer
	submit_field ("&nbsp;", "validate", CONFIRM_DELETE) ;
	stopform () ;
}
?>