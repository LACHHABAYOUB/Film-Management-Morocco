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

// autorisation d'accès au script
if (($RIGHTS != "administrator") and ($RIGHTS != "private")) error_page (ERR_ACCESS, "media", $RIGHTS, $footer_lib, $db ) ;

// acquisition de la phase de création
if (isset ($_GET['phase'])) $phase = $_GET['phase'] ;
else $phase = "option_album" ;

page_header ($USER, 2, $db) ;
echo make_left_column ("media", $RIGHTS, $left_lib) ;

switch ($phase) {
	case "option_album" :
		// déclaration de l'album par défaut et initialisation erreur
		$preset_albumid = 1 ;
		$error_album_select = "" ;
		echo "\t\t\t<h1>".LIBM_NEW_MEDIA."</h1>\r\n" ;
		// appel du formulaire 1 pour choisir l'album OU en créer un nouveau
		choose_album_form ($preset_albumid, $error_album_select, $db) ;
	break ;
	case "choose_album" ;
		// réception du nom de l'album choisi OU de la demande de création d'un nouvel album (check)
		// contrôle de saisie
		$error = choose_album_control ($_POST) ;
		if ($error != "") {
			echo "\t\t\t<h1>".LIBM_NEW_MEDIA."</h1>\r\n" ;
			// réaffichage du formulaire 1 avec message d'erreur en rouge
			choose_album_form (1, $error, $db) ;
		} else {
			if (isset ($_POST['create'])) {
				$creation = true ;
				$ARRAY_album = array("album_title"=>"","album_author"=>"","album_comment"=>"","album_visibility"=>"public") ;
				$error_album_title = "" ;
			} else {
				$creation = false ;
				$ARRAY_album['selected_album_title'] = $_POST['selected_album_title'] ;
				$error_album_title = "" ;
			}
			echo "\t\t\t<h1>".LIBM_NEW_MEDIA."</h1>\r\n" ;
			// affichage du formulaire 2 avec possibilité de création du nouvel album si demandé
			create_album_form ($ARRAY_album, $error_album_title, $creation, $db) ;
		}
	break ;
	case "write_album" ;
		// réception du titre de l'album qui a été sélectionné OU du tableau pour créer un nouvel album
		if ($_POST['new_album'] === "affirmatif") {
			// contrôle de saisie
			$error = create_album_control ($_POST['album_title']) ;
			if ($error != "") {
				echo "\t\t\t<h1>".LIBM_NEW_MEDIA."</h1>\r\n" ;
				// réaffichage du formulaire 2 avec message d'erreur en rouge
				create_album_form ($_POST, $error, true, $db) ;
				break ;
			} else {
				// création du nouvel album
				$retour = create_album ($_POST, $db, $USER) ;
				$albumid = $retour[0] ;
				$feedback = $retour[1] ;
				echo "\t\t\t<h1>".LIBM_NEW_MEDIA."</h1>\r\n" ;
				// affichage du message de feedback
				display_in_box ($feedback, "") ;
			}
		} else {
			// récupération de l'identifiant de l'album qui a été sélectionné
			$OBJ_album = search_row_by_string ("album", "album_title", $_POST['selected_album_title'], $db) ;
			$albumid = $OBJ_album->album_id ;
		}
		echo "\t\t\t<h1>".LIBM_NEW_MEDIA."</h1>\r\n" ;
		// affichage du formulaire 3 d'upload
		media_upload_form ($albumid, "", $db) ;
	break ;
	case "add_media" ;
		// acquisition de l'id de l'album appelant
		if (isset ($_GET['albumid'])) $albumid = $_GET['albumid'] ;
		else break ;
		echo "\t\t\t<h1>".LIBM_NEW_MEDIA."</h1>\r\n" ;
		// affichage du formulaire 3 d'upload
		media_upload_form ($albumid, "", $db) ;
	break ;
	case "upload_media" ;
		// réception de la resource media
		// contrôle de la saisie
		$error = media_upload_control ($_FILES['resource']) ;
		if ($error != "") {
			echo "\t\t\t<h1>".LIBM_NEW_MEDIA."</h1>\r\n" ;
			// réaffichage du formulaire avec message d'erreur
			media_upload_form ($_POST['albumid'], $error, $db) ;
			break ;
		} else {
			// appel de la fonction d'upload
			$retour = media_upload ($_POST['albumid'],  $_FILES['resource'], $db, $USER) ;
			echo "\t\t\t<h1>".LIBM_NEW_MEDIA."</h1>\r\n" ;
			// affichage du message de feedback avec miniature du media chargé
			display_in_box ($retour[0], $retour[1]) ;
			$mid = $retour[2] ;
		}
		// affichage du formulaire 4 pour commenter le dernier media chargé
		// initialisations
		$ARRAY_media = search_row_by_id ("media", "mid", $mid, $db, TABLEAU);
		$title_error = "" ;
		media_comment_form ($ARRAY_media, $title_error, $db) ;
	break ;
	case "comment_media" ;
		// réception des commentaires pour le dernier media chargé
		// contrôle de saisie
		$error = media_comment_control ($_POST) ;
		if ($error != "") {
			// réaffichage du média et du formulaire avec message d'erreur
			$ARRAY_media = search_row_by_string ("media", "media_name", $_POST['media_name'], $db, TABLEAU);
			$message = INFO_UPLOAD_1.$_POST['original_name'].INFO_UPLOAD_2 ;
			$directory = UPLDP.generate_album_dir_name ($_POST['albumid']) ;
			$info = make_message_image ($ARRAY_media, $directory, $message) ;
			echo "\t\t\t<h1>".LIBM_NEW_MEDIA."</h1>\r\n" ;
			display_in_box ("", "", $info) ;
			media_comment_form ($_POST, $error, $db) ;
			break ;
		} else {
			// enregistrement des commentaires dans la bdd
			$retour = media_comment ($_POST, $db) ;
			$albumid  = $retour[1] ;
			echo "\t\t\t<h1>".LIBM_NEW_MEDIA.fill_it(50,10)."<a href=\"index.php?location=media&amp;action=display_album&amp;albumid=".$albumid."\" class=\"blue_button\" title=\"".END_UPLOAD."\">".END_UPLOAD."</a></h1>\r\n" ;
			// Affichage du message de feedback
			display_in_box ($retour[2], $retour[3]) ;
		}
		// saisie suivante
		// affichage du formulaire 3 d'upload
		media_upload_form ($albumid, "", $db) ;
	break ;
}
page_footer($footer_lib) ;

