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

// Acquisition mid à afficher
if (isset ($_GET['mid'])) $mid = $_GET['mid'] ;
else error_page (ERR_UNDEFINED, "media", $RIGHTS, $footer_lib, $db ) ;
// Recherche media à afficher
$ARRAY_media = search_row_by_id ("media", "mid", $mid, $db, TABLEAU);
if (is_array($ARRAY_media)) foreach ($ARRAY_media as $cle => $valeur) $ARRAY_media[$cle] = html_convert($valeur);
// Si media inexistant on arrete la
else error_page (ERR_NO_PAGE, "media", $RIGHTS, $footer_lib, $db ) ;

// acquisition du type de media et du dossier de l'album
$itemid = $ARRAY_media["itemid"] ;
$albumid = $ARRAY_media["albumid"] ;
$album_name = generate_album_dir_name($albumid) ;
$media_name = $ARRAY_media["media_name"] ;
list ($type, $media_code, $media_extension) = explode (".", $media_name) ;

// Incrémentation du champs clicks du media
increment_click ($mid, "media", 3, $db) ;

// tableau contenant les actions possibles suivant le média affiché
$tools_table = "\t\t\t<table id=\"toolsTable\">\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>\r\n" ;
		// navigation
list ($first_mid, $previous_mid, $next_mid, $last_mid) = explode ("|", navigate_through_album ($album_name, $media_name)) ;
$tools_table .= tool_unit ("action_first.png", "play_media&amp;mid=".$first_mid, NAV_FIRST, 22) ;
if ($previous_mid != "none") $tools_table .= tool_unit ("action_previous.png", "play_media&amp;mid=".$previous_mid, NAV_PREVIOUS, 44) ;
else $tools_table .= fill_it(88,24) ;
if ($next_mid != "none") $tools_table .= tool_unit ("action_next.png", "play_media&amp;mid=".$next_mid, NAV_NEXT, 22) ;
else $tools_table .= fill_it(66,24) ;
$tools_table .= tool_unit ("action_last.png", "play_media&amp;mid=".$last_mid, NAV_LAST, 44) ;

		// actions communes à tous les médias : éditer, déplacer, supprimer, télécharger
if (($RIGHTS === "administrator") or ($RIGHTS === "private")) {
	$tools_table .= popup_tool_unit ("action_edit.png", EDIT_MEDIA, "edit_popup", "edit_request&amp;mid=", $mid) ;
	$tools_table .= popup_tool_unit ("action_move.png", MOVE_MEDIA, "edit_popup", "move_request&amp;mid=", $mid) ;
	$tools_table .= popup_tool_unit ("action_delete.png", DELETE_MEDIA, "edit_popup", "delete_request&amp;mid=", $mid) ;
} else $tools_table .= fill_it(132,24) ;
if ($RIGHTS != "visitor") $tools_table .= "<a href=\"media/request_download.php?mid=".$mid."\"  title=\"".DOWNLOAD_MEDIA."\"><img src=\"img/actions/action_download.png\" width=\"44\" height=\"24\" alt=\"action_download\" /></a>" ;
else $tools_table .= fill_it(44,24) ;

		// actions réservées aux images : exif, plein-écran, diaporama
if (($itemid == 2) or ($itemid == 3) or ($itemid == 4)) {
	$ARRAY_exif = search_row_by_id ("exif", "exif_id", $mid, $db);
	if (is_object($ARRAY_exif)) $tools_table .= popup_tool_unit ("action_show_exif.png", EXIF_SHOW, "exif_popup", "show_exif&amp;mid=", $mid) ;
	else $tools_table .= fill_it(44,24) ;
	$picture = UPLDP.$album_name."/".$media_name ;
	$max = image_max ($picture, SCREEN_WIDTH, SCREEN_HEIGHT) ;
	$max_width = $max[1] + 8 ;
	$max_height = $max[2] + 4 ;
	$tools_table .= popup_tool_unit ("action_full_screen.png", FULL_SCREEN, "poster", "poster&amp;mid=", $mid, $max_width, $max_height) ;
	$tools_table .= tool_unit ("action_slideshow.png", "slideshow&amp;albumid=".$albumid, SLIDESHOW) ;
} else {
	$tools_table .= fill_it(137,24) ;
}
		// retour à l'album | ajouter un media
$tools_table .= tool_unit ("action_show_album.png", "display_album&amp;albumid=".$albumid, BACK_TO_ALBUM) ;
if (($RIGHTS === "administrator") or ($RIGHTS === "private")) $tools_table .= tool_unit ("action_add.png", "add_media&amp;phase=add_media&amp;albumid=".$albumid, ADD_MEDIA_IN_ALBUM) ;
// fin du tableau d'entête
$tools_table .= "\t\t\t\t\t</td>\r\n\t\t\t\t</tr>\r\n\t\t\t</table>\r\n" ;

// tableau contenant le média proprement dit
$display_table = "\t\t\t<table id=\"mediaTable\">\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>\r\n" ;
switch ($itemid) {
	case "1":
		$output_audio = UPLDP.$album_name."/".$media_name ;
		$display_table .= audio_play ($output_audio) ;
	break ;
	case "2":
	case "3":
	case "4":
		// Redimensionnement de l'image
		$max_picture_width = 740 ;
		$max_picture_height = 555 ;
		$max = image_max ($picture, $max_picture_width, $max_picture_height) ;
		$output_picture = $max[0] ;
		// affichage de l'image
		$display_table .= $output_picture ;
	break ;
	case "5":
		$output_video = UPLDP.$album_name."/".$media_name ;
		$display_table .= video_play($output_video) ;
	break ;
}
$display_table .= "\t\t\t\t\t</td>\r\n\t\t\t\t</tr>\r\n\t\t\t</table>\r\n" ;

