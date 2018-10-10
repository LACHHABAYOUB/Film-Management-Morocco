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
// Acquisition mid à afficher
if (isset ($_GET['mid'])) $mid = $_GET['mid'] ;
else error_page (ERR_UNDEFINED, "media", $path, $footer_lib, $db ) ;
// Recherche image à afficher
$ARRAY_image = search_row_by_id ("media", "mid", $mid, $db, TABLEAU);
if (is_array($ARRAY_image)) foreach ($ARRAY_image as $cle => $valeur) $ARRAY_image[$cle] = html_convert($valeur);
// Si image inexistante on arrête là
else error_popup (ERR_NO_PAGE, "media", $path, $footer_lib, $db ) ;

// Acquisition : dossier de l'album, image à afficher
$itemid = $ARRAY_image["itemid"] ;
$albumid = $ARRAY_image["albumid"] ;
$album_name = generate_album_dir_name($albumid) ;
$image_name = $ARRAY_image["media_name"] ;
$picture = UPLDP.$album_name."/".$image_name ;

/* Autorisation de lecture
TBD
$ARRAY_album = search_row_by_id ("album", "album_id", $albumid, $db, TABLEAU) ;
$visibility = $ARRAY_album['album_visibility'] ;
*/

// incrémentation du champs clicks du media
increment_click ($mid, "media", 3, $db);

// Redimensionnement de l'image
$max_width = SCREEN_WIDTH - 12 ;
$max_height = SCREEN_HEIGHT - 95 ;
$max = image_max ($picture, $max_width, $max_height) ;
$output_picture = $max[0] ;

// bandeau
$bandeau = $image_name." + infos album tbd" ;

// Sortie html
popup_header ($bandeau, "poster_vamp") ;
echo "<table width=\"100%\" height=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\r\n" ;
echo "\t<tr>\r\n" ;
echo "\t\t<td align=\"center\">".$output_picture."</td>\r\n" ;
echo "\t</tr>\r\n" ;
echo "</table>\r\n" ;
popup_footer () ;
?>