function choose_album_form ($preset_albumid, $error_album_select, $db) {
	// début du formulaire 1
	startform ("set_album", "index.php?location=media&amp;action=add_media&amp;phase=choose_album", ALBUM_OPTION) ;
	// recherche de la liste des albums
	$result = $db->query("SELECT * FROM album ORDER BY album_title ASC ");
//	$liste_albums["none"] = " " ;
	while ($OBJ_row = $db->fetch_object($result)) {
		$liste_albums[$OBJ_row->album_title] = $OBJ_row->album_title ;
	}
	// champs select pour sélectionner un album
	echo select_field (CHOOSE_EXISTING_ALBUM, "selected_album_title", "", $liste_albums, $error_album_select, 3) ;
	echo "\t\t\t\t<p>".OU."</p>\r\n" ;
	// champs checkbox pour créer un nouvel album
	echo "\t\t\t\t<p>\r\n" ;
		echo "\t\t\t\t\t<label for=\"check_field\">".NOTCH_HERE."</label>\r\n" ;
		echo "\t\t\t\t\t<input type=\"checkbox\" name=\"create\" value=\"create\" /> ".NEW_ALBUM_ASKED."\r\n" ;
	echo "\t\t\t\t</p>\r\n" ;
	// bouton de validation
	end_entete () ;
	submit_field ("&nbsp;", "validate", CONFIRM_YOUR_CHOICE) ;
	stopform () ;
}
function choose_album_control ($input) {
	$error_album_select = "" ;
	if (isset ($input['selected_album_title'])) $select_album = true ;
	else $select_album = false ;
	if (($select_album == true) and (isset ($input['create']))) $error_album_select .= "Sélectionnez ou cochez, mais pas les deux" ;
	if (($select_album == false) and (!isset ($input['create']))) $error_album_select .= "Quel est votre choix ?" ;
	return $error_album_select ;
}