// début de page
if ($itemid == 1) $plugin = "audio" ;
elseif ($itemid == 5) $plugin = "video" ;
else $plugin = "none" ;
page_header ($USER, 1, $db, $plugin) ;

// affichage des tableaux ci-dessus
echo $tools_table ;
echo $display_table ;

// construction et affichage du titre
$album_title = "<span class=\"jaune\">".get_field_by_id ("album", "album_title", "album_id", $albumid, $db)."</span>" ;
$space = str_repeat("&nbsp;", 8);
echo "\t\t\t<h4>".$album_title.$space.$ARRAY_media['title']." <span class=\"gris_moyen\">[ ".cut_string ($ARRAY_media['original_name'], 20)." ]</span> </h4>\r\n" ;

// fin de la page
page_footer($footer_lib) ;

function popup_tool_unit ($icon, $title, $js_popup, $url, $id, $width=0, $height=0, $class="") {
	list ($ico, $xt) = explode (".", $icon) ;
	if ($class == "") $tag = "<img" ;
	else $tag = "<img class=\"".$class."\"" ;
	$output = $tag." src=\"img/actions/".$icon."\" width=\"44\" height=\"24\" alt=\"".$ico."\" title=\"".$title."\" onclick=\"" ;
	$root = "index.php?location=media&amp;action=" ;
	if (($width == 0) and ($height == 0)) {
		$output .= $js_popup."('".$root.$url."".$id."')\" />" ;
	} else {
		$output .= $js_popup."('".$root.$url."".$id."', '".$width."', '".$height."')\" />" ;
	}
	return $output ;
}

function tool_unit ($image, $url, $title, $space=0, $class="") {
	$output = "" ;
	if ($class == "") $tag = "<img" ;
	else $tag = "<img class=\"".$class."\"" ;
	list ($icon, $xt) = explode (".", $image) ;
	$root = "index.php?location=media&amp;action=" ;
	$output .= "\t\t\t\t\t\t<a href=\"".$root.$url."\"  title=\"".$title."\">" ;
	$output .= $tag." src=\"img/actions/".$image."\" width=\"44\" height=\"24\" alt=\"".$icon."\" />" ;
	$output .= "</a>" ;
	if ($space != 0) $output .= fill_it($space, 24) ;
	$output .= "\r\n" ;
	return $output ;
}

function navigate_through_album ($album_name, $media_name) {
	// Construction tableau des codes des medias de l'album en cours
	$rank = 0 ;
	if ($handle = opendir(UPLDP.$album_name."/")) {
		while (($file = readdir($handle)) !== false) {
			if (($file != ".") and ($file != "..") and (strstr($file, "humb") === FALSE)) {
				list ($t, $code, $x) = explode (".", $file) ;
				$TBLmedia_code[$rank] = $code ;
				$rank++ ;
			}
		}
	}
	$media_qty = $rank ;
	closedir($handle) ;
	// Tri du tableau par code
	sort($TBLmedia_code) ;
	// Recherche de la clé du media courant dans le tableau trié
	list ($type, $current_code, $ext) = explode (".", $media_name) ;
	$current_key = array_search($current_code, $TBLmedia_code) ;
	// Recherche de la clé du media precedent et du mid correspondant
	$previous_key = $current_key - 1 ;
	if ($previous_key >= 0) $previous_mid = $TBLmedia_code[$previous_key] + 0 ;
	else $previous_mid = "none" ;
	// Recherche de la clé du media suivant et du mid correspondant
	$next_key = $current_key + 1 ;
	if ($next_key < $media_qty) $next_mid = $TBLmedia_code[$next_key] + 0 ;
	else $next_mid = "none" ;
	// Envoi des identifiants mid pour la navigation
	$first_mid = $TBLmedia_code[0] + 0 ;
	$last_mid = $TBLmedia_code[($media_qty - 1)] + 0 ;
	return $first_mid."|".$previous_mid."|".$next_mid."|".$last_mid ;
}

function audio_play ($audio) {
	// http://www.alsacreations.fr/dewplayer.html
	$play  = "<object type=\"application/x-shockwave-flash\" data=\"plugin/audio/dewplayer-bubble-vol.swf\" width=\"250\" height=\"70\" id=\"dewplayer\" name=\"dewplayer\">" ;
	$play .= "<param name=\"movie\" value=\"plugin/audio/dewplayer-bubble-vol.swf\" />" ;
	$play .= "<param name=\"flashvars\" value=\"mp3=".$audio."&amp;autostart=1&amp;autoreplay=1&amp;showtime=1\" />" ;
	$play .= "<param name=\"wmode\" value=\"transparent\" />" ;
	$play .= "</object>" ;
	return $play ;
}
function video_play($video) {
	$play = "<div style=\"text-align:center;margin-top:2em;\">" ;
	$play .= "<object type=\"application/x-shockwave-flash\" data=\"dewtube.swf\" width=\"600\" height=\"450\" id=\"dewtube\">" ;
	$play .= "<param name=\"allowFullScreen\" value=\"true\" />" ;
	$play .= "<param name=\"movie\" value=\"dewtube.swf\" />" ;
	$play .= "<param name=\"quality\" value=\"high\" />" ;
	$play .= "<param name=\"bgcolor\" value=\"#000000\" />" ;
	$play .= "<param name=\"flashvars\" value=\"movie=".$video."&amp;width=600&amp;height=450&amp;autostart=1\" />" ;
	$play .= "</object>" ;
	$play .= "</div>" ;
	return $play ;
}
?>