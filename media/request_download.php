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

// connexion à la base de données
require_once ("../localconf.php");
require_once ("../class/db.class.php");
// connexion à la base de données et instanciation de l'objet $db
$db = new ceckdb (DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NOUN) ;

// acquisition du mid
if (isset ($_GET['mid'])) $mid = $_GET['mid'] ;
else exit ;

// recherche du media correspondant
$request = "SELECT * FROM `media` WHERE `media`.`mid` = '$mid' LIMIT 1 ;" ;
$result = $db->query($request) ;
$ARRAY_media = $db->fetch_assoc($result) ;
if (!is_array($ARRAY_media)) exit ;
$album_id = $ARRAY_media['albumid'] ;
$media_name = $ARRAY_media['media_name'] ;

// préparation des infos pour le téléchargement
$directory = "../".UPLDP.generate_album_dir_name($album_id) ;
$local_file = $directory."/".$media_name ;
$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz" ;
$random_name = rand_string($chars, 16) ;
list ($itm, $cod, $media_extension) = explode (".", $media_name) ;
$downloaded_file = $random_name.".".$media_extension ;

// invocation de la fonction de téléchargement
if (!download_the_file ($local_file, $downloaded_file)) exit ;
else {
	// incrémentation du compteur de téléchargement du média
	$incremente = "UPDATE `media` SET `downloads` = `downloads` + 1 WHERE `media`.`mid` = '$mid' LIMIT 1 ;" ;
	$db->query ($incremente);
}

function download_the_file ($local_file, $downloaded_file) {
	// Must be fresh start
	if(headers_sent()) return false ;
	// Required for some browsers
	if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off');
	// File Exists ?
	if(file_exists($local_file) and is_file($local_file)) {
		// Parse Info / Get Extension
		$fsize = filesize($local_file);
		$path_parts = pathinfo($local_file);
		$ext = strtolower($path_parts["extension"]);   
		// Determine Content Type
		switch ($ext) {
			case "pdf": $ctype="application/pdf"; break;
			case "exe": $ctype="application/octet-stream"; break;
			case "zip": $ctype="application/zip"; break;
			case "doc": $ctype="application/msword"; break;
			case "xls": $ctype="application/vnd.ms-excel"; break;
			case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
			case "gif": $ctype="image/gif"; break;
			case "png": $ctype="image/png"; break;
			case "jpeg":
			case "jpg": $ctype="image/jpg"; break;
			default: $ctype="application/force-download";
		}
		header("Pragma: public"); // required
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false); // required for certain browsers
		header("Content-Type: $ctype");
		header("Content-Disposition: attachment; filename=\"".$downloaded_file."\";" );
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".$fsize);
		ob_clean();
		flush();
		readfile( $local_file );
		return true ;
	} else return false ;
}
function rand_string($chars, $len) {
	$string = '';
	for ($i = 0; $i < $len; $i++)	{
		$pos = rand(0, strlen($chars)-1);
		$string .= $chars{$pos};
	}
   return $string;
}
// génération du nom de dossier pour l'album
function generate_album_dir_name ($index) {
	if (strlen($index) == 1) $code = "0000".$index ;
	elseif (strlen($index) == 2) $code = "000".$index ;
	elseif (strlen($index) == 3) $code = "00".$index ;
	elseif (strlen($index) == 4) $code = "0".$index ;
	elseif (strlen($index) == 5) $code = $index ;
	else return "albXXXXX" ;
	return "alb".$code ;
}
?>