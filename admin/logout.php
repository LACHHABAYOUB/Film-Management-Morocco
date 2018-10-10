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

// début de page popup
popup_header (LIBM_LOGOUT, "look_popup", 2000) ;

echo "<div id=\"page_head\">\r\n" ;
echo "\t<h3>".END_SESSION."<br />".session_id()."</h3>\r\n" ;
echo "</div>\r\n" ;

echo "<div id=\"information\">\r\n" ;
if (is_object($OBJET_session)) {
	echo "\t<p>".BYE_BYE."<span class=\"jaune\">".$OBJET_session->session_login . "</span><br />\r\n" ;
	session_destroy();
	$request  = "DELETE FROM session WHERE session_id = '$OBJET_session->session_id' LIMIT 1";
	$result = $db->query ($request);
	echo SEE_YOU_SOON ."\t</p>\r\n" ;
}
echo "</div>\r\n" ;

// Fin de page popup
popup_footer () ;
?>