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

page_header ($USER, 2, $db) ;
echo make_left_column ("media", $RIGHTS, $left_lib) ;

echo "\t\t\t<h1>" . LIBM_DEEP_SEARCH . "</h1>\r\n" ;

// début du formulaire
startform ("deep_search", "index.php?location=media&amp;action=deep_search", ENTER_CRITERIA) ;
// critère #1 : album
$result = $db->query("SELECT * FROM album ORDER BY album_title ASC ");
$liste_albums["all"] = ALL ;
while ($OBJ_row = $db->fetch_object($result)) {
	$liste_albums[$OBJ_row->album_title] = $OBJ_row->album_title ;
}
echo select_field (CHOOSE_ALBUM, "selected_album_title", "all", $liste_albums) ;
// critère #2 : titre du média
text_field (LAB_MEDIA_TITLE, "title", "", 2) ;

// critère #3 : date du média > entre le ... (date début)
$ARRAY_begin_date = array("begin_yea"=>"180","begin_r"=>"0","begin_month"=>"01","begin_day"=>"01") ;
echo date_field ("begin_date", $ARRAY_begin_date, "", "begin_") ;

// critère #4 : date du média > et le ... (date fin)
$today = date("Y-m-d");
list($set_year, $set_month, $set_day) = explode('-', $today) ;
$set_yea = substr ($set_year, 0, 3) ;
$set_r = substr ($set_year, 3, 1) ;
$ARRAY_end_date = array("end_yea"=>"$set_yea","end_r"=>"$set_r","end_month"=>"$set_month","end_day"=>"$set_day") ;
echo date_field ("end_date", $ARRAY_end_date, "", "end_") ;

// critère #5 : auteur du média
text_field (LAB_MEDIA_AUTHOR, "author",  "", 2) ;
// critère #6 : commentaires du média
text_field (LAB_COMMENT, "comment",  "", 2) ;

// Champs trier par
$sort_by_list = array ("date"=>"Date", "title"=>LAB_MEDIA_TITLE, "author"=>LAB_MEDIA_AUTHOR) ;
echo radio_field (SORT_BY, "sort_by", "title", $sort_by_list) ;

// Champs ordre de sortie
$output_order_list = array ("ASC"=>ASCT, "DESC"=>DSCT) ;
echo (radio_field (OUTPUT_ORDER, "output_order", "ASC", $output_order_list)) ;

end_entete () ;
// bouton de validation
submit_field ("&nbsp;", "validate", CONFIRM_SEARCH) ;
stopform () ;

page_footer($footer_lib) ;
?>