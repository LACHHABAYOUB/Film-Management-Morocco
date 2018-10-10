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

require_once("admin/admin.php") ;

page_header ($USER, 2, $db) ;
echo make_left_column ("media", $RIGHTS, $left_lib) ;

echo "\t\t\t<h1>" . LIBM_REGISTER . "</h1>\r\n" ;

$ERROR_account = account_control ($_POST, $db) ;

// enregistrement des données saisies dans le formulaire
if (($ERROR_account['user_email'] != "") or ($ERROR_account['user_login'] != "")) {
	// réaffichage du formulaire avec les valeurs saisies + les erreurs en rouge
	register_form ($_POST['option'], $_POST, $ERROR_account, $db) ;
} else {
	// appel de la fonction d'écriture
	$retour = account_write ($_POST, $_POST['option'], $db) ;
	// affichage du message de feedback
	$feedback = $retour[0] ;
	$warning = $retour[1] ;
	$new_user = $retour[2] ;
	display_in_box ($feedback, $warning) ;
	// recherche de l'enregistrement qui vient d'etre créé ou modifié
	$ARRAY_record = search_user ($new_user, $db, TABLEAU) ;
	// ré-affichage du formulaire avec les valeurs insérées
	register_form ("view", $ARRAY_record, "", $db) ;
}

page_footer($footer_lib) ;
?>