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
page_header ($USER, 2, $db) ;
echo make_left_column ("media", $RIGHTS, $left_lib) ;

if (($RIGHTS == "administrator") or ($RIGHTS == "private")) echo "\t\t\t<h1>Vue globale du gestionnaire de contenu multimédia</h1>\r\n" ;
elseif ($RIGHTS == "member") echo "\t\t\t<h1>Vue globale pour les membres</h1>\r\n" ;
else echo "\t\t\t<h1>Vue restreinte pour les visiteurs</h1>\r\n" ;

// comptage des albums (en fonction du client : filter)
$result = $db->query("SELECT album_title FROM album WHERE $FILTER") ;
$album_qty = $db->num_rows($result) ;
// comptage des medias suivant leur type (en fonction du client : filter)
$sql = "SELECT media.itemid, album.album_visibility
 FROM media, album
 WHERE media.albumid = album.album_id
 AND $FILTER" ;
$result = $db->query($sql) ;
$mp3 = $gif = $jpg = $png = $flv = $somme = 0 ;
while ($row = $db->fetch_object($result)) {
	if ($row->itemid == 1) $mp3++  ;
	if ($row->itemid == 2) $gif++  ;
	if ($row->itemid == 3) $jpg++  ;
	if ($row->itemid == 4) $png++  ;
	if ($row->itemid == 5) $flv++  ;
	$somme++ ;
}
// état du contenu du site
echo "\t\t\t<table id=\"homeTable\">\r\n\t\t\t<tr>\r\n" ;
echo "\t\t\t<td style=\"width: 70%; \">A l'heure actuelle, ".SITE." contient<br /><strong>".$album_qty."</strong> albums et <strong>".$somme."</strong> médias répartis comme suit :\r\n" ;
echo "\t\t\t\t<ul>\r\n" ;
echo "\t\t\t\t\t<li>".$mp3." plages audio mp3</li>\r\n" ;
echo "\t\t\t\t\t<li>".$gif." images au format gif</li>\r\n" ;
echo "\t\t\t\t\t<li>".$jpg." photos en jpeg</li>\r\n" ;
echo "\t\t\t\t\t<li>".$png." images au format png</li>\r\n" ;
echo "\t\t\t\t\t<li>".$flv." clips vidéo flash flv</li>\r\n" ;
echo "\t\t\t\t</ul>\r\n" ;

// Recherche du dernier album créé (en fonction du client : filter) ### retourne le premier album non vide
$sql = "SELECT media.albumid, album.album_id, album.album_title, album.album_visibility 
 FROM media, album
 WHERE media.albumid = album.album_id
 AND $FILTER
 ORDER BY album.album_id DESC
 LIMIT 1 " ;
$result = $db->query($sql);
$LAST_album = $db->fetch_object($result) ;
$last_album_id = $LAST_album->album_id ;
echo "\t\t\t\tDernier album créé : <a href=\"index.php?location=media&amp;action=display_album&amp;albumid=".$last_album_id."\"  title=\"Voir Album\"><span class=\"bleu\">".$LAST_album->album_title."</span><br /></a>\r\n" ;

// Recherche du dernier média envoyé (en fonction du client : filter)
$sql = "SELECT *
 FROM media, album
 WHERE media.albumid = album.album_id
 AND $FILTER
 ORDER BY media.mid DESC
 LIMIT 1 " ;
$result = $db->query($sql);
$LAST_media = $db->fetch_object($result) ;
$last_media_id = $LAST_media->mid ;
$albumid = $LAST_media->albumid ;
$album_folder = generate_album_dir_name ($albumid) ;
$directory = UPLDP.$album_folder."/thumbnail/" ;

$ARRAY_last_media = search_row_by_id ("media", "mid", $last_media_id, $db, TABLEAU);
$last_name = $ARRAY_last_media['media_name'] ;
list ($type, $code, $ext) = explode (".", $last_name) ;
$last_thumbnail = $directory."/mini.".$code.".".$ext ;

echo "\t\t\tDernier média envoyé : <a href=\"index.php?location=media&amp;action=play_media&amp;mid=".$last_media_id."\"  title=\"".$LAST_media->media_name."\"><span class=\"bleu\">".$LAST_media->original_name."</span></a></td>\r\n" ;

if ($LAST_media->itemid == 1) $right_img = "<img src=\"".UPLDP."generic/audio_media.png\" width=\"160\" height=\"120\" />" ;
elseif ($LAST_media->itemid == 5) $right_img = "<img src=\"".UPLDP."generic/video_media.png\" width=\"160\" height=\"120\" />" ;
else $right_img = "<img class=\"thumbnail\" src=\"".$last_thumbnail."\" alt=\"last_media\" />" ;
echo "\t\t\t<td style=\"width: 30%; \"><a href=\"index.php?location=media&amp;action=play_media&amp;mid=".$last_media_id."\"  title=\"".$LAST_media->media_name."\">".$right_img."</a></td></tr></table>\r\n" ;

// liens vers 4 pages de hits
echo "\t\t\t<p>&nbsp;</p>\r\n" ;
echo "\t\t\t<table id=\"hitsTable\">\r\n\t\t\t<tr>\r\n" ;
echo "\t\t\t\t<td><a href=\"index.php?location=media&amp;action=top_album\" class=\"grey_button\" title=\"".TOP_ALBUM."\">album :  top 10</a></td>\r\n" ;
echo "\t\t\t\t<td><a href=\"index.php?location=media&amp;action=top_media&amp;family=audio\" class=\"red_button\" title=\"".TOP_AUDIO."\">audios : top 8</a></td>\r\n" ;
echo "\t\t\t\t<td><a href=\"index.php?location=media&amp;action=top_media&amp;family=image\" class=\"green_button\" title=\"".TOP_IMAGE."\">images : top 12</a></td>\r\n" ;
echo "\t\t\t\t<td><a href=\"index.php?location=media&amp;action=top_media&amp;family=video\" class=\"blue_button\" title=\"".TOP_VIDEO."\">vidéos : top 8</a></td>\r\n" ;
echo "\t\t\t</tr></table>\r\n" ;
echo "\t\t\t<p>&nbsp;</p>\r\n" ;

page_footer($footer_lib) ;
?>