function create_album_form ($ARRAY_album, $error_album_title, $creation, $db) {
	// début du formulaire 2
	startform ("new_album", "index.php?location=media&amp;action=add_media&amp;phase=write_album", ALBUM_VALIDATE) ;
	if ($creation === true) {
		hidden_field ("new_album", "affirmatif") ;
		// traitement des caractères spéciaux HTML
		foreach ($ARRAY_album as $clef => $valeur) $ARRAY_album[$clef] = htmlspecialchars(stripslashes($valeur));
		text_field (LAB_ALBUM_NEW, "album_title",  $ARRAY_album['album_title'], 3, $error_album_title) ;
		text_field (LAB_ALBUM_AUTHOR, "album_author",  $ARRAY_album['album_author'], 2) ;
		echo textarea_field (LAB_COMMENT, "album_comment", $ARRAY_album['album_comment']) ;
		// options de visibilité
		$option_list = array ("everyone"=>"Tout le Monde", "public"=>"Membres", "private"=>PRIVE) ;
		echo radio_field (ACCESS_CONTROL, "album_visibility", $ARRAY_album['album_visibility'], $option_list) ;
	} else {
		hidden_field ("new_album", "negatif") ;
		hidden_field ("selected_album_title", $ARRAY_album['selected_album_title']) ;
		// affichage des infos relatives à l'album sélectionné
		$OBJ_album = search_row_by_string ("album", "album_title", $ARRAY_album['selected_album_title'], $db) ;
		lib_field (ALBUM_SELECTED, $OBJ_album->album_title) ;
		lib_field (LAB_ALBUM_AUTHOR, $OBJ_album->album_author) ;
	}
	// bouton de validation
	end_entete () ;
	submit_field ("&nbsp;", "validate", CONFIRM_ALBUM) ;
	stopform () ;
}
function create_album ($ARRAY_album, $db, $USER) {
	if ($proposed_album_title = search_row_by_string ("album", "album_title", $ARRAY_album['album_title'], $db)) {
		// tentative de duplicata du titre de l'album
		display_in_box ("", "<br />".SORY.THE_ALBUM."( <strong><span class=\"jaune\">".$ARRAY_album['album_title']."</span></strong> ) ".ALRDY_EXIST) ;
		// suite tbd
	} else {
		// création du nouvel album
		// prise d'un identifiant
		$id_album = generate_index("constant_album", $db) ;
		// préparation des données avant insertion
		$album_title = $db->real_escape_string ($ARRAY_album['album_title']) ;
		if (isset($ARRAY_album['album_author'])) $album_author = $db->real_escape_string($ARRAY_album['album_author']);
		else $album_author = UNDEFINED ;
		if (isset($ARRAY_album['album_comment'])) $album_comment = $db->real_escape_string($ARRAY_album['album_comment']);
		else $album_comment = "" ;
		$album_keywords = $album_title." " ;
		if ($album_author != UNDEFINED) $album_keywords .= $album_author." " ;
		$album_visibility = $ARRAY_album['album_visibility'] ;
		// champs automatiques
		$album_maked_by = $USER ;
		$album_maked_on = date("U") ;
		$sql = "INSERT INTO `album` (`album_id`, `album_keywords`, `album_title`, `album_author`, `album_comment`, `album_clicks`, `album_visibility`, `album_maked_on`, `album_maked_by`, `album_revised_on`, `album_revised_by`) VALUES ('$id_album', '$album_keywords', '$album_title', '$album_author', '$album_comment', '1', '$album_visibility', '$album_maked_on', '$album_maked_by', '0', '')" ;
		$db->query ($sql) ;
		$albumf = html_convert($album_title) ;
		$feedback = OK_NEW_ALBUM1 . $albumf . OK_NEW_ALBUM2 ;
		// envoi du feedback et du nouvel id
		$retour = array ("0"=>"$id_album","1"=>"$feedback") ;
		return $retour ;
	}
}
function create_album_control ($input) {
	$error_album_title = "" ;
	if (strlen($input) < 6) $error_album_title = NOK_TITLE_LEN ;
	return $error_album_title ;
}

