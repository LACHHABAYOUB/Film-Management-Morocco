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

page_header ($USER, 2, $db) ;
echo make_left_column ("media", $RIGHTS, $left_lib) ;

echo "\t\t\t<h1>" . LIBM_BROWSE_ALBUM . "</h1>\r\n" ;

// formulaire de recherche
startform ("search", "index.php?location=media&amp;action=browse_album", "Faites votre choix") ;
$OPTION_list = array ("most_new"=>MOST_NEW, "most_seen"=>MOST_SEEN, "other"=>OTHER) ;
echo radio_field (BROWSE_OPTION, "option", "other", $OPTION_list) ;
text_field (SEARCHED_STRING, "string",  "", 2) ;
end_entete() ;
submit_field ("&nbsp;", "validate", CONFIRM_SEARCH) ;
stopform () ;

page_footer($footer_lib) ;
?>