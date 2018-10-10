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

// Recherche des 10 albums les plus regardés
$top_sql = "SELECT `album`.* 
 FROM album
 WHERE album_id > 0
 AND $FILTER
 ORDER BY album_clicks DESC
 LIMIT 10" ;
$result = $db->query($top_sql) ;
$total_outputs = $db->num_rows($result) ;

page_header ($USER, 1, $db) ;

echo "\t\t\t<h1>" . TOP_ALBUM . "</h1>\r\n" ;

for ($i=0; $i<$total_outputs; $i++) {
   $occurence = $db->fetch_assoc($result) ;
	echo (display_album_line ($occurence, ($i + 1), $db, $RIGHTS)) ;
}

if ($total_outputs == 0) echo "\t\t\t<br />" . NO_ALBUM_FOUND . "\r\n" ;

echo "\t\t\t<h3>&nbsp;</h3>\r\n" ;

page_footer($footer_lib) ;
?>