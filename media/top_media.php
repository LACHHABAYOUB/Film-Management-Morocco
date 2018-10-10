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

// acquisition du family de media
if (isset ($_GET['family'])) $family = $_GET['family'] ;
else error_page (ERR_UNDEFINED, "media", $RIGHTS, $footer_lib, $db ) ;

// Recherche des tops
if ($family == "audio") $top_result = $db->query("SELECT *
 FROM media, album
 WHERE media.albumid = album.album_id
 AND $FILTER
 AND itemid = 1
 ORDER BY media.clicks DESC
 LIMIT 8") ;
elseif ($family == "image") $top_result = $db->query("SELECT *
 FROM media, album
 WHERE media.albumid = album.album_id
 AND $FILTER
 AND (media.itemid > 1 AND media.itemid < 5)
 ORDER BY media.clicks DESC
 LIMIT 12") ;
elseif ($family == "video") $top_result = $db->query("SELECT *
 FROM media, album
 WHERE media.albumid = album.album_id
 AND $FILTER
 AND itemid = 5
 ORDER BY media.clicks DESC
 LIMIT 8") ;

$total_outputs = $db->num_rows($top_result) ;

page_header ($USER, 1, $db) ;

if ($family == "audio") echo "\t\t\t<h1>".TOP_AUDIO."</h1>\r\n" ;
elseif ($family == "image") echo "\t\t\t<h1>".TOP_IMAGE."</h1>\r\n" ;
elseif ($family == "video") echo "\t\t\t<h1>".TOP_VIDEO."</h1>\r\n" ;


// début de table
$mt = "\t\t\t<table id=\"albumDisplayTable\">\r\n" ;
	$mt .= "\t\t\t\t<tr>\r\n" ;
		$mt .= "\t\t\t\t\t<td>".fill_it(160,1)."</td>\r\n" ;
		$mt .= "\t\t\t\t\t<td>&nbsp;</td>\r\n" ;
		$mt .= "\t\t\t\t\t<td>".fill_it(160,1)."</td>\r\n" ;
		$mt .= "\t\t\t\t\t<td>&nbsp;</td>\r\n" ;
		$mt .= "\t\t\t\t\t<td>".fill_it(160,1)."</td>\r\n" ;
		$mt .= "\t\t\t\t\t<td>&nbsp;</td>\r\n" ;
		$mt .= "\t\t\t\t\t<td>".fill_it(160,1)."</td>\r\n" ;
	$mt .= "\t\t\t\t</tr>\r\n" ;

$cell = 1 ;
while ($OBJ_row = $db->fetch_object($top_result)) {
	$location = ($cell + 4) % 4 ; // 1 pour 1,5,9,13 | 2 pour 2,6,10,14 | 3 pour 3,7,11,15 | 0 pour 4,8,12,16
	$mt .= display_top_cell ($OBJ_row, $location) ;
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
page_footer($footer_lib) ;

function display_top_cell ($OBJ_row, $location) {
	$amt = "" ;
	$albumid = $OBJ_row->albumid ;
	$album_folder = generate_album_dir_name ($albumid) ;
	$directory = UPLDP.$album_folder."/thumbnail/" ;
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
?>