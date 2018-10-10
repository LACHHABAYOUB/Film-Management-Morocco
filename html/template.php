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
function page_header ($USER, $outline, $db) {
	require ("html/html_meta.inc");
	echo "\r\n\t<title>" . SITE . "</title>\r\n" ;
	// inclusion des fichiers css et js
	echo "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"css/look.css\">\r\n" ;
	$current_script = $_SERVER['REQUEST_URI'] ;
	if ((strstr($current_script, 'none')) or (strstr($current_script, 'my_account')) or (strstr($current_script, 'comment_media'))) echo "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"css/look_button.css\">\r\n" ;
	echo "\t<script src=\"js/fonctions.js\"></script>\r\n" ;
	// fin du head et début du body
	echo "</head>\r\n<body>\r\n<div id=\"global\">\r\n" ;
	echo "\t<div id=\"header\">\r\n" ;
		// table dans header
		echo "\t\t<table>\r\n\t\t\t<tr>\r\n" ;
			// colonne 1 : logo
			echo "\t\t\t\t<td style=\"width:185px\"><a href=\"index.php?location=media&amp;action=none\" title=\"".HEAD_HOME."\"><img src=\"img/logo_gcm.png\" width=\"180\"  height=\"43\" alt=\"Home\" title=\"".HEAD_HOME."\"></a></td>\r\n" ;
			// colonne 2 : moteur de recherche
			echo "\t\t\t\t<td style=\"width:375px\"><form method=\"post\" action=\"index.php?location=media&amp;action=find\" name=\"general_search\" id=\"general_search\">\r\n" ;
			echo "\t\t\t\t<input type=\"text\" name=\"search_string\" value=\"".HEAD_SEARCH."\" ; onfocus=\"if(this.value=='".HEAD_SEARCH."') this.value='';\" onblur=\"if(this.value=='') this.value='".HEAD_SEARCH."';\" size=\"16\" maxlength=\"16\" />\r\n" ;
			echo "\t\t\t\t&nbsp;&nbsp;\r\n" ;
			$liste_items["all"] = HEAD_ALL_ITEMS ;
			$item = "item_" . LANG ;
			$result = $db->query("SELECT $item FROM item ORDER BY $item ASC ") ;
			while ($row = $db->fetch_object($result)) {
				if (LANG == "fr") $liste_items[$row->item_fr] = $row->item_fr ;
				else $liste_items[$row->item_en] = $row->item_en ;
			}
			echo (select_field ("", "item_name", HEAD_ALL_ITEMS, $liste_items)) ;
			echo "\t\t\t\t&nbsp;&nbsp;\r\n" ;
			echo "\t\t\t\t<input type=\"submit\" name=\"send\" value=\"".HEAD_FIND."\" size=\"0\" maxlength=\"0\" />\r\n" ;
			echo "\t\t\t\t</form>\t\t\t\t</td>\r\n" ;
			// colonne 3 : icône # 1
			echo "\t\t\t\t<td style=\"width:44px\"><a href=\"".GCMCECKURL."help.php\" title=\"".HELP."\"><img src=\"img/actions/action_help.png\" width=\"44\"  height=\"24\" alt=\"action_help\" /></a></td>\r\n" ;
			// colonne 4 : icône # 2
			echo "\t\t\t\t<td style=\"width:44px\"><a href=\"".GCMCECKURL."about.php\" title=\"".ABOUT."\"><img src=\"img/actions/about.png\" width=\"44\"  height=\"24\" alt=\"about\" /></a></td>\r\n" ;
			// colonne 5 : affichage de l'hôte client
			echo "\t\t\t\t<td style=\"width:100px\">".$USER."</td>\r\n" ;
			// colonne 6 : action déconnexion
			if ($USER == "") fill_it(44,24) ;
			else echo "\t\t\t\t<td style=\"width:50px\"><img src=\"img/actions/action_logout.png\" width=\"44\"  height=\"24\" title=\"".LOGOUT."\" alt=\"action_logout\" onclick=\"javascript:open_logout();\" /></td>\r\n" ;
		echo "\t\t\t</tr>\r\n\t\t</table>\r\n" ;
	echo "\t</div><!-- #header -->\r\n" ;
	echo "\t<!-- DEBUT du CONTENU DYNAMIQUE -->\r\n" ;
	// initialisation du contenu central dynamique
	if ($outline == 1) {
		echo "\t<div id=\"container_one\">\r\n" ;
		echo "\t\t<div id=\"full_content\">\r\n" ;
	} elseif ($outline == 2) {
		echo "\t<div id=\"container_two\">\r\n" ;
		echo "\t\t<div id=\"left_content\">\r\n" ;
	}
}
// ############## ne toucher à rien (même pas un saut de ligne) #############################
function page_footer ($footer_lib="default") {
	echo "\t\t</div><!-- #xxx_content -->\r\n\t</div><!-- #container_xxx -->\r\n" ;
	echo "\t<!-- FIN du CONTENU DYNAMIQUE -->\r\n" ;
	// debut du pieds de page
	echo "\t<div id=\"footer\">\r\n" ;
		echo "\t\t<p class=\"align_left\"><a href=\"".GCMCECKURL."copyright.php\">droits d'auteur &copy; copyright</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"http://www.ceck.org/Applications-Web/GCM/\">site internet</a></p>" ;
		echo "<p class=\"align_right\">".$footer_lib."</p>\r\n" ;
		echo "\t\t<div style=\"clear: both;\"></div>\r\n" ;
	echo "\t</div><!-- #footer -->\r\n" ;
	echo "</div><!-- #global -->\r\n" ;
	echo "</body>\r\n</html>" ;
	exit ; 
}
function popup_header ($bandeau, $fds, $tempo=90000) {
	require ("html/html_meta.inc");
	echo "\r\n<title>" . $bandeau . "</title>\r\n" ;
	// inclusion du fichier css $fds
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"css/" . $fds . ".css\" media=\"all\" />\r\n" ;
	echo "</head>\r\n<body onload=\"setTimeout('self.close();',".$tempo.")\">\r\n" ;
	echo "<!-- DEBUT du CONTENU DYNAMIQUE -->\r\n" ;
}
function popup_footer () {
	echo "\r\n<!-- FIN du CONTENU DYNAMIQUE -->\r\n" ;
	echo "</body>\r\n</html>" ;
	exit ; 
}
?>