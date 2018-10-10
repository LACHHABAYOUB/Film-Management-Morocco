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

// autorisation d'accès au script
if ($RIGHTS != "administrator") error_page (ERR_ACCESS, "media", $RIGHTS, $footer_lib, $db ) ;

page_header ($USER, 2, $db) ;
echo make_left_column ("admin", $RIGHTS, $left_lib) ;

echo "\t\t\t<h1><a href=\"index.php?location=admin&amp;action=admins\"><<< ADMIN &nbsp; &nbsp; &nbsp; &nbsp;</a>" . LIBM_WHOS_ONLINE . "</h1>\r\n" ;

// Recherche des sessions ouvertes
$now = date("U") ;
$sql_search_sessions = "SELECT * FROM session WHERE session_time_limit > $now ORDER BY session_time_limit DESC" ;
$result = $db->query($sql_search_sessions) ;
$total_outputs = $db->num_rows($result) ;

// Initialisation du tableau de résultat 600px maxi
echo "\t\t<table id=\"commonTable\">\r\n" ;
	echo "\t\t\t<tr>\r\n" ;
		echo "\t\t\t\t<th width=\"30%\">Utilisateur</a></th>\r\n" ;
		echo "\t\t\t\t<th width=\"16%\">Droits</a></th>\r\n" ;
		echo "\t\t\t\t<th width=\"24%\">IP</a></th>\r\n" ;
		echo "\t\t\t\t<th width=\"30%\">date session</a></th>\r\n" ;
	echo "\t\t\t</tr>\r\n" ;

// Parcours du résultat de recherche
for ($i=0; $i<$total_outputs; $i++) {
   $ARRAY_occurence = $db->fetch_assoc($result) ;
	echo display_sessions_search_result ($ARRAY_occurence, ($i + 1), $db) ;
}

echo "\t\t</table>\r\n" ;

page_footer($footer_lib) ;

function display_sessions_search_result ($ARRAY_occurence, $i, $db) {
	$session_date = timestamp_to_datetime_fr (($ARRAY_occurence['session_time_limit']) - 14400) ;
	$search_result = "\t\t\t<tr>\r\n" ;
		$search_result .= "\t\t\t\t<td>".$ARRAY_occurence['session_login']."</td>\r\n" ;
		$search_result .= "\t\t\t\t<td>".$ARRAY_occurence['session_type']."</td>\r\n" ;
		$search_result .= "\t\t\t\t<td>".$ARRAY_occurence['session_ip_adress']."</td>\r\n" ;
		$search_result .= "\t\t\t\t<td>".$session_date."</td>\r\n" ;
	$search_result .= "\t\t\t</tr>\r\n" ;
	return $search_result ;
}
?>