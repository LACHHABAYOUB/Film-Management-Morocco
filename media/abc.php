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

function image_max ($resource, $max_width, $max_height) {
	list($resource_width, $resource_height) = getimagesize($resource);
	$resource_ratio = $resource_width / $resource_height ;
	if ($resource_ratio > (4/3)) {
		if (($resource_width <=  $max_width) and ($resource_height <=  $max_height)) {
			$final_width = $resource_width ;
			$final_height = $resource_height ;
		} else {
			$final_width = $max_width ;
			$final_height = round ($resource_height * ($max_width / $resource_width)) ;
		}
	} else {
		if (($resource_width <=  $max_width) and ($resource_height <=  $max_height)) {
			$final_width = $resource_width ;
			$final_height = $resource_height ;
		} else {
			$final_width = round ($resource_width * ($max_height / $resource_height)) ;
			$final_height = $max_height ;
		}
	}
	$output_picture = "<img src=\"".$resource."\" width=\"".$final_width."\" height=\"".$final_height."\" alt=\"poster\" />" ;
	$retour = array ("0"=>"$output_picture","1"=>"$final_width","2"=>"$final_height") ;
	return $retour ;
}

function make_thumbnail ($resource, $thumb_dir, $media_code) {
	// Definition de la largeur et de la hauteur maximale
	$max_thumb_width = 160 ;
	$max_thumb_height = 120 ;
	// Calcul des nouvelles dimensions
	$max = image_max ($resource, $max_thumb_width, $max_thumb_height) ;
	$final_width = $max[1] ;
	$final_height = $max[2] ;
	list($resource_width, $resource_height) = getimagesize($resource);
	$type = strtolower(substr(strrchr($resource,"."),1));
	if($type == 'jpeg') $type = 'jpg';
	// creation image vide
	$thumbmail_img = imagecreatetruecolor($final_width, $final_height) ;
	// preservation de la transparence
	if(($type == "gif") or ($type == "png"))	{
		imagecolortransparent($thumbmail_img, imagecolorallocatealpha($thumbmail_img, 0, 0, 0, 127));
		imagealphablending($thumbmail_img, false);
		imagesavealpha($thumbmail_img, true);
	}
	// creation image source
	switch($type){
		case 'gif': $image = imagecreatefromgif($resource); break;
		case 'jpg': $image = imagecreatefromjpeg($resource); break;			// Fatal error: Allowed memory size of 134217728 bytes exhausted (tried to allocate 32000 bytes)
		case 'png': $image = imagecreatefrompng($resource); break;
	}
	// copie et redimensionnement
	imagecopyresampled($thumbmail_img, $image, 0, 0, 0, 0, $final_width, $final_height, $resource_width, $resource_height) ;
	// Enregistrement de la miniature
	$thumbnail = $thumb_dir."mini.".$media_code.".".$type ;
	switch($type){
		case 'gif': imagegif($thumbmail_img, $thumbnail); break;
		case 'jpg': imagejpeg($thumbmail_img, $thumbnail, 50); break;
		case 'png': imagepng($thumbmail_img, $thumbnail); break;
	}
	return $thumbnail ;
}
// affichage d'une ligne d'occurence pour les médias
function display_media_line ($ARRAY_occurence, $i, $db, $RIGHTS) {
	$mid = $ARRAY_occurence["mid"] ;
	$itemid = $ARRAY_occurence["itemid"] ;
	$albumid = $ARRAY_occurence["albumid"] ;
	$title = $ARRAY_occurence["title"] ;
	// Incrementation du champs clicks du media
	increment_click ($mid, "media", 1, $db);
	// Affichage album associé + lien vers l'album
	if (($i % 2) == 0) $pair = true ;
	else $pair = false ;
	if ($i < 10) $i = "0".$i ;
	$album_folder = generate_album_dir_name($albumid) ;
	$album_title = SEE_ALBUM . get_field_by_id ("album", "album_title", "album_id", $albumid, $db) ;
	$titre_album = cut_string (get_field_by_id ("album", "album_title", "album_id", $albumid, $db), 16) ;
	$b = strlen ($titre_album) ;
	if ($b < 25) $titre_album = $titre_album . str_repeat("&nbsp;", (16 - $b));
	if ($pair === true) $media_line = "\t\t\t<h3 class=\"occurence\">&nbsp;&nbsp;&nbsp;".$i." - " ;
	else $media_line = "\t\t\t<h3 class=\"occurent\">&nbsp;&nbsp;&nbsp;".$i." - " ;
	$media_line .= "<a href=\"index.php?location=media&amp;action=display_album&amp;albumid=".$albumid."\"  title=\"".$album_title."\">".$titre_album."<img class=\"searching\" src=\"img/actions/action_show_album.png\" height=\"24\" width=\"44\" alt=\"display_album\" /></a>" ;
	// Recherche de la photo associée au media
	if ($itemid == 1) {
		$picture = "generic/audio_media.png" ;
	} elseif ($itemid == 5) {
		$picture = "generic/video_media.png" ;
	} else {
		list ($it, $media_code, $media_extension) = explode (".", $ARRAY_occurence["media_name"]) ;
		$picture = $album_folder."/thumbnail/mini.".$media_code.".".$media_extension ;
	}
	$associated_photo = " <img class=\"thumbnail\" src=\"".UPLDP.$picture."\" height=\"32\" alt=\"edit\" />" ;
	if ($itemid == 1) $play_title = PLAY_AUDIO_MEDIA." : ".$title ;
	elseif ($itemid == 5) $play_title = PLAY_VIDEO_MEDIA." : ".$title ;
	else $play_title = LIBM_SHOW_IMAGE." : ".$title ;
	$wording_title = cut_string ($title, 25) ;
	$c = strlen ($wording_title) ;
	if ($c < 25) $wording_title = $wording_title . str_repeat("&nbsp;", (25 - $c));
	$wording = "<div class=\"wording\">" . $wording_title . "</div>" ;
	$media_line .= " <a href=\"index.php?location=media&amp;action=play_media&amp;mid=".$mid."\"  title=\"".$play_title."\">".$wording.$associated_photo."</a>" ;
	$media_line .= fill_it(30,10) ;
	// ajout des actions disponibles sur le média, suivant les droits du visiteur
	$ARYexif = search_row_by_id ("exif", "exif_id", $mid, $db) ;
	if (is_object($ARYexif)) $media_line .= popup_link ("action_show_exif.png", EXIF_SHOW, "exif_popup", "show_exif&amp;mid=", $mid, 0, 0, "searching") ;
	if (($RIGHTS === "administrator") or ($RIGHTS === "private")) {
		$media_line .= popup_link ("action_edit.png", EDIT_MEDIA, "edit_popup", "edit_request&amp;mid=", $mid, 0, 0, "searching") ;
		$media_line .= popup_link ("action_move.png", MOVE_MEDIA, "edit_popup", "move_request&amp;mid=", $mid, 0, 0, "searching") ;
		$media_line .= popup_link ("action_delete.png", DELETE_MEDIA, "edit_popup", "delete_request&amp;mid=", $mid, 0, 0, "searching") ;
	}
	if ($RIGHTS != "visitor") $media_line .= "<a href=\"media/request_download.php?mid=".$mid."\"  title=\"".DOWNLOAD_MEDIA."\"><img src=\"img/actions/action_download.png\" class=\"searching\" width=\"44\" height=\"24\" alt=\"action_download\" /></a>" ;
	// fin de la ligne d'occurence
	$media_line .= "</h3>\r\n" ;
	return $media_line ;
}
// affichage d'une ligne d'occurence pour les albums
function display_album_line ($occurence, $i, $db, $RIGHTS) {
	$albumid = $occurence["album_id"] ;
	$album_title = html_convert($occurence["album_title"]) ;
	// affichage pair/impair et numéro de ligne
	if (($i % 2) == 0) $album_line = "\t\t\t<h3 class=\"occurence\">&nbsp;" ;
	else $album_line = "\t\t\t<h3 class=\"occurent\">&nbsp;" ;
	if ($i < 10) $i = "0".$i ;
	$album_code = generate_album_dir_name($albumid) ;
	if ($album_code == "albXXXXX") return ;
	$album_line .= $i." - " ;
	// affichage du titre de l'album avec lien pour le visualiser
	$titre = cut_string ($album_title, 30) ;
	$c = strlen ($titre) ;
	if ($c < 30) $titre = $titre . str_repeat("&nbsp;", (30 - $c));
	$album_line .= "<a href=\"index.php?location=media&amp;action=display_album&amp;albumid=".$albumid."\"  title=\"".LIBM_DISPLAY_ALBUM."\"><img src=\"img/actions/action_show_album.png\" class=\"searching\" height=\"24\" width=\"44\" alt=\"display_album\" />".$titre." </a>" ;
	// comptage et affichage de la qté de médias contenus dans l'album (par types)
	$sql_compte_audio = $db->query("SELECT mid FROM media WHERE albumid = $albumid AND itemid = 1") ;
	$audio_media_qty = $db->num_rows($sql_compte_audio) ;
	$sql_compte_image = $db->query("SELECT mid FROM media WHERE albumid = $albumid AND itemid > 1 AND itemid < 5") ;
	$image_media_qty = $db->num_rows($sql_compte_image) ;
	$sql_compte_video = $db->query("SELECT mid FROM media WHERE albumid = $albumid AND itemid = 5") ;
	$video_media_qty = $db->num_rows($sql_compte_video) ;
	$media_qty = $audio_media_qty + $image_media_qty + $video_media_qty ;
	$album_line .= "( " ;
	if ($media_qty > 0) $album_line .= $media_qty." medias )" ;
	else $album_line .= "<span class=\"jaune\"> 0 media )</span> " ;
	// recherche et affichage des premier et dernier médias de l'album (priorité aux images)
	if ($image_media_qty > 0) {
		$album_folder = generate_album_dir_name ($albumid) ;
		$directory = UPLDP.$album_folder."/" ;
		$rank = 0 ;
		$TBLmedia_code = "" ;
		if ($handle = @ opendir($directory)) {
			while (($file = readdir($handle)) !== false) {
				if (($file != ".") and ($file != "..") and (stristr($file, "humb") === FALSE)) {
					@ list ($type, $code, $ext) = explode (".", $file) ;
					if ($type === "i") {
						$TBLmedia[$rank] = $file ;
						$TBLmedia_code[$rank] = $code ;
						$rank++ ;
					}
				}
			}
			$media_qty = $rank ;
			closedir($handle) ;
			if (is_array($TBLmedia_code)) sort($TBLmedia_code) ;
		}
		if (is_array($TBLmedia_code)) {
			$first_mid = $TBLmedia_code[0] + 0 ;
			$last_mid = $TBLmedia_code[($media_qty - 1)] + 0 ;
			// premiere et derniere miniatures
			$TBLfirst_media = search_row_by_id ("media", "mid", $first_mid, $db, TABLEAU);
			$first_name = $TBLfirst_media['media_name'] ;
			$first_title = $TBLfirst_media['title'] ;
			list ($type1, $code1, $ext1) = explode (".", $first_name) ;
			
			$TBLlast_media = search_row_by_id ("media", "mid", $last_mid, $db, TABLEAU);
			$last_name = $TBLlast_media['media_name'] ;
			$last_title = $TBLlast_media['title'] ;
			list ($type2, $code2, $ext2) = explode (".", $last_name) ;
			
			if ($first_name != $last_name) {
				$first_thumbnail = $directory."thumbnail/mini.".$code1.".".$ext1 ;
				$album_line .= fill_it(20,10)."<a href=\"index.php?location=media&amp;action=play_media&amp;mid=".$first_mid."\"  title=\"".$first_title."\"><img class=\"thumbnail\" src=\"".$first_thumbnail."\" height=\"32\" alt=\"first_thumbnail\" /></a>" ;
			} else $album_line .= fill_it(64,10) ;
			$last_thumbnail = $directory."thumbnail/mini.".$code2.".".$ext2 ;
			$album_line .= fill_it(20,10)."<a href=\"index.php?location=media&amp;action=play_media&amp;mid=".$last_mid."\"  title=\"".$last_title."\"><img class=\"thumbnail\" src=\"".$last_thumbnail."\" height=\"32\" alt=\"last_thumbnail\" /></a>" ;
		}
	} elseif ($audio_media_qty > 0) {
		$album_line .= fill_it(60,10) ;
		$album_line .= "<img class=\"thumbnail\" src=\"".UPLDP."/generic/audio_media.png\" height=\"32\" alt=\"audio\" />" ;
	} elseif ($video_media_qty > 0) {
		$album_line .= fill_it(60,10) ;
		$album_line .= "<img class=\"thumbnail\" src=\"".UPLDP."/generic/video_media.png\" height=\"32\" alt=\"video\" />" ;
	} else $album_line .= fill_it(100,10) ;
	// action éditer (si les droits le permettent)
	if (($RIGHTS === "administrator") or ($RIGHTS === "private")) {
		$album_line .= fill_it(20,10) ;
		$album_line .= popup_link ("action_edit.png", EDIT_ALBUM, "edit_popup", "album_edit_request&amp;albumid=", $albumid, 0, 0, "searching") ;
		// action supprimer
		$album_line .= fill_it(10,10) ;
		$album_line .= popup_link ("action_delete.png", DELETE_ALBUM, "edit_popup", "album_delete_request&amp;albumid=", $albumid, 0, 0, "searching") ;
	}
	$album_line .= "</h3>\r\n" ;
	return $album_line ;
}
// Generation du code du media
function generate_media_code ($index) {
	if (strlen($index) == 1) $code = "00000".$index ;
	if (strlen($index) == 2) $code = "0000".$index ;
	if (strlen($index) == 3) $code = "000".$index ;
	if (strlen($index) == 4) $code = "00".$index ;
	if (strlen($index) == 5) $code = "0".$index ;
	if (strlen($index) == 6) $code = $index ;
	return $code ;
}
// Generation du nom de dossier pour l'album
function generate_album_dir_name ($index) {
	if (strlen($index) == 1) $code = "0000".$index ;
	elseif (strlen($index) == 2) $code = "000".$index ;
	elseif (strlen($index) == 3) $code = "00".$index ;
	elseif (strlen($index) == 4) $code = "0".$index ;
	elseif (strlen($index) == 5) $code = $index ;
	else return "albXXXXX" ;
	return "alb".$code ;
}
// mise en mémoire cache
function save_in_cache ($value, $field, $sesid, $db) {
	$searched_row = search_from_cache ($sesid, $db) ;
	$sane_value = $db->real_escape_string($value);
	if (is_object($searched_row)) {
		$sql = "UPDATE `cache` SET `$field` = '$sane_value' WHERE `cache_id` = '$sesid';" ;
		$db->query ($sql) ;
	} else {
		$cache_tstamp = date("U") ;
		$sql = "INSERT INTO `cache` (`cache_id`, `$field`, `cache_tstamp`) VALUES ('$sesid', '$sane_value', '$cache_tstamp')" ;
		$db->query ($sql) ;
	}
}
// récupération du cache
function get_from_cache ($field, $sesid, $db) {
	$searched_row = search_from_cache ($sesid, $db) ;
	if (is_object($searched_row)) return $searched_row->$field ;
	else return false ;
}
function search_from_cache ($sesid, $db) {
	$search = "SELECT * FROM `cache` WHERE `cache_id` = '$sesid' LIMIT 1" ;
	$find = $db->query($search) ;
	return $db->fetch_object($find);
}
function get_exif ($resource) {
	$TBLexif = array() ;
	if (function_exists("exif_read_data")) {
		$TBLexif['exif'] = 1 ;
		$exifTBL = exif_read_data ($resource['tmp_name'], 0, true) ;
		// DateTimeOriginal : YYYY:MM:DD HH:MM:SS
		if (!empty($exifTBL['EXIF']['DateTimeOriginal'])) {
			$DateTime = $exifTBL['EXIF']['DateTimeOriginal'] ;
			list ($Dat, $Time) = explode (" ", $DateTime) ;
			$Date = str_replace (":", "-", $Dat);
			$TBLexif['exif_date'] = $Date . " " . $Time ;   // DateTime au format MySQL
		} else $TBLexif['exif_date'] = UNDEFINED ;
		if (!empty($exifTBL['IFD0']['Make'])) $TBLexif['exif_manufacturer'] = $exifTBL['IFD0']['Make'] ;
		else $TBLexif['exif_manufacturer'] = UNDEFINED ;
		if (!empty($exifTBL['IFD0']['Model'])) $TBLexif['exif_model'] = $exifTBL['IFD0']['Model'] ;
		else $TBLexif['exif_model'] = UNDEFINED ;
		if (!empty($exifTBL['EXIF']['ExposureTime'])) $TBLexif['exif_exposure'] = $exifTBL['EXIF']['ExposureTime'] ;
		else $TBLexif['exif_exposure'] = UNDEFINED ;
		if (!empty($exifTBL['EXIF']['FNumber'])) $TBLexif['exif_fnumber'] = $exifTBL['EXIF']['FNumber'] ;
		else $TBLexif['exif_fnumber'] = UNDEFINED ;
		if (!empty($exifTBL['EXIF']['ISOSpeedRatings'])) $TBLexif['exif_iso'] = $exifTBL['EXIF']['ISOSpeedRatings'] ;
		else $TBLexif['exif_iso'] = UNDEFINED ;
		if (!empty($exifTBL['EXIF']['MaxApertureValue'])) $TBLexif['exif_aperture'] = $exifTBL['EXIF']['MaxApertureValue'] ;
		else $TBLexif['exif_aperture'] = UNDEFINED ;
		if (!empty($exifTBL['EXIF']['LightSource'])) $TBLexif['exif_light'] = $exifTBL['EXIF']['LightSource'] ;
		else $TBLexif['exif_light'] = UNDEFINED ;
		if (!empty($exifTBL['EXIF']['FocalLength'])) $TBLexif['exif_focal'] = $exifTBL['EXIF']['FocalLength'] ;
		else $TBLexif['exif_focal'] = UNDEFINED ;
	} else $TBLexif['exif'] = 0 ;
	return $TBLexif ;
}
function icon_link ($image, $url, $title, $clear=0, $class="") {
	$output = "" ;
	if ($clear != 0) $output .= fill_it(44, $clear) ;
	if ($class == "") $tag = "<img" ;
	else $tag = "<img class=\"".$class."\"" ;
	if (strstr($url, "none") !== FALSE) {
//		$output .= $tag." src=\"img/clear.gif\" width=\"44\" height=\"24\" />" ;
		$output .= $tag." ".fill_it(44, 24) ;
		return $output ;
	}
	list ($icon, $xt) = explode (".", $image) ;
	$root = "index.php?location=media&amp;action=" ;
	$output .= "<a href=\"".$root.$url."\"  title=\"".$title."\">" ;
	$output .= $tag." src=\"img/actions/".$image."\" height=\"24\" width=\"44\" alt=\"".$icon."\" />" ;
	$output .= "</a>" ;
	return $output ;
}
function popup_link ($icon, $title, $js_popup, $url, $id, $width=0, $height=0, $class="") {
	list ($ico, $xt) = explode (".", $icon) ;
	if ($class == "") $tag = "<img" ;
	else $tag = "<img class=\"".$class."\"" ;
	$output = $tag." src=\"img/actions/".$icon."\" height=\"24\" width=\"44\" alt=\"".$ico."\" title=\"".$title."\" onclick=\"" ;
	$root = "index.php?location=media&amp;action=" ;
	if (($width == 0) and ($height == 0)) {
		$output .= $js_popup."('".$root.$url."".$id."')\" />" ;
	} else {
		$output .= $js_popup."('".$root.$url."".$id."', '".$width."', '".$height."')\" />" ;
	}
	return $output ;
}
?>