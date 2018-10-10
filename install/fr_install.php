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

define ("LANG", "fr");

define ("BACK_STEP", " Revenir à l'étape précédante ");
define ("BANDEAU", "Installation de GCM - étape : ");

define ("CONFIRM_STEP", " Passer à l'étape suivante ");

define ("ENTETE_1", "Configuration générale et Base de Données");
define ("ENTETE_2_1", "Création du fichier de configuration");
define ("ENTETE_2_2", "Test de connexion à la Base de Données");
define ("ENTETE_3", "Création des tables de la Base de Données");
define ("ENTETE_4", "Collecte des informations pour le compte administrateur");
define ("ENTETE_5_1", "Création du compte administrateur");
define ("ENTETE_5_2", "Fin de l'installation");

define ("ERROR_EMPTY", "Ce champs ne peut pas rester vide");

define ("FORM_ENTETE_1", "Collecte de vos informations");
define ("FORM_ENTETE_2", "N'oubliez pas de noter votre mot de passe en lieu sûr");

define ("INTRO_ONE", "Avant de commencer l'installation, vous devez créer votre base de données MySQL. Pour cela, vous pouvez utiliser phpMyAdmin.<br />N'oubliez pas de sélectionner l'interclassement : utf8_general_ci.");
define ("INTRO_END", "En cliquant sur le bouton ci-dessous, vous serez dirigé vers la page d'acceuil de votre site GCM.<br />Vous devrez vous identifier avec le compte administrateur que vous venez de créer.");

define ("GO_HOME", "Page d'acceuil de votre site");

define ("LABEL_ADMIN_CONFIRM_IT", "Confirmez-le");
define ("LABEL_ADMIN_MAIL", "Adresse mail admin");
define ("LABEL_ADMIN_PASSWORD", "Mot de passe");
define ("LABEL_ADMIN_USERNAME", "Nom utilisateur pour admin");
define ("LABEL_DB_HOST", "Nom du serveur");
define ("LABEL_DB_INFOS", "Paramètres pour la connexion à votre base de données MySQL :");
define ("LABEL_DB_NOUN", "Nom de la base de données");
define ("LABEL_DB_PASSWORD", "Mot de passe");
define ("LABEL_DB_USERNAME", "Nom d'utilisateur");
define ("LABEL_DOMAIN_NAME", "Nom de votre domaine");
define ("LABEL_SITE_NAME", "Nom de votre site");
define ("LABEL_SITE_NAME_INFO", "Le nom de votre site s'affichera dans la barre d'entête de votre navigateur.");

define ("LOREM_IPSUM", "Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan.");

define ("NOK_ADMIN_LEN", "5 caractères minimum ! ");
define ("NOK_CHAR", "Caractères autorisés : a-z A-Z 0-9 _ @ : ] [ : (exclusivement)<br />");
define ("NOK_DB_CONNECTION", "La connexion à votre base de données a échoué.<br />Vous devez vérifier les paramètres que vous avez saisis dans l'étape précédante et ré-essayer.");
define ("NOK_DB_TABLE_CREATE", "La création des tables suivantes a échoué :<br />");
define ("NOK_DB_TABLE_INSERT", "Le minimum de données n'a pas pu être ajouté dans les tables :<br />");
define ("NOK_EMAIL_LEN", "Merci de saisir votre e-mail.");
define ("NOK_PWD_COMPARE", "Ne correspond pas à votre nouveau mot de passe");
define ("NOK_PWD_LEN", "Votre mot de passe doit contenir au moins 8 caractères");
define ("NOK_SETTINGS_INSERT", "Echec de la création des paramètres administrateur.<br />");
define ("NOK_USER_INSERT", "Echec de la création du compte administrateur.<br />");

define ("OK_ADMIN_ACCOUNT1", "Votre compte administrateur <strong><span class=\"jaune\">&nbsp;&nbsp;");
define ("OK_ADMIN_ACCOUNT2", "&nbsp;</span></strong> a été créé avec succès.<br />");
define ("OK_CONFIG_FILE", "Votre fichier de configuration a été créé avec succès.");
define ("OK_DB_CONNECTION", "La connexion à votre base de données est effective.");
define ("OK_DB_TABLE_CREATE", "Les tables suivantes ont été créées avec succès :<br />");
define ("OK_DB_TABLE_INSERT", "Un minimum de données a été ajouté dans les tables :<br />");

define ("PHP_VERSION_IS", "Votre version de PHP est : ");
?>