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

echo "\t\t\t<h1><a href=\"index.php?location=admin&amp;action=admins\"><<< ADMIN &nbsp; &nbsp; &nbsp; &nbsp;</a>" . LIBM_MANAGE_ACCOUNT . "</h1>\r\n" ;

// Recherche des utilisateurs
$sql_search_users = "SELECT * FROM user WHERE user_login NOT LIKE 'Anonymous' ORDER BY " ;
	// aquisition du critère de tri
	if (!isset($_GET['sortby'])) $sql_search_users .= "user_login ASC" ;
	else {
		$sort_criteria = $_GET['sortby'] ;
		switch ($sort_criteria) {
			case "user": $sql_search_users .= "user_login ASC" ; break ;
			case "rights": $sql_search_users .= "user_rights ASC" ; break ;
			case "date": $sql_search_users .= "user_make_date DESC" ; break ;
			case "ip": $sql_search_users .= "user_ip ASC" ; break ;
			case "logs": $sql_search_users .= "user_logs DESC" ; break ;
		}
	}
$result = $db->query($sql_search_users) ;
$total_outputs = $db->num_rows($result) ;

// Initialisation du tableau de résultat avec liens pour tris
echo "\t\t<table id=\"commonTable\">\r\n" ;
	echo "\t\t\t<tr>\r\n" ;
		echo "\t\t\t\t<th width=\"30%\"><a href=\"index.php?location=admin&amp;action=manage_account&amp;sortby=user\" title=\"trier\">Utilisateur</a></th>\r\n" ;
		echo "\t\t\t\t<th width=\"22%\"><a href=\"index.php?location=admin&amp;action=manage_account&amp;sortby=rights\" title=\"trier\">Droits</a></th>\r\n" ;
		echo "\t\t\t\t<th width=\"16%\"><a href=\"index.php?location=admin&amp;action=manage_account&amp;sortby=date\" title=\"trier\">inscrit le</a></th>\r\n" ;
		echo "\t\t\t\t<th width=\"24%\"><a href=\"index.php?location=admin&amp;action=manage_account&amp;sortby=ip\" title=\"trier\">IP</a></th>\r\n" ;
		echo "\t\t\t\t<th width=\"8%\"><a href=\"index.php?location=admin&amp;action=manage_account&amp;sortby=logs\" title=\"trier\">Logs</a></th>\r\n" ;
	echo "\t\t\t</tr>\r\n" ;

// Parcours du résultat de recherche
for ($i=0; $i<$total_outputs; $i++) {
   $ARRAY_occurence = $db->fetch_assoc($result) ;
	echo display_users_search_result ($ARRAY_occurence, ($i + 1), $db) ;
}
echo "\t\t</table>\r\n" ;
//	echo "\t\t<h3>&nbsp;</h3>\r\n" ;

page_footer($footer_lib) ;

function display_users_search_result ($ARRAY_occurence, $i, $db) {
	$user_id = $ARRAY_occurence['user_login'] ;
	$user_rights = $ARRAY_occurence['user_rights'] ;
	$user_created_on = timestamp_to_datetime ($ARRAY_occurence['user_make_date']) ;
	$user_ip = $ARRAY_occurence['user_ip'] ;
	$user_logs = $ARRAY_occurence['user_logs'] ;
	if (($i % 2) != 0) $search_result = "\t\t\t<tr>\r\n" ;
	else $search_result = "\t\t\t<tr class=\"alt\">\r\n" ;
	$search_result .= "\t\t\t\t<td>".$user_id."</td>\r\n" ;
	// lien pour modif des droits dans une fenêtre popup
	$search_result .= "\t\t\t\t<td><img src=\"img/actions/action_edit.png\" height=\"18\" width=\"32\" alt=\"action_edit\" title=\"Editer le compte\" onclick=\"edit_popup('index.php?location=admin&amp;action=set_rights&amp;login=".$user_id."')\" />".$user_rights."</td>\r\n" ;
	
	$search_result .= "\t\t\t\t<td>".$user_created_on."</td>\r\n" ;
	$search_result .= "\t\t\t\t<td>".$user_ip."</td>\r\n" ;
	$search_result .= "\t\t\t\t<td>".$user_logs."</td>\r\n" ;
	$search_result .= "\t\t\t</tr>\r\n" ;
	return $search_result ;
}

?>