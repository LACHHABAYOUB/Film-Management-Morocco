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

require_once("install/detect.php");
header( 'content-type: text/html; charset=utf-8' );
require_once("drive.php");
// Contrôle de la session et acquisition des infos utilisateur
$WHO_is = access_control ($db, session_id(), $_POST) ;
$USER = $WHO_is['session_login'] ;
$RIGHTS = $WHO_is['session_type'] ;
$FILTER = $WHO_is['request_limit'] ;
$left_lib = $WHO_is['left_lib'] ;
$header_lib = $WHO_is['header_lib'] ;
$footer_lib = $WHO_is['footer_lib'] ;
// Acquisition de la tâche a effectuer
if (!isset ($_GET['location'])) $location = "media" ;
else $location = $_GET['location'] ;
if (!isset ($_GET['action'])) $action = "none" ;
else $action = $_GET['action'] ;
if ($action != "none ") {
	$to_do = scriptorun ($location, $action) ;
	if (!$to_do) error_page (ERR_INVALID, "media", "path_a_retirer", $footer_lib, $db ) ;
} else {
	$to_do = "media/home.php" ;
}
// Appel du script concerné
require ($to_do);
?>