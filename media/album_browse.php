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

// acquisition du _POST contenant les critères de recherche
if (isset ($_POST['option'])) {
	switch ($_POST['option']) {
		case "most_new": $order = "ORDER BY album_maked_on DESC" ; break ;
		case "most_seen": $order = "ORDER BY album_clicks DESC" ; break ;
		case "other": $order = "ORDER BY album_title ASC" ; break ;
	}
	if (isset ($_POST['string'])) $string = $db->real_escape_string($_POST['string']);
	else $string = "%" ;
	$CRITERIA = " album_title LIKE '%$string%' ".$order ;
	save_in_cache ($CRITERIA, "cache_criteria", session_id(), $db) ;
} else {
	$CRITERIA = get_from_cache ("cache_criteria", session_id(), $db) ;
}

// acquisition du groupe d'albums à afficher
if (isset ($_GET['start'])) $start = $_GET['start'] ;
else $start = 0 ;

// Nombre d'albums par page
$qte_album_par_page = 20 ;

// Comptage des albums a afficher
$result = $db->query("SELECT album_id
 FROM album
 WHERE album_id > 0
 AND $FILTER
 AND $CRITERIA") ;
$albums_qty = $db->num_rows($result) ;

// Pagination
$paging = "\t\t\t<h4>Page : " ;
if ($start == 0) {
	$paging .= "&nbsp;<span class=\"pagination_selected\">1</span>&nbsp;" ;
} else {
	$paging .= "&nbsp;<a href=\"index.php?location=media&amp;action=browse_album&amp;start=0\" title=\"".DISPLAY_THIS_PAGE."\">1</a>&nbsp;" ;
}
for ($i=1 ; ($i * $qte_album_par_page) < $albums_qty ; $i++) {
	$page = $i + 1 ;
	$here = $i * $qte_album_par_page ;
	if ($here != $start) $paging .= "&nbsp;<a href=\"index.php?location=media&amp;action=browse_album&amp;start=".$here."\" title=\"".DISPLAY_THIS_PAGE."\">".$page."</a>&nbsp;" ;
	else $paging .= "&nbsp;<span class=\"pagination_selected\">".$page."</span>&nbsp;" ;
}
$paging .= "</h4><h4>&nbsp;</h4>\r\n" ;

page_header ($USER, 1, $db) ;

echo "\t\t\t<h1><a href=\"index.php?location=media&amp;action=browse_album_form\">".LIBM_BROWSE_ALBUM."</a></h1>\r\n" ;

// Affichage de la pagination
echo $paging ;

// extraction d'un paquet de $qte_album_par_page commençant à $start
$paquet = $db->query("SELECT *
 FROM album
 WHERE album_id > 0
 AND $FILTER
 AND $CRITERIA
 LIMIT $start, $qte_album_par_page");

for ($i=0; $i<$qte_album_par_page; $i++) {
	$occurence = $db->fetch_assoc($paquet) ;
	echo (display_album_line ($occurence, ($i + 1 + $start), $db, $RIGHTS)) ;
}
echo "\t\t\t<h3>&nbsp;</h3>\r\n" ;

page_footer($footer_lib) ;
?>