function media_upload_form ($albumid, $error_media_file, $db) {
	// début du formulaire
	startform ("new_media", "index.php?location=media&amp;action=add_media&amp;phase=upload_media", CHOOSE_FILE) ;
	// champs fichier
	file_field (LAB_MEDIA_FILE, "resource", "", $error_media_file, true) ;
	// acquisition et affichage du titre de l' album
	hidden_field ("albumid", $albumid) ;
	$selected_album = get_field_by_id ("album", "album_title", "album_id", $albumid, $db) ;
	lib_field (DESTINATION_ALBUM, $selected_album) ;
	end_entete () ;
	// bouton d'envoi
	submit_field ("&nbsp;", "validate", CONFIRM_UPLOAD) ;
	stopform () ;
}
function media_upload_control ($resource) {
	// initialisation
	$media_file_error = "" ;
	$extension = strtolower(substr(strrchr($resource['name'],"."),1));
	$accepted = array ('mp3', 'gif', 'jpg', 'png', 'flv') ;
	if($extension == 'jpeg') $extension = 'jpg';
	if (($resource['name'] != "") and (!in_array($extension, $accepted))) $media_file_error .= NOK_NEW_EXT." : ".$extension ;
	if ($resource['name'] == "") $media_file_error .= NOK_NEW_FILE ;
	return $media_file_error ;
}
function media_upload ($albumid, $resource, $db, $USER) {
	$feedback = $ok_message = $warning = $original_name = $media_size = $keywords = "" ;
	// Construction du nom [$media_name] pour le nouveau fichier media
	$mid = generate_index("constant_media", $db) ;
	$media_code = generate_media_code ($mid) ;
	if ($resource['error'] === 0) {
		$original_name = $resource['name'] ;
		$media_size = $resource['size'] ;
		$media_extension = strtolower(substr(strrchr($original_name,"."),1));
		if($media_extension == 'jpeg') $media_extension = 'jpg';
		switch ($media_extension) {
			case "mp3": $itemid = 1 ; $media_type = "a." ; break ;
			case "gif": $itemid = 2 ; $media_type = "i." ; break ;
			case "jpg": $itemid = 3 ; $media_type = "i." ; break ;
			case "png": $itemid = 4 ; $media_type = "i." ; break ;
			case "flv": $itemid = 5 ; $media_type = "v." ; break ;
		}
		$media_name = $media_type.$media_code.".".$media_extension ;
	} else {
		// Affichage de l'erreur
		// tbd
		page_footer("ERROR", "") ;
	}
	$server_dir = UPLDP.generate_album_dir_name ($albumid) ;
	if (!is_dir($server_dir)) mkdir($server_dir, 0777) ;
	$retour = upload_media ($resource, $server_dir, $media_name, $media_size, $original_name) ;
	$ok_message .= $retour[0] ;
	$warning .= $retour[1] ;
	if (($itemid > 1) and ($itemid < 5)) {
		// fabrication de la miniature
		$media_image = $server_dir."/".$media_name ;
		$thumb_dir = $server_dir."/thumbnail/" ;
		if (!is_dir($thumb_dir)) mkdir($thumb_dir, 0777) ;
		$thumb_img = make_thumbnail ($media_image, $thumb_dir, $media_code) ;
		$thumbnail_picture = "<img src=\"".$thumb_img."\" alt=\"thumbnail\" />" ;
	} elseif ($itemid == 1) {
		$thumbnail_picture = "<img src=\"".UPLDP."generic/audio_media.png\" width=\"160\" height=\"120\" alt=\"audio\" />" ;
	} elseif ($itemid == 5) $thumbnail_picture = "<img src=\"".UPLDP."generic/video_media.png\" width=\"160\" height=\"120\" alt=\"video\" />" ;
	// tableau de feedback avec message + thumbnail ou logo[audio ou video]
	$feedback .= "\t\t\t\t\t<table><tr>\r\n" ;
	$feedback .= "\t\t\t\t\t\t<td>".$ok_message."<br />".fill_it(230,1)."</td>\r\n" ;
	$feedback .= "\t\t\t\t\t\t<td>".$thumbnail_picture."</td>\r\n" ;
	$feedback .= "\t\t\t\t\t</tr></table>\r\n" ;
	// construction des champs à enregistrer dans la bdd
	$keywords .= $original_name." " ;
	$maked_by = $USER ;
	$maked_on = date("U") ;
	// requête d'insertion du media dans la bdd
	$sql_insert_media = "INSERT INTO `media` (`mid`, `itemid`, `albumid`, `keywords`, `title`, `author`, `url`, `comment`, `media_name`, `original_name`, `event_date`, `media_size`, `clicks`, `downloads`, `maked_on`, `maked_by`) VALUES ('$mid', '$itemid', '$albumid', '$keywords', '', '', '', '', '$media_name', '$original_name', '', '$media_size', '1', '1', '$maked_on', '$maked_by');" ;
	$db->query ($sql_insert_media);
	// Requete d'insertion des infos exif pour les jpeg
	if ($media_extension == "jpg") {
		$TBL_exif = get_exif ($resource) ;
		if ($TBL_exif['exif'] == 1) {
			$exif_date = $TBL_exif['exif_date'] ;
			$exif_manufacturer = $TBL_exif['exif_manufacturer'] ;
			$exif_model = $TBL_exif['exif_model'] ;
			$exif_exposure = $TBL_exif['exif_exposure'] ;
			$exif_fnumber = $TBL_exif['exif_fnumber'] ;
			$exif_iso = $TBL_exif['exif_iso'] ;
			$exif_aperture = $TBL_exif['exif_aperture'] ;
			$exif_light = $TBL_exif['exif_light'] ;
			$exif_focal = $TBL_exif['exif_focal'] ;
			$sql_insert_exif = "INSERT INTO `exif` (`exif_id`, `exif_date`, `exif_manufacturer`, `exif_model`, `exif_exposure`, `exif_fnumber`, `exif_iso`, `exif_aperture`, `exif_light`, `exif_focal`) VALUES ('$mid', '$exif_date', '$exif_manufacturer', '$exif_model', '$exif_exposure', '$exif_fnumber', '$exif_iso', '$exif_aperture', '$exif_light', '$exif_focal');" ;
			$db->query ($sql_insert_exif);
			// recopie de la date exif
			if (!$exif_date) $event_date = "1515-02-23" ;
			else $event_date = substr ($exif_date, 0, 10) ;
			$sql_date = "UPDATE `media` SET `event_date` = '$event_date' WHERE `media`.`mid` = '$mid' LIMIT 1 ;" ;
			$db->query ($sql_date) ;
		}
	}
	$retour = array ("0"=>"$feedback","1"=>"$warning","2"=>"$mid") ;
	return $retour ;
}
function upload_media ($resource, $server_dir, $media_name, $media_size, $original_name) {
	$feedback = $warning = "" ;
	$size = number_format($media_size/1000, 3, ',', ' ');
	$ok_message = OK_UPLOAD_1.$original_name.OK_UPLOAD_2.$size ;
	$media_extension = strtolower(substr(strrchr($resource['name'],"."),1));
	$destination = $server_dir."/".$media_name ;
	if (($media_extension == "mp3") or ($media_extension == "flv")) {
		if (is_uploaded_file($resource['tmp_name'])) {
			if (copy($resource['tmp_name'], $destination)) $feedback = $ok_message ;
			else $warning = "no-good copy" ;
		} else $warning = "no-good is_uploaded_file" ;
	} else {
		if (is_uploaded_file($resource['tmp_name'])) {
			// Redimensionnement conditionnel
			$final_resource = $resource ;		//temporaire
//			$final_resource = image_resize ($resource, $max_width, $max_height) ;
			if (copy($final_resource['tmp_name'], $destination)) $feedback = $ok_message ;
			else $warning = "no-good copy" ;
		} else $warning = "no-good is_uploaded_file" ;
	}
	$retour = array ("0"=>"$feedback","1"=>"$warning") ;
	return $retour ;
}

