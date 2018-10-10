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
// Acquisition mid = exif_id
if (isset ($_GET['mid'])) $mid = $exif_id = $_GET['mid'] ;
else error_popup (ERR_UNDEFINED) ;
// Recherche image correspondante
$ARRAY_image = search_row_by_id ("media", "mid", $mid, $db, TABLEAU);
if (is_array($ARRAY_image)) foreach ($ARRAY_image as $cle => $valeur) $ARRAY_image[$cle] = html_convert($valeur);
// Si image inexistante on arrete la
else error_popup (ERR_NO_RECORD) ;

// Acquisition infos exif
$ARRAY_exif = search_row_by_id ("exif", "exif_id", $mid, $db, TABLEAU);
if (is_array($ARRAY_exif)) {
	foreach ($ARRAY_exif as $cle => $valeur) $ARRAY_exif[$cle] = html_convert($valeur);
// Si exif inexistant on arrête là
} else error_popup (ERR_NO_EXIF, "") ;

$title = EXIF_DATA ;
$page_head = EXIF_DATA ;

// Construction du tableau de données
$table = "<table id=\"exif\">\r\n" ;
$table .= "\t<tr>\r\n" ;
$table .= "\t\t<th width=\"150\">".EXIF_FIELD."</th>\r\n" ;
$table .= "\t\t<th>".EXIF_VALUE."</th>\r\n" ;
$table .= "\t</tr>\r\n" ;

$table .= make_row (LIB_EXIF_DATE, $ARRAY_exif['exif_date'], "") ;
$table .= make_row (LIB_EXIF_MFR, $ARRAY_exif['exif_manufacturer'], "alt") ;
$table .= make_row (LIB_EXIF_MODEL, $ARRAY_exif['exif_model'], "") ;
$table .= make_row (LIB_EXIF_EXPO, $ARRAY_exif['exif_exposure'], "alt") ;
$table .= make_row (LIB_EXIF_FNR, $ARRAY_exif['exif_fnumber'], "") ;
$table .= make_row (LIB_EXIF_ISO, $ARRAY_exif['exif_iso'], "alt") ;
$table .= make_row (LIB_EXIF_APERTURE, $ARRAY_exif['exif_aperture'], "") ;
$table .= make_row (LIB_EXIF_FLASH, $ARRAY_exif['exif_light'], "alt") ;
$table .= make_row (LIB_EXIF_FOCAL, $ARRAY_exif['exif_focal'], "") ;

$table .= "</table>\r\n" ;

// Début de page en html
popup_header ($title, "look_exif") ;

echo "<div id=\"page_head\">\r\n" ;
echo "\t<p>" . $page_head . "</p>\r\n" ;
echo "</div>\r\n" ;
echo $table ;

// Fin de page html
popup_footer () ;

function make_row ($libelle, $data, $style="") {
	if ($style == "") $row = "\t<tr>\r\n" ;
	else $row = "\t<tr class=\"" . $style . "\">\r\n" ;
	$row .= "\t\t<td>" . $libelle . "</td>\r\n" ;
	$row .= "\t\t<td>" . $data . "</td>\r\n" ;
	return $row . "\t</tr>\r\n" ;
}
?>