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

$bandeau = MOVE_MEDIA." : ".$ARRAY_media['original_name'] ;
$page_head = MOVE_MEDIA." : <span class=\"jaune\">".$ARRAY_media['original_name']."</span>" ;

// Debut de page en html
popup_header ($bandeau, "look_popup") ;

echo "<div id=\"page_head\">\r\n" ;
echo "\t<h3>" . $page_head . "</h3>\r\n" ;
echo "\t<h1>&nbsp;</h1>\r\n" ;
echo "</div>\r\n" ;

if (!isset($_GET['status'])) {
	// affichage du formulaire pour le choix du nouvel album
	media_move_form ($ARRAY_media, $db) ;
} elseif ($_GET['status'] == "submit") {
	$feedback = $warning = "" ;
	$mid = $_POST['mid'] ;
	$itemid = $_POST['itemid'] ;
	$from_album_id = $_POST['albumid'] ;
	// contrôles du bon choix de l'album destinataire
	if (!isset($_POST['to_album_title'])) $warning = "Vous n'avez pas sélectionné l'album vers lequel le media doit être déplacé !" ; 
	else {
		$OBJ_album = search_row_by_string ("album", "album_title", $_POST['to_album_title'] , $db) ;
		$to_album_id = $OBJ_album->album_id ;
		if ($to_album_id == $from_album_id) $warning = "Erreur :<br />vous avez sélectionné l'album qui contient déja ce média !" ;
	}
	if ($warning == "") {
		$warn1 = $warn2 = "" ;
		$from_album_title = get_field_by_id ("album", "album_title", "album_id", $from_album_id, $db) ;
		$media_name = $_POST['media_name'] ;
		// construction des chemins
		$from_directory = UPLDP.generate_album_dir_name ($from_album_id) ;
		$to_directory = UPLDP.generate_album_dir_name ($to_album_id) ;
		$from = $from_directory."/".$media_name ;
		$to = $to_directory."/".$media_name ;
		$thumb_from = $from_directory."/thumbnail/min".$media_name ;
		$thumb_to = $to_directory."/thumbnail/min".$media_name ;
		// déplacement du fichier média et de la miniature associée le cas échéant
		if (!rename ($from, $to)) $warn1 = "no good" ;
		if (($itemid > 1) and ($itemid < 5)) {
			if (!rename ($thumb_from, $thumb_to)) $warn2 = "no good" ;
		}
		// mise à jour de la ligne concernée dans la table media (si les fichiers ont bien été déplacés)
		if (($warn1 == "") and ($warn2 == "")) {
			$move_sql = "UPDATE `media` SET `albumid` = '$to_album_id' WHERE `media`.`mid` = '$mid' LIMIT 1 ;" ;
			$db->query ($move_sql);
			if (mysql_affected_rows() == 1) $feedback = "Le media <span class=\"jaune\"> ".$_POST['original_name']." </span>a bien été déplacé<br />de l'album : <i>".$from_album_title."</i><br />vers l'album : <strong>".$_POST['to_album_title']."</strong>" ;
		} else $warning = $warn1."<br />".$warn2 ;
		
	}
	// Affichage du message de feedback
	display_in_box ($feedback, $warning) ;
}

popup_footer () ;

function media_move_form ($ARRAY_media, $db) {
	$mid = $ARRAY_media['mid'] ;
	startform ("move_media", "index.php?location=media&amp;action=move_request&amp;status=submit&amp;mid=".$mid) ;
	hidden_field ("mid", $mid) ;
	hidden_field ("itemid", $ARRAY_media['itemid']) ;
	hidden_field ("albumid", $ARRAY_media['albumid']) ;
	hidden_field ("media_name", $ARRAY_media['media_name']) ;
	hidden_field ("original_name", $ARRAY_media['original_name']) ;
	// affichage de l'album courant
	$from_album_title = get_field_by_id ("album", "album_title", "album_id", $ARRAY_media['albumid'], $db) ;
	lib_field (ALBUM_FROM, $from_album_title) ;
	// afichage d'un champs select pour le choix du nouvel album
	$result = $db->query("SELECT * FROM album ORDER BY album_title ASC ");
	while ($OBJ_row = $db->fetch_object($result)) {
		$liste_albums[$OBJ_row->album_title] = $OBJ_row->album_title ;
	}
	// champs select pour sélectionner un album
	echo select_field (ALBUM_TO, "to_album_title", "", $liste_albums, "", 3) ;
	submit_field ("&nbsp;", "validate", CONFIRM_MOVE) ;
	stopform () ;
}
?>