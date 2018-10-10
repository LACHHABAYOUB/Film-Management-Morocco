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
page_header ($USER, 1, $db) ;
// Acquisition du POST venant du header
$criteria = $_POST ;		//search_string	item_name
// mot cherché
if ($criteria['search_string'] == "Chercher") {
	$search_string = 'zzzzzzzz';
	$searched_string = '???';
} else {
	$searched_string = str_replace ( "*", "%", $criteria['search_string']);
	$search_string = $db->real_escape_string($searched_string);
}
// item choisi
if ($criteria['item_name'] == "all") $item_id =  '%';
else {
	$OBJ_item = search_row_by_string ("item", "item_fr", $criteria['item_name'], $db) ;
	$item_id = $OBJ_item->item_id ; 
}

// Création de la requête de recherche
$requete = "SELECT media.mid AS 'mid', media.itemid AS 'itemid', media.albumid AS 'albumid', media.title AS 'title', media.keywords AS 'keywords', media.media_name AS 'media_name', album.album_visibility AS 'visibility'
 FROM media, album
 WHERE media.albumid = album.album_id
 AND ".$FILTER."
 AND (media.keywords LIKE '%$search_string%' OR media.title LIKE '%$search_string%' OR media.original_name LIKE '%$search_string%')
 ORDER BY media.clicks DESC " ;

$result = $db->query($requete) ;
$total_outputs = $db->num_rows($result) ;

echo "\t\t\t<h1>".YOUR_SEARCH."« ".$searched_string." »<br />&nbsp;</h1>\r\n" ;

for ($i=0; $i<$total_outputs; $i++) {
   $ARRAY_occurence = $db->fetch_assoc($result) ;
	echo (display_media_line ($ARRAY_occurence, ($i + 1), $db, $RIGHTS)) ;
}
$warning = "" ;
if ($total_outputs == 0) $warning = NO_MEDIA_FOUND ;
if ($warning != "") echo "\t\t\t<h3>".$warning."</h3>\r\n" ;
echo "\t\t\t<h3>&nbsp;</h3>\r\n" ;

page_footer($footer_lib) ;
?>