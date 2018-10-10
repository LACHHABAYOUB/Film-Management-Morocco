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
page_header ($USER, 2, $db) ;
echo make_left_column ("media", $RIGHTS, $left_lib) ;

echo "\t\t\t<h1>" . LIBM_SETTINGS . "</h1>\r\n" ;

// Acquisition des paramètres utilisateur courants
$ARRAY_settings = search_row_by_string ("settings", "param_login", $USER, $db, TABLEAU) ;
if (!is_array($ARRAY_settings)) error_page (ERR_UNDEFINED, "media", $RIGHTS, $footer_lib, $db ) ;

// acquisition de la résolution d'écran
?>
<script language="javascript">
if (window.location.search == "?location=settings&action=preference") {
	window.location.href = window.location + "&width=" + screen.width + "&height=" + screen.height;
}
</script>
<?php
if (isset ($_GET['width'])) $screen_width = $_GET['width'] ;
else $screen_width = 800 ;
if (isset ($_GET['height'])) $screen_height = $_GET['height'] ;
else $screen_height = 600 ;
// pour certaines tablettes ou i-phone : permutation largeur <> hauteur
if (($screen_width != 0) and ($screen_height != 0)) {
	if ($screen_width < $screen_height) {
		$screen_width = $_GET['height'] ;
		$screen_height = $_GET['width'] ;
	}
}

if (!isset($_GET['status'])) {
	// affiche le formulaire pour la saisie
	$ARRAY_preset = $ARRAY_settings ;
	$ARRAY_preset['param_screen_width'] = $screen_width ;
	$ARRAY_preset['param_screen_height'] = $screen_height ;
	settings_form ($ARRAY_preset, "one", $db) ;
} elseif ($_GET['status'] == "submit") {
	$feedback = $warning = "" ;
	$retour = settings_write ($_POST, $USER, $db) ;
	$feedback = $retour[0] ;
	$warning = $retour[1] ;
	display_in_box ($feedback, $warning) ;
}

page_footer($footer_lib) ;

function settings_form ($ARRAY_settings, $step, $db) {
	startform ("edit_settings", "index.php?location=settings&amp;action=preference&amp;status=submit", "Ebauche seulement") ;
	lib_field ("Largeur d'écran détectée", $ARRAY_settings['param_screen_width']) ;
	lib_field ("Hauteur d'écran détectée", $ARRAY_settings['param_screen_height']) ;
	text_field ("JPEG : Largeur max", "param_max_width",  $ARRAY_settings['param_max_width'], 1) ;
	text_field ("JPEG : Hauteur max", "param_max_height",  $ARRAY_settings['param_max_height'], 1) ;
	end_entete () ;
	if ($step == "one") submit_field ("&nbsp;", "validate", " Enregistrer ") ;
	stopform () ;
}

function settings_write ($ARRAY_settings, $USER, $db) {
	$feedback = $warning = "" ;
	$param_screen_width = $ARRAY_settings['param_max_width'] ;
	$param_screen_height = $ARRAY_settings['param_max_height'] ;
	$settings_update = "UPDATE `settings` SET `param_screen_width` = '$param_screen_width', `param_screen_height` = '$param_screen_height' WHERE `settings`.`param_login` = '$USER';" ;
	if (!$db->query ($settings_update)) $warning .= "Pb exécution requète" ;
	else $feedback = "Vos préférences ont bien été enregistrées." ;
	$retour = array ("0"=>"$feedback", "1"=>"$warning") ;
	return $retour ;
}


























?>