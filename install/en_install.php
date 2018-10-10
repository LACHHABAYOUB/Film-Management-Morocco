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

define ("LANG", "en");

define ("BACK_STEP", " Back to previous step ");
define ("BANDEAU", "GCM installation - step : ");

define ("CONFIRM_STEP", " Go to next step ");

define ("ENTETE_1", "Generalities and database configuration");
define ("ENTETE_2_1", "Configuration file creation");
define ("ENTETE_2_2", "Testing database connexion");
define ("ENTETE_3", "Creating database tables");
define ("ENTETE_4", "Collecting informations for administrator account");
define ("ENTETE_5_1", "Creating administrator account");
define ("ENTETE_5_2", "Installation completed");

define ("ERROR_EMPTY", "This field cannot stay empty");

define ("FORM_ENTETE_1", "Collecting your informations");
define ("FORM_ENTETE_2", "Don't forget to write down your password in a safe place");

define ("INTRO_ONE", "Before beginning installation, you must create your MySQL database. You can do it by using your phpMyAdmin client.<br />Don't forget to use the appropriate collation : utf8_general_ci.");
define ("INTRO_END", "By clicking on the button below, you will be directed towards the homepage of your GCM site.<br />You will have to log in with the administrator account you just created.");

define ("GO_HOME", "Go to your site's homepage");

define ("LABEL_ADMIN_CONFIRM_IT", "Confirm-it");
define ("LABEL_ADMIN_MAIL", "E-mail adress for admin");
define ("LABEL_ADMIN_PASSWORD", "Password");
define ("LABEL_ADMIN_USERNAME", "Username for admin");
define ("LABEL_DB_HOST", "Server name/url");
define ("LABEL_DB_INFOS", "Settings for connecting to your MySQL database :");
define ("LABEL_DB_NOUN", "Database name");
define ("LABEL_DB_PASSWORD", "MySQL Password");
define ("LABEL_DB_USERNAME", "MySQL Username");
define ("LABEL_DOMAIN_NAME", "Name of your domain");
define ("LABEL_SITE_NAME", "Name of your site");
define ("LABEL_SITE_NAME_INFO", "The name of your site will be displayed in the header bar of your browser.");

define ("LOREM_IPSUM", "Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan.");

define ("NOK_ADMIN_LEN", "5 characters minimum ! ");
define ("NOK_CHAR", "Authorized characters : a-z A-Z 0-9 _ @ : ] [ : (exclusively)<br />");
define ("NOK_DB_CONNECTION", "Connection to your MySQL database failed.<br />You should check the settings you entered in the previous step and try again.");
define ("NOK_DB_TABLE_CREATE", "The creation of following tables failed :<br />");
define ("NOK_DB_TABLE_INSERT", "The minimum data could not be inserted in following tables :<br />");
define ("NOK_EMAIL_LEN", "Enter your e-mail.");
define ("NOK_PWD_COMPARE", "Does not match with your password");
define ("NOK_PWD_LEN", "Your password must contain at least 8 characters");
define ("NOK_SETTINGS_INSERT", "Failed to create administrator settings.<br />");
define ("NOK_USER_INSERT", "Failed to create administrator account.<br />");

define ("OK_ADMIN_ACCOUNT1", "Your administrator account <strong><span class=\"jaune\">&nbsp;&nbsp;");
define ("OK_ADMIN_ACCOUNT2", "&nbsp;</span></strong> has been successfully created.<br />");
define ("OK_CONFIG_FILE", "Your configuration file has been successfully created.");
define ("OK_DB_CONNECTION", "Connection to your database is effective.");
define ("OK_DB_TABLE_CREATE", "The following tables have been successfully created :<br />");
define ("OK_DB_TABLE_INSERT", "Minimum data has been inserted in following tables :<br />");

define ("PHP_VERSION_IS", "Your PHP version is : ");
?>