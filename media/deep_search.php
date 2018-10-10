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

// acquisition du _POST contenant les critères de recherche
// #1
if ($_POST['selected_album_title'] != "all") {
	$OBJ_album = search_row_by_string ("album", "album_title", $_POST['selected_album_title'], $db) ;
	$album_id = $OBJ_album->album_id ;
} else $album_id = "%";
// #2
$title = $db->real_escape_string($_POST['title']) ;
// #3
// $begin_date = "2000-01-01" ;
$begin_date = $_POST['begin_yea'].$_POST['begin_r']."-".$_POST['begin_month']."-".$_POST['begin_day'] ;
// #4
// $end_date = "2020-01-01" ;
$end_date = $_POST['end_yea'].$_POST['end_r']."-".$_POST['end_month']."-".$_POST['end_day'] ;
// #5
$author = $db->real_escape_string($_POST['author']) ;
// #6
$comment = $db->real_escape_string($_POST['comment']) ;
// trier par
$sorted_by = $_POST['sort_by'] ;
if ($sorted_by == "date") $sort_by = "event_date" ;
else $sort_by = $sorted_by ;
// ordre de sortie
$output_order = $_POST['output_order'] ;
// création de la requête de recherche
$requete = "SELECT *
 FROM media, album
 WHERE $FILTER
 AND media.albumid LIKE '%$album_id%'
 AND media.title LIKE '%$title%'
 AND media.event_date > '$begin_date' AND media.event_date < '$end_date'
 AND media.author LIKE '%$author%'
 AND media.comment LIKE '%$comment%'
 AND media.albumid = album.album_id
 ORDER BY media.$sort_by $output_order
 LIMIT 30" ;

$result = $db->query($requete) ;
$total_outputs = $db->num_rows($result) ;

echo "\t\t\t<h1>".YOUR_SEARCH."</h1>\r\n" ;

for ($i=0; $i<$total_outputs; $i++) {
   $ARRAY_occurence = $db->fetch_assoc($result) ;
	echo (display_media_line ($ARRAY_occurence, ($i + 1), $db, $RIGHTS)) ;
}

page_footer($footer_lib) ;
?>