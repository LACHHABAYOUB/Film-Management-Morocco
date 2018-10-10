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

// Acquisition albumid à afficher
if (isset ($_GET['albumid'])) $albumid = $_GET['albumid'] ;
else error_page (ERR_UNDEFINED, "media", $RIGHTS, $footer_lib, $db ) ;
// Recherche album à afficher
$ARRAY_album = search_row_by_id ("album", "album_id", $albumid, $db, TABLEAU) ;
if (is_array($ARRAY_album)) {
	foreach ($ARRAY_album as $cle => $valeur) $ARRAY_album[$cle] = html_convert($valeur);
	$visibility = $ARRAY_album['album_visibility'] ;
// Si album inexistant on arrete la
} else error_page (ERR_NO_PAGE, "media", $RIGHTS, $footer_lib, $db ) ;

// Incrementation du champs clicks de l'album
increment_click ($albumid, "album", 3, $db);

// Acquisition du groupe de medias a afficher
if (isset ($_GET['start'])) $start = $_GET['start'] ;
else $start = 0 ;

// Nombre de medias par page
$qty_page = 12 ;

// Comptage des medias a afficher
$result = $db->query("SELECT media_name FROM media WHERE albumid = $albumid") ;
$media_qty = $db->num_rows($result) ;

// Pagination
$paging = "\t\t\t<h4>Page : " ;
if ($start == 0) {
	$paging .= "&nbsp;<span class=\"pagination_selected\">1</span>&nbsp;" ;
} else {
	$paging .= "&nbsp;<a href=\"index.php?location=media&amp;action=display_album&amp;albumid=".$albumid."&amp;start=0\" title=\"".DISPLAY_THIS_PAGE."\">1</a>&nbsp;" ;
}
for ($i=1 ; ($i * $qty_page) < $media_qty ; $i++) {
	$page = $i + 1 ;
	$here = $i * $qty_page ;
	if ($here != $start) $paging .= "&nbsp;<a href=\"index.php?location=media&amp;action=display_album&amp;albumid=".$albumid."&amp;start=".$here."\" title=\"".DISPLAY_THIS_PAGE."\">".$page."</a>&nbsp;" ;
	else $paging .= "&nbsp;<span class=\"pagination_selected\">".$page."</span>&nbsp;" ;
}
$paging .= "</h4>\r\n" ;

page_header ($USER, 1, $db) ;

echo "\t\t\t<h1>".ALBUM." : ".$ARRAY_album['album_title']." <span class=\"jaune\">[ ".$media_qty." medias ]</span> </h1>\r\n" ;

// Affichage de la pagination
echo $paging ;

// Extraction d'un paquet de $qty_page medias commençant à $start
$paquet = $db->query("SELECT * FROM media WHERE albumid = $albumid ORDER BY mid ASC LIMIT $start, $qty_page");

// début de table
$mt = "\t\t\t<table id=\"albumDisplayTable\">\r\n" ;
	$mt .= "\t\t\t\t<tr>\r\n" ;
		$mt .= "\t\t\t\t\t<td>".fill_it(160, 1)."</td>\r\n" ;
		$mt .= "\t\t\t\t\t<td>&nbsp;</td>\r\n" ;
		$mt .= "\t\t\t\t\t<td>".fill_it(160, 1)."</td>\r\n" ;
		$mt .= "\t\t\t\t\t<td>&nbsp;</td>\r\n" ;
		$mt .= "\t\t\t\t\t<td>".fill_it(160, 1)."</td>\r\n" ;
		$mt .= "\t\t\t\t\t<td>&nbsp;</td>\r\n" ;
		$mt .= "\t\t\t\t\t<td>".fill_it(160, 1)."</td>\r\n" ;
	$mt .= "\t\t\t\t</tr>\r\n" ;

$album_folder = generate_album_dir_name ($albumid) ;
$directory = UPLDP.$album_folder."/thumbnail/" ;
 
$cell = 1 ;
while ($OBJ_row = $db->fetch_object($paquet)) {
	$location = ($cell + 4) % 4 ; // 1 pour 1,5,9,13 | 2 pour 2,6,10,14 | 3 pour 3,7,11,15 | 0 pour 4,8,12,16
	$mt .= display_album_cell ($OBJ_row, $location, $directory) ;
	$cell++ ;
}
// Fin de table suivant valeur de $location
if ($location == 3) {
	$end = 1 ;
} elseif ($location == 2) {
	$end = 3 ;
} elseif ($location == 1) {
	$end = 5 ;
} else $end = 0 ;
for ($i=0; $i<$end; $i++) {
	$mt .= "\t\t\t\t\t<td>&nbsp;</td>\r\n" ;
}
// fin de la table
if ($location != 0) {
	$mt .= "\t\t\t\t<tr>\r\n" ;
		$mt .= "\t\t\t\t\t<td colspan=\"7\">&nbsp;</td>\r\n" ;
	$mt .= "\t\t\t\t</tr>\r\n" ;
}
$mt .= "\t\t\t</table>\r\n" ;

echo $mt ;

function display_album_cell ($OBJ_row, $location, $directory) {
	$amt = "" ;
	$m = $OBJ_row->media_name ;
	list ($type, $code, $ext) = explode (".", $m) ;
	$mid = $OBJ_row->mid ;
	$title = $OBJ_row->title ;
	$author = $OBJ_row->author ;
	$name = $OBJ_row->original_name ;
	if ($type == "a") {
		$cellule = "<td class=\"cellAudio\"><a href=\"index.php?location=media&amp;action=play_media&amp;mid=".$mid."\"  title=\"".PLAY_AUDIO_MEDIA."\">" ;
		$cellule .= $author."<br />".$title ;
		$cellule .= "</a>" ;
	} elseif ($type == "v") {
		$cellule = "<td class=\"cellVideo\"><a href=\"index.php?location=media&amp;action=play_media&amp;mid=".$mid."\"  title=\"".PLAY_VIDEO_MEDIA."\">" ;
		$cellule .= $title."<br />".$author ;
		$cellule .= "</a>" ;
	} else {
		$thumb_image = "mini.".$code.".".$ext ;
		$cellule = "<td class=\"cellPicture\"><a href=\"index.php?location=media&amp;action=play_media&amp;mid=".$mid."\"  title=\"".$name." - ".$title."\">" ;
		$cellule .= "<img src=\"".$directory.$thumb_image."\" />" ;
		$cellule .= "</a>" ;
	}
	if ($location == 1) { // demarre une ligne
		$amt .= "\t\t\t\t<tr>\r\n" ;
		$amt .= "\t\t\t\t\t".$cellule."</td>\r\n" ;
		$amt .= "\t\t\t\t\t<td>&nbsp;</td>\r\n" ;
	}
	elseif (($location == 2) or ($location == 3)) { // cellules du milieu
		$amt .= "\t\t\t\t\t".$cellule."</td>\r\n" ;
		$amt .= "\t\t\t\t\t<td>&nbsp;</td>\r\n" ;
	} else { // termine une ligne
		$amt .= "\t\t\t\t\t".$cellule."</td>\r\n" ;
		$amt .= "\t\t\t\t</tr>\r\n" ;
		// ligne vide de separation
		$amt .= "\t\t\t\t<tr>\r\n" ;
		$amt .= "\t\t\t\t\t<td colspan=\"7\">&nbsp;</td>\r\n" ;
		$amt .= "\t\t\t\t</tr>\r\n" ;
	}
	return $amt ;
}
page_footer($footer_lib) ;
?>