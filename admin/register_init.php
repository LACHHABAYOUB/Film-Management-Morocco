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

require_once("admin/admin.php") ;

page_header ($USER, 2, $db) ;
echo make_left_column ("media", $RIGHTS, $left_lib) ;

echo "\t\t\t<h1>" . LIBM_REGISTER . "</h1>\r\n" ;

display_in_box ("", "", TO_REGISTER) ;

// initialisation des valeurs des champs
$PRESET_account = array("user_email"=>"","user_login"=>"") ;
// initialisation des erreurs pour les champs sous contrôle
$ERROR_account = array("user_email"=>"","user_login"=>"") ;
// appel du formulaire
register_form ("new", $PRESET_account, $ERROR_account, $db) ;

page_footer($footer_lib) ;
?>