function media_comment_form ($ARRAY_media, $title_error, $db) {
	// traitement des caractères spéciaux HTML avant affichage
	foreach ($ARRAY_media as $clef => $valeur) $ARRAY_media[$clef] = htmlspecialchars(stripslashes($valeur));
	// début du formulaire
	startform ("comment_media", "index.php?location=media&amp;action=add_media&amp;phase=comment_media", COMMENT_FILE.$ARRAY_media['original_name']) ;
	hidden_field ("mid", $ARRAY_media['mid']) ;
	hidden_field ("keywords", $ARRAY_media['keywords']) ;
	// acquisition et affichage du titre de l' album
	$albumid = $ARRAY_media['albumid'] ;
	hidden_field ("albumid", $albumid) ;
	hidden_field ("media_name", $ARRAY_media['media_name']) ;
	hidden_field ("original_name", $ARRAY_media['original_name']) ;
	$selected_album = get_field_by_id ("album", "album_title", "album_id", $albumid, $db) ;
	lib_field (DESTINATION_ALBUM, $selected_album) ;
	// 3 champs à remplir (titre obligatoire)
	text_field (LAB_MEDIA_TITLE, "title",  $ARRAY_media['title'], 2, $title_error) ;
	text_field (LAB_MEDIA_AUTHOR, "author",  $ARRAY_media['author'], 2) ;
	echo textarea_field (LAB_COMMENT, "comment", $ARRAY_media['comment']) ;
	end_entete () ;
	// bouton d'envoi
	submit_field ("&nbsp;", "validate", CONFIRM_COMMENT) ;
	stopform () ;
}
function media_comment_control ($input) {
	// initialisation
	$title_error = "" ;
	if (strlen ($input['title']) < 6) $title_error = NOK_TITLE_LEN ;
	return $title_error ;
}
function media_comment ($ARRAY_media, $db) {
	$feedback = $warning = "" ;
	// préparation des données avant insertion ($ARRAY_media)
	$mid = $ARRAY_media['mid'] ;
	$albumid = $ARRAY_media['albumid'] ;
	$title = $db->real_escape_string($ARRAY_media['title']);
	if (isset($ARRAY_media['author'])) $author = $db->real_escape_string($ARRAY_media['author']);
	else $author = UNDEFINED ;
	if (isset($ARRAY_media['comment'])) $comment = $db->real_escape_string($ARRAY_media['comment']);
	else $comment = "" ;
	$keywords = $db->real_escape_string($ARRAY_media['keywords']);
	$keywords .= " ".$title." " ;
	if ($author != UNDEFINED) $keywords .= $author." " ;
	// requête d'enregistrement des infos saisies
	$sql_comment = "UPDATE `media` SET `keywords` = '$keywords', `title` = '$title', `author` = '$author', `comment` = '$comment' WHERE `media`.`mid` = '$mid' LIMIT 1 ;" ;
	$db->query ($sql_comment) ;
	if (mysql_affected_rows() == 1) {
		$message = OK_COMMENT_1.$title."<br />".$author.OK_COMMENT_2 ;
		$directory = UPLDP.generate_album_dir_name ($albumid) ;
		$feedback = make_message_image ($ARRAY_media, $directory, $message) ;
	} else $warning = NO_UPDATE_DONE ;
	$retour = array ("0"=>"$mid","1"=>"$albumid","2"=>"$feedback","3"=>"$warning") ;
	return $retour ; 
}
function make_message_image ($ARRAY_media, $directory, $message) {
	$media_name = $ARRAY_media['media_name'] ;
	list ($type, $code, $ext) = explode (".", $media_name) ;
	$thumbnail = $directory."/thumbnail/min".$media_name ;
	if ($type == "a") $image = "<img src=\"".UPLDP."generic/audio_media.png\" width=\"160\" height=\"120\" alt=\"audio\" />" ;
	elseif ($type == "v") $image = "<img src=\"".UPLDP."generic/video_media.png\" width=\"160\" height=\"120\" alt=\"video\" />" ;
	else $image = $image = "<img src=\"".$thumbnail."\" alt=\"thumbnail\" />" ;
	// tableau de feedback avec message + thumbnail ou logo[audio ou video]
	$feedback = "\t\t\t\t\t<table><tr>\r\n" ;
	$feedback .= "\t\t\t\t\t\t<td>".$message."<br />".fill_it(230,1)."</td>\r\n" ;
	$feedback .= "\t\t\t\t\t\t<td>".$image."</td>\r\n" ;
	$feedback .= "\t\t\t\t\t</tr></table>\r\n" ;
	return $feedback ;
}
?>