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

echo "\t\t\t<h1>Administration : informations générales</h1>\r\n" ;

echo "\t\t\t<h4>DOMAIN = ".DOMAIN."</h4>\r\n" ;
echo "\t\t\t<h4>URI = ".$_SERVER['REQUEST_URI']."</h4>\r\n" ;
echo "\t\t\t<h4>UPLDP = ".UPLDP."</h4>\r\n" ;
echo "\t\t\t<hr />\r\n" ;

$admins = "Comptes administrateur : <strong><span class=\"rouge\"> &nbsp;" . users_qty ("admin", $db) ;
$public_members  = "Membres enregistrés (member) : <strong><span class=\"bleu\"> &nbsp;" . users_qty ("member", $db) ;
$private_members = "Membres déclarés privés (private) : <strong><span class=\"vert\"> &nbsp;" . users_qty ("private", $db) ;

echo "\t\t\t<h4>".$public_members."</span></strong></h4>\r\n" ;
echo "\t\t\t<h4>".$private_members."</span></strong></h4>\r\n" ;
echo "\t\t\t<h4>".$admins."</span></strong></h4>\r\n" ;

echo "\t\t\t<p>&nbsp;</p>\r\n" ;

page_footer($footer_lib) ;

function users_qty ($profil, $db) {
	$requete = "SELECT * FROM user WHERE user_rights LIKE '%".$profil."%'" ;
	$result = $db->query($requete);
	return $db->num_rows($result) ;
}
?>