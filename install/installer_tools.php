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

require_once ("class/db.class.php");

// supprimer l'échappement automatique
function supprime_echap_auto($tableau) {
	foreach ($tableau as $cle => $valeur) {
		if (!is_array($valeur)) $tableau[$cle] = stripslashes($valeur);
		else $tableau[$cle] = supprime_echap_auto($valeur);
	}
	return $tableau;
}
if (get_magic_quotes_gpc()) {
	$_POST = supprime_echap_auto($_POST);
	$_GET = supprime_echap_auto($_GET);
	$_REQUEST = supprime_echap_auto($_REQUEST);
	$_COOKIE = supprime_echap_auto($_COOKIE);
}
// langue adaptée au client
$langue = autoSelectLanguage(array('fr','en'), 'en') ;
if ($langue === "fr") require_once ("install/fr_install.php");
else require_once ("install/en_install.php");
// Detection automatique de la langue du navigateur
// author Hugo Hamon (version 0.1)
function autoSelectLanguage($aLanguages, $sDefault) {
	if(!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
		$aBrowserLanguages = explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);    
		foreach($aBrowserLanguages as $sBrowserLanguage) {
			$sLang = strtolower(substr($sBrowserLanguage,0,2));      
			if(in_array($sLang, $aLanguages)) {
				return $sLang;
			}    
		}  
	}
	return $sDefault;
}

function install_begin ($step) {
	require ("html/html_meta.inc");
	$bandeau = BANDEAU.$step."/5" ;
	echo "\r\n\t<title>" . $bandeau . "</title>\r\n" ;
	echo "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"css/look.css\">\r\n" ;
	echo "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"install/look_install.css\">\r\n" ;
	echo "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"css/look_button.css\">\r\n" ;
	echo "</head>\r\n<body>\r\n<div id=\"installBloc\">\r\n" ;
	echo "\t<div id=\"installContainer\">\r\n" ;
	$step_img = "install/img/step-".$step.".png" ;
	echo "\t<img src=\"".$step_img."\" width=\"450\" height=\"36\" alt=\"step\" />\r\n" ;
	echo "\t\t<div id=\"installContent\">\r\n" ;
}
function install_end () {
	echo "\r\n\t\t</div><!-- #installContent -->\r\n\t</div><!-- #installContainer -->\r\n" ;
	echo "</div><!-- #installBloc -->\r\n</body>\r\n</html>" ;
	exit ; 
}

function config_form ($ARRAY_config, $ARRAY_error) {
	startform ("config_form", "install.php?step=2", FORM_ENTETE_1) ;
	foreach ($ARRAY_config as $clef => $valeur) $ARRAY_config[$clef] = htmlspecialchars(stripslashes($valeur));
	text_field (LABEL_SITE_NAME, "site",  $ARRAY_config['site'], 2, $ARRAY_error['site']) ;
	echo "\t\t\t\t<h5>" . LABEL_SITE_NAME_INFO . "</h5>\r\n" ;
	lib_field (LABEL_DOMAIN_NAME, $_SERVER['SERVER_NAME']) ;
	echo "\t\t\t\t<hr>\r\n" ;
	echo "\t\t\t\t<h3>" . LABEL_DB_INFOS . "</h3>\r\n" ;
	text_field (LABEL_DB_USERNAME, "db_username",  $ARRAY_config['db_username'], 2, $ARRAY_error['db_username']) ;
	
//	text_field (LABEL_DB_PASSWORD, "db_password",  $ARRAY_config['db_password'], 2, $ARRAY_error['db_password']) ;
	password_field (LABEL_DB_PASSWORD, "db_password",  $ARRAY_config['db_password'], $ARRAY_error['db_password']) ;
	
	text_field (LABEL_DB_HOST, "db_host",  $ARRAY_config['db_host'], 2, $ARRAY_error['db_host']) ;
	text_field (LABEL_DB_NOUN, "db_noun",  $ARRAY_config['db_noun'], 2, $ARRAY_error['db_noun']) ;
	end_entete () ;
	submit_field ("&nbsp;", "validate", CONFIRM_STEP) ;
	stopform () ;
}
function config_control ($ARRAY_input) {
	$ARRAY_error['site'] = $ARRAY_error['db_username'] = $ARRAY_error['db_password'] = $ARRAY_error['db_host'] = $ARRAY_error['db_noun'] = "" ;
	if (strlen ($ARRAY_input['site']) < 2) $ARRAY_error['site'] = ERROR_EMPTY ;
	if (strlen ($ARRAY_input['db_username']) < 2) $ARRAY_error['db_username'] = ERROR_EMPTY ;
	if (strlen ($ARRAY_input['db_host']) < 2) $ARRAY_error['db_host'] = ERROR_EMPTY ;
	if (strlen ($ARRAY_input['db_noun']) < 2) $ARRAY_error['db_noun'] = ERROR_EMPTY ;
	return $ARRAY_error ;
}
function config_write ($ARRAY_config) {
	$domain = $_SERVER['SERVER_NAME'] ;
	$site = $ARRAY_config['site'] ;
	$db_username = $ARRAY_config['db_username'] ;
	$db_password = $ARRAY_config['db_password'] ;
	$db_host = $ARRAY_config['db_host'] ;
	$db_noun = $ARRAY_config['db_noun'] ;
	$handle = fopen ("localconf.php", "w") ;
	fwrite ($handle, "<?php \n") ;
	fwrite ($handle, "// general informations : upload_path | domain | site \n") ;
	fwrite ($handle, "define ('UPLDP', 'upload/');\n") ;
	fwrite ($handle, "define ('DOMAIN', '$domain');\n") ;
	fwrite ($handle, "define ('SITE', '$site');\n") ;
	fwrite ($handle, "// MySQL connexion parameters \n") ;
	fwrite ($handle, "define ('DB_USERNAME', '$db_username');\n") ;
	fwrite ($handle, "define ('DB_PASSWORD', '$db_password');\n") ;
	fwrite ($handle, "define ('DB_HOST', '$db_host');\n") ;
	fwrite ($handle, "define ('DB_NOUN', '$db_noun');\n") ;
	fwrite ($handle, "// Pour les liens externes : aide... \n") ;
	fwrite ($handle, "define ('GCMCECKURL', 'http://gcm.ceck.org/');\n") ;
	fwrite ($handle, "?>") ;
	fclose ($handle);
	return OK_CONFIG_FILE ;
}

function admin_account_form ($ARRAY_admin_account, $ARRAY_error) {
	startform ("admin_account_form", "install.php?step=5", FORM_ENTETE_2) ;
	text_field (LABEL_ADMIN_USERNAME, "admin_username",  $ARRAY_admin_account['admin_username'], 2, $ARRAY_error['admin_username']) ;
	text_field (LABEL_ADMIN_MAIL, "admin_mail",  $ARRAY_admin_account['admin_mail'], 2, $ARRAY_error['admin_mail']) ;
	password_field (LABEL_ADMIN_PASSWORD, "password",  "", $ARRAY_error['password']) ;
	password_field (LABEL_ADMIN_CONFIRM_IT, "password_confirm",  "", $ARRAY_error['password_confirm']) ;
	end_entete () ;
	submit_field ("&nbsp;", "validate", CONFIRM_STEP) ;
	stopform () ;
}
function admin_account_control ($ARRAY_input) {
	$ARRAY_error['admin_username'] = $ARRAY_error['admin_mail'] = $ARRAY_error['password'] = $ARRAY_error['password_confirm'] = "" ;
	if (strlen ($ARRAY_input['admin_username']) < 5) $ARRAY_error['admin_username'] .= NOK_ADMIN_LEN ;
	if (strlen ($ARRAY_input['admin_mail']) < 9) $ARRAY_error['admin_mail'] .= NOK_EMAIL_LEN ;
	if(preg_match('/[^0-9A-Za-z_@:]/',$ARRAY_input['password'])) $ARRAY_error['password'] .= NOK_CHAR ;
	if (strlen ($ARRAY_input['password']) < 8) $ARRAY_error['password'] .= NOK_PWD_LEN ;
	if ($ARRAY_input['password_confirm'] != $ARRAY_input['password']) $ARRAY_error['password_confirm'] .= NOK_PWD_COMPARE ;
	return $ARRAY_error ;
}
function admin_account_write ($ARRAY_admin_account, $db) {
	$feedback = $warning = "" ;
	$user_login = $db->real_escape_string($ARRAY_admin_account['admin_username']);
	$user_email = $db->real_escape_string($ARRAY_admin_account['admin_mail']);
	$user_pass = $db->real_escape_string($ARRAY_admin_account['password']);
	$user_pass_md5 = md5($user_pass) ;
	$user_rights = "administrator" ;
	$user_make_date = date("U") ;
	$user_history = "##### Created at installation #####" ;
	$user_lang = LANG ;
	// insertion dans la table user
	$user_insert = "INSERT INTO user (user_login, user_email, user_pass_md5, user_rights, user_make_date, user_history, user_lang) VALUES ('$user_login', '$user_email', '$user_pass_md5', '$user_rights', '$user_make_date', '$user_history', '$user_lang');" ;
	if (!$db->query ($user_insert)) $warning .= NOK_USER_INSERT ;
	// insertion dans la table settings avec paramètres prédéfinis
	$settings_insert = "INSERT INTO `settings` (`param_login`, `param_screen_width`, `param_screen_height`, `param_max_width`, `param_max_height`, `param_upload_form`) VALUES ('$user_login', 1280, 1024, 1200, 900, 'detail');" ;
	if (!$db->query ($settings_insert)) $warning .= NOK_SETTINGS_INSERT ;
	if ($warning === "") $feedback = OK_ADMIN_ACCOUNT1.$user_login.OK_ADMIN_ACCOUNT2 ;
	$retour = array ("0"=>"$feedback", "1"=>"$warning") ;
	return $retour ;
}

function create_tables ($db) {
	$feedback = $warning = $okay_create = $nogood_create = $okay_insert = $nogood_insert = "" ;
	// table album + 1 ligne (album par défaut)
	// ########################################
		$sql10 = "CREATE TABLE IF NOT EXISTS `album` (
		`album_id` mediumint(8) unsigned NOT NULL,
		`album_keywords` varchar(128) NOT NULL,
		`album_title` varchar(64) DEFAULT NULL,
		`album_author` varchar(32) DEFAULT NULL,
		`album_comment` text NOT NULL,
		`album_clicks` int(11) NOT NULL DEFAULT '1',
		`album_visibility` varchar(8) NOT NULL DEFAULT 'perso',
		`album_maked_on` int(11) unsigned NOT NULL DEFAULT '0',
		`album_maked_by` varchar(64) NOT NULL DEFAULT '',
		`album_revised_on` int(11) unsigned NOT NULL DEFAULT '0',
		`album_revised_by` varchar(64) NOT NULL DEFAULT '',
		PRIMARY KEY (`album_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;" ;
	if ($db->query ($sql10)) $okay_create .= "album | ";
	else $nogood_create .= "album | ";
		if (LANG === "fr") {
			$album_title = "Album par défaut" ;
			$album_keywords = "Album par défaut gcm original" ;
		} else {
			$album_title = "Default album" ;
			$album_keywords = "Default album gcm original" ;
		}
		$sql11 = "INSERT INTO `album` (`album_id`, `album_keywords`, `album_title`, `album_author`, `album_comment`, `album_clicks`, `album_visibility`, `album_maked_on`, `album_maked_by`, `album_revised_on`, `album_revised_by`) VALUES
		(1, '$album_keywords', '$album_title', 'gcm', 'original', 10, 'everyone', 1350562103, 'toto', 0, '');" ;
	if ($db->query ($sql11)) $okay_insert .= "+1 > album | ";
	else $nogood_insert .= "-1 > album | ";

	// table cache
	// ###########
		$sql20 = "CREATE TABLE IF NOT EXISTS `cache` (
		`cache_id` varchar(64) NOT NULL,
		`cache_criteria` varchar(256) NOT NULL,
		`cache_request` varchar(1024) NOT NULL,
		`cache_url` varchar(256) NOT NULL,
		`cache_tstamp` int(11) NOT NULL,
		PRIMARY KEY (`cache_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;" ;
	if ($db->query ($sql20)) $okay_create .= "cache | ";
	else $nogood_create .= "cache | ";

	// table constants + 1 ligne
	// #########################
		$sql30 = "CREATE TABLE IF NOT EXISTS `constants` (
		`constant_id` tinyint(4) NOT NULL AUTO_INCREMENT,
		`constant_media` int(11) NOT NULL,
		`constant_album` smallint(6) NOT NULL,
		PRIMARY KEY (`constant_id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;" ;
	if ($db->query ($sql30)) $okay_create .= "constants | ";
	else $nogood_create .= "constants | ";
		$sql31 = "INSERT INTO `constants` (`constant_id`, `constant_media`, `constant_album`) VALUES (1, 5, 1);" ;
	if ($db->query ($sql31)) $okay_insert .= "+1 > constants | ";
	else $nogood_insert .= "-1 > constants | ";

	// table country + 244 lignes
	// ##########################
		$sql40 = "CREATE TABLE IF NOT EXISTS `country` (
		`country_id` smallint(4) NOT NULL,
		`country_short` varchar(2) NOT NULL,
		`country_en` varchar(32) DEFAULT NULL,
		`country_fr` varchar(32) DEFAULT NULL,
		PRIMARY KEY (`country_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;" ;
	if ($db->query ($sql40)) $okay_create .= "country | ";
	else $nogood_create .= "country | ";
		$sql41 = "INSERT INTO `country` (`country_id`, `country_short`, `country_en`, `country_fr`) VALUES
		(101, 'AD', 'Andorra', 'Andorre'),
		(102, 'AE', 'United Arab Emirates', 'Émirats Arabes Unis'),
		(103, 'AF', 'Afghanistan', 'Afghanistan'),
		(104, 'AG', 'Antigua and Barbuda', 'Antigua-et-Barbuda'),
		(105, 'AI', 'Anguilla', 'Anguilla'),
		(106, 'AL', 'Albania', 'Albanie'),
		(107, 'AM', 'Armenia', 'Arménie'),
		(108, 'AN', 'Netherlands Antilles', 'Antilles Néerlandaises'),
		(109, 'AO', 'Angola', 'Angola'),
		(110, 'AQ', 'Antarctica', 'Antarctique'),
		(111, 'AR', 'Argentina', 'Argentine'),
		(112, 'AS', 'American Samoa', 'Samoa Américaines'),
		(113, 'AT', 'Austria', 'Autriche'),
		(114, 'AU', 'Australia', 'Australie'),
		(115, 'AW', 'Aruba', 'Aruba'),
		(116, 'AX', 'Åland Islands', 'Isles d''Åland'),
		(117, 'AZ', 'Azerbaijan', 'Azerbaïdjan'),
		(118, 'BA', 'Bosnia and Herzegovina', 'Bosnie-Herzégovine'),
		(119, 'BB', 'Barbados', 'Barbade'),
		(120, 'BD', 'Bangladesh', 'Bangladesh'),
		(121, 'BE', 'Belgium', 'Belgique'),
		(122, 'BF', 'Burkina Faso', 'Burkina Faso'),
		(123, 'BG', 'Bulgaria', 'Bulgarie'),
		(124, 'BH', 'Bahrain', 'Bahreïn'),
		(125, 'BI', 'Burundi', 'Burundi'),
		(126, 'BJ', 'Benin', 'Bénin'),
		(127, 'BM', 'Bermuda', 'Bermudes'),
		(128, 'BN', 'Brunei Darussalam', 'Brunéi Darussalam'),
		(129, 'BO', 'BoliviaA Plurinational State of', 'Bolivie'),
		(130, 'BR', 'Brazil', 'Brésil'),
		(131, 'BS', 'Bahamas', 'Bahamas'),
		(132, 'BT', 'Bhutan', 'Bhoutan'),
		(133, 'BV', 'Bouvet Island', 'Bouvet, Île'),
		(134, 'BW', 'Botswana', 'Botswana'),
		(135, 'BY', 'Belarus', 'Bélarus'),
		(136, 'BZ', 'Belize', 'Belize'),
		(137, 'CA', 'Canada', 'Canada'),
		(138, 'CC', 'Cocos (Keeling) Islands', 'Cocos (Keeling), Îles'),
		(139, 'CD', 'Congo, Democratic Republic of', 'Congo (République Démocratique)'),
		(140, 'CF', 'Central African Republic', 'Centrafricaine, République'),
		(141, 'CG', 'Congo', 'Congo'),
		(142, 'CH', 'Switzerland', 'Suisse'),
		(143, 'CI', 'Côte d''Ivoire', 'Côte d''Ivoire'),
		(144, 'CK', 'Cook Islands', 'Cook, Îles'),
		(145, 'CL', 'Chile', 'Chili'),
		(146, 'CM', 'Cameroon', 'Cameroun'),
		(147, 'CN', 'China', 'Chine'),
		(148, 'CO', 'Colombia', 'Colombie'),
		(149, 'CR', 'Costa Rica', 'Costa Rica'),
		(150, 'CU', 'Cuba', 'Cuba'),
		(151, 'CV', 'Cape Verde', 'Cap-Vert'),
		(152, 'CX', 'Christmas Island', 'Christmas, Île'),
		(153, 'CY', 'Cyprus', 'Chypre'),
		(154, 'CZ', 'Czech Republic', 'Tchèque, République'),
		(155, 'DE', 'Germany', 'Allemagne'),
		(156, 'DJ', 'Djibouti', 'Djibouti'),
		(157, 'DK', 'Denmark', 'Danemark'),
		(158, 'DM', 'Dominica', 'Dominique'),
		(159, 'DO', 'Dominican Republic', 'Dominicaine, République'),
		(160, 'DZ', 'Algeria', 'Algérie'),
		(161, 'EC', 'Ecuador', 'Équateur'),
		(162, 'EE', 'Estonia', 'Estonie'),
		(163, 'EG', 'Egypt', 'Égypte'),
		(164, 'EH', 'Western Sahara', 'Sahara Occidental'),
		(165, 'ER', 'Eritrea', 'Érythrée'),
		(166, 'ES', 'Spain', 'Espagne'),
		(167, 'ET', 'Ethiopia', 'Éthiopie'),
		(168, 'FI', 'Finland', 'Finlande'),
		(169, 'FJ', 'Fiji', 'Fidji'),
		(170, 'FK', 'Falkland Islands (Malvinas)', 'Falkland, Îles (Malvinas)'),
		(171, 'FM', 'Micronesia, Federated States of', 'Micronésie, États Fédérés de'),
		(172, 'FO', 'Faroe Islands', 'Féroé, Îles'),
		(173, 'FR', 'France', 'France'),
		(174, 'GA', 'Gabon', 'Gabon'),
		(175, 'GB', 'United Kingdom', 'Royaume-Uni'),
		(176, 'GD', 'Grenada', 'Grenade'),
		(177, 'GE', 'Georgia', 'Géorgie'),
		(178, 'GF', 'French Guiana', 'Guyane Française'),
		(179, 'GG', 'Guernsey', 'Guernesey'),
		(180, 'GH', 'Ghana', 'Ghana'),
		(181, 'GI', 'Gibraltar', 'Gibraltar'),
		(182, 'GL', 'Greenland', 'Groenland'),
		(183, 'GM', 'Gambia', 'Gambie'),
		(184, 'GN', 'Guinea', 'Guinée'),
		(185, 'GP', 'Guadeloupe', 'Guadeloupe'),
		(186, 'GQ', 'Equatorial Guinea', 'Guinée Équatoriale'),
		(187, 'GR', 'Greece', 'Grèce'),
		(188, 'GS', 'South Georgia/Sandwich Islands', 'Géorgie et Îles Sandwich du Sud'),
		(189, 'GT', 'Guatemala', 'Guatemala'),
		(190, 'GU', 'Guam', 'Guam'),
		(191, 'GW', 'Guinea-Bissau', 'Guinée-Bissau'),
		(192, 'GY', 'Guyana', 'Guyana'),
		(193, 'HK', 'Hong Kong', 'Hong-Kong'),
		(194, 'HM', 'Heard and Mcdonald Islands', 'Heard et Mcdonald, Îles'),
		(195, 'HN', 'Honduras', 'Honduras'),
		(196, 'HR', 'Croatia', 'Croatie'),
		(197, 'HT', 'Haiti', 'Haïti'),
		(198, 'HU', 'Hungary', 'Hongrie'),
		(199, 'ID', 'Indonesia', 'Indonésie'),
		(200, 'IE', 'Ireland', 'Irlande'),
		(201, 'IL', 'Israel', 'Israël'),
		(202, 'IM', 'Isle of Man', 'Île de Man'),
		(203, 'IN', 'India', 'Inde'),
		(204, 'IO', 'British Indian Ocean Territory', 'Océan Indien (Territ. Britan.)'),
		(205, 'IQ', 'Iraq', 'Iraq'),
		(206, 'IR', 'Iran, Islamic Republic of', 'Iran, République Islamique d'''),
		(207, 'IS', 'Iceland', 'Islande'),
		(208, 'IT', 'Italy', 'Italie'),
		(209, 'JE', 'Jersey', 'Jersey'),
		(210, 'JM', 'Jamaica', 'Jamaïque'),
		(211, 'JO', 'Jordan', 'Jordanie'),
		(212, 'JP', 'Japan', 'Japon'),
		(213, 'KE', 'Kenya', 'Kenya'),
		(214, 'KG', 'Kyrgyzstan', 'Kirghizistan'),
		(215, 'KH', 'Cambodia', 'Cambodge'),
		(216, 'KI', 'Kiribati', 'Kiribati'),
		(217, 'KM', 'Comoros', 'Comores'),
		(218, 'KN', 'Saint Kitts and Nevis', 'Saint-Kitts-et-Nevis'),
		(219, 'KP', 'Korea, Ddemocratic Republic of', 'Corée (République Populaire D.)'),
		(220, 'KR', 'Korea, Republic of', 'Corée, République de'),
		(221, 'KW', 'Kuwait', 'Koweït'),
		(222, 'KY', 'Cayman Islands', 'Caïmanes, Îles'),
		(223, 'KZ', 'Kazakhstan', 'Kazakhstan'),
		(224, 'LA', 'Lao People S Democrat. Republic', 'Lao (République Démocrat. Pop.)'),
		(225, 'LB', 'Lebanon', 'Liban'),
		(226, 'LC', 'Saint Lucia', 'Sainte-Lucie'),
		(227, 'LI', 'Liechtenstein', 'Liechtenstein'),
		(228, 'LK', 'Sri Lanka', 'Sri Lanka'),
		(229, 'LR', 'Liberia', 'Libéria'),
		(230, 'LS', 'Lesotho', 'Lesotho'),
		(231, 'LT', 'Lithuania', 'Lituanie'),
		(232, 'LU', 'Luxembourg', 'Luxembourg'),
		(233, 'LV', 'Latvia', 'Lettonie'),
		(234, 'LY', 'Libyan Arab Jamahiriya', 'Libyenne, Jamahiriya Arabe'),
		(235, 'MA', 'Morocco', 'Maroc'),
		(236, 'MC', 'Monaco', 'Monaco'),
		(237, 'MD', 'Moldova, Republic of', 'Moldova, République de'),
		(238, 'ME', 'Montenegro', 'Monténégro'),
		(239, 'MG', 'Madagascar', 'Madagascar'),
		(240, 'MH', 'Marshall Islands', 'Marshall, Îles'),
		(241, 'MK', 'Macedonia, Former Yugoslav R.', 'Macédoine (Ex-Rép.Yougoslave)'),
		(242, 'ML', 'Mali', 'Mali'),
		(243, 'MM', 'Myanmar', 'Myanmar'),
		(244, 'MN', 'Mongolia', 'Mongolie'),
		(245, 'MO', 'Macao', 'Macao'),
		(246, 'MP', 'Northern Mariana Islands', 'Mariannes du Nord, Îles'),
		(247, 'MQ', 'Martinique', 'Martinique'),
		(248, 'MR', 'Mauritania', 'Mauritanie'),
		(249, 'MS', 'Montserrat', 'Montserrat'),
		(250, 'MT', 'Malta', 'Malte'),
		(251, 'MU', 'Mauritius', 'Maurice'),
		(252, 'MV', 'Maldives', 'Maldives'),
		(253, 'MW', 'Malawi', 'Malawi'),
		(254, 'MX', 'Mexico', 'Mexique'),
		(255, 'MY', 'Malaysia', 'Malaisie'),
		(256, 'MZ', 'Mozambique', 'Mozambique'),
		(257, 'NA', 'Namibia', 'Namibie'),
		(258, 'NC', 'New Caledonia', 'Nouvelle-Calédonie'),
		(259, 'NE', 'Niger', 'Niger'),
		(260, 'NF', 'Norfolk Island', 'Norfolk, Île'),
		(261, 'NG', 'Nigeria', 'Nigéria'),
		(262, 'NI', 'Nicaragua', 'Nicaragua'),
		(263, 'NL', 'Netherlands', 'Pays-Bas'),
		(264, 'NO', 'Norway', 'Norvège'),
		(265, 'NP', 'Nepal', 'Népal'),
		(266, 'NR', 'Nauru', 'Nauru'),
		(267, 'NU', 'Niue', 'Niué'),
		(268, 'NZ', 'New Zealand', 'Nouvelle-Zélande'),
		(269, 'OM', 'Oman', 'Oman'),
		(270, 'PA', 'Panama', 'Panama'),
		(271, 'PE', 'Peru', 'Pérou'),
		(272, 'PF', 'French Polynesia', 'Polynésie Française'),
		(273, 'PG', 'Papua New Guinea', 'Papouasie-Nouvelle-Guinée'),
		(274, 'PH', 'Philippines', 'Philippines'),
		(275, 'PK', 'Pakistan', 'Pakistan'),
		(276, 'PL', 'Poland', 'Pologne'),
		(277, 'PM', 'Saint Pierre and Miquelon', 'Saint-Pierre-et-Miquelon'),
		(278, 'PN', 'Pitcairn', 'Pitcairn'),
		(279, 'PR', 'Puerto Rico', 'Porto Rico'),
		(280, 'PS', 'Palestinian Territory, Occupied', 'Palestinien Occupé, Territoire'),
		(281, 'PT', 'Portugal', 'Portugal'),
		(282, 'PW', 'Palau', 'Palaos'),
		(283, 'PY', 'Paraguay', 'Paraguay'),
		(284, 'QA', 'Qatar', 'Qatar'),
		(285, 'RE', 'Réunion', 'Réunion'),
		(286, 'RO', 'Romania', 'Roumanie'),
		(287, 'RS', 'Serbia', 'Serbie'),
		(288, 'RU', 'Russian Federation', 'Russie, Fédération de'),
		(289, 'RW', 'Rwanda', 'Rwanda'),
		(290, 'SA', 'Saudi Arabia', 'Arabie Saoudite'),
		(291, 'SB', 'Solomon Islands', 'Salomon, Îles'),
		(292, 'SC', 'Seychelles', 'Seychelles'),
		(293, 'SD', 'Sudan', 'Soudan'),
		(294, 'SE', 'Sweden', 'Suède'),
		(295, 'SG', 'Singapore', 'Singapour'),
		(296, 'SH', 'Saint Helena', 'Sainte-Hélène'),
		(297, 'SI', 'Slovenia', 'Slovénie'),
		(298, 'SJ', 'Svalbard and Jan Mayen', 'Svalbard et Île Jan Mayen'),
		(299, 'SK', 'Slovakia', 'Slovaquie'),
		(300, 'SL', 'Sierra Leone', 'Sierra Leone'),
		(301, 'SM', 'San Marino', 'Saint-Marin'),
		(302, 'SN', 'Senegal', 'Sénégal'),
		(303, 'SO', 'Somalia', 'Somalie'),
		(304, 'SR', 'Suriname', 'Suriname'),
		(305, 'ST', 'Sao Tome and Principe', 'Sao Tomé-et-Principe'),
		(306, 'SV', 'El Salvador', 'El Salvador'),
		(307, 'SY', 'Syrian Arab Republic', 'Syrienne, République Arabe'),
		(308, 'SZ', 'Swaziland', 'Swaziland'),
		(309, 'TC', 'Turks and Caicos Islands', 'Turks et Caïques, Îles'),
		(310, 'TD', 'Chad', 'Tchad'),
		(311, 'TF', 'French Southern Territories', 'Terres Australes Françaises'),
		(312, 'TG', 'Togo', 'Togo'),
		(313, 'TH', 'Thailand', 'Thaïlande'),
		(314, 'TJ', 'Tajikistan', 'Tadjikistan'),
		(315, 'TK', 'Tokelau', 'Tokelau'),
		(316, 'TL', 'Timor-Leste', 'Timor-Leste'),
		(317, 'TM', 'Turkmenistan', 'Turkménistan'),
		(318, 'TN', 'Tunisia', 'Tunisie'),
		(319, 'TO', 'Tonga', 'Tonga'),
		(320, 'TR', 'Turkey', 'Turquie'),
		(321, 'TT', 'Trinidad and Tobago', 'Trinité-et-Tobago'),
		(322, 'TV', 'Tuvalu', 'Tuvalu'),
		(323, 'TW', 'Taiwan, Province of China', 'Taïwan, Province de Chine'),
		(324, 'TZ', 'Tanzania, United Republic of', 'Tanzanie, République-Unie de'),
		(325, 'UA', 'Ukraine', 'Ukraine'),
		(326, 'UG', 'Uganda', 'Ouganda'),
		(327, 'UM', 'United States Minor Out.Islands', 'Îles Mineures des États-Unis'),
		(328, 'US', 'United States', 'États-Unis'),
		(329, 'UY', 'Uruguay', 'Uruguay'),
		(330, 'UZ', 'Uzbekistan', 'Ouzbékistan'),
		(331, 'VA', 'Holy See (Vatican City State)', 'Saint-Siège (Vatican)'),
		(332, 'VC', 'Saint Vincent & The Grenadines', 'Saint-Vincent-et-Les Grenadines'),
		(333, 'VE', 'Venezuela, Bolivarian Republ.of', 'Venezuela '),
		(334, 'VG', 'Virgin Islands, British', 'Îles Vierges Britanniques'),
		(335, 'VI', 'Virgin Islands, U.S.', 'Îles Vierges des États-Unis'),
		(336, 'VN', 'Viet Nam', 'Viet Nam'),
		(337, 'VU', 'Vanuatu', 'Vanuatu'),
		(338, 'WF', 'Wallis and Futuna', 'Wallis et Futuna'),
		(339, 'WS', 'Samoa', 'Samoa'),
		(340, 'YE', 'Yemen', 'Yémen'),
		(341, 'YT', 'Mayotte', 'Mayotte'),
		(342, 'ZA', 'South Africa', 'Afrique du Sud'),
		(343, 'ZM', 'Zambia', 'Zambie'),
		(344, 'ZW', 'Zimbabwe', 'Zimbabwe');" ;
	if ($db->query ($sql41)) $okay_insert .= "+244 > country | ";
	else $nogood_insert .= "-244 > country | ";

	// table exif + 1 ligne
	// ####################
		$sql50 = "CREATE TABLE IF NOT EXISTS `exif` (
		`exif_id` int(11) NOT NULL,
		`exif_date` datetime DEFAULT NULL,
		`exif_manufacturer` varchar(32) DEFAULT NULL,
		`exif_model` varchar(32) DEFAULT NULL,
		`exif_exposure` varchar(16) DEFAULT NULL,
		`exif_fnumber` varchar(16) DEFAULT NULL,
		`exif_iso` varchar(8) DEFAULT NULL,
		`exif_aperture` varchar(16) DEFAULT NULL,
		`exif_light` varchar(8) DEFAULT NULL,
		`exif_focal` varchar(16) DEFAULT NULL,
		PRIMARY KEY (`exif_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;" ;
	if ($db->query ($sql50)) $okay_create .= "exif | ";
	else $nogood_create .= "exif | ";
		$sql51 = "INSERT INTO `exif` (`exif_id`, `exif_date`, `exif_manufacturer`, `exif_model`, `exif_exposure`, `exif_fnumber`, `exif_iso`, `exif_aperture`, `exif_light`, `exif_focal`) VALUES
		(3, '2012-02-07 12:17:00', 'SAMSUNG', 'GT-I9003', '1/3821', '26/10', '50', '26/10', 'indefini', '3430/1000');" ;
	if ($db->query ($sql51)) $okay_insert .= "+1 > exif | ";
	else $nogood_insert .= "-1 > exif | ";

	// table item + 5 lignes
	// #####################
		$sql60 = "CREATE TABLE IF NOT EXISTS `item` (
		`item_id` tinyint(4) unsigned NOT NULL,
		`item_en` varchar(24) NOT NULL,
		`item_fr` varchar(24) NOT NULL,
		PRIMARY KEY (`item_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;" ;
	if ($db->query ($sql60)) $okay_create .= "item | ";
	else $nogood_create .= "item | ";
		$sql61 = "INSERT INTO `item` (`item_id`, `item_en`, `item_fr`) VALUES
		(1, 'mp3-Audio', 'Audio-mp3'),
		(2, 'gif-Image', 'Image-gif'),
		(3, 'jpg-Image', 'Image-jpg'),
		(4, 'png-Image', 'Image-png'),
		(5, 'flv-Video', 'Vidéo-flv');" ;
	if ($db->query ($sql61)) $okay_insert .= "+5 > item | ";
	else $nogood_insert .= "-5 > item | ";

	// table media + 5 lignes (1 échantillon de chaque media)
	// ######################################################
		$sql70 = "CREATE TABLE IF NOT EXISTS `media` (
		`mid` int(11) NOT NULL,
		`itemid` tinyint(4) unsigned NOT NULL DEFAULT '0',
		`albumid` int(11) NOT NULL DEFAULT '0',
		`keywords` varchar(128) DEFAULT NULL,
		`title` varchar(64) DEFAULT NULL,
		`author` varchar(32) DEFAULT 'inconnu',
		`url` text,
		`comment` text NOT NULL,
		`media_name` varchar(16) DEFAULT NULL,
		`original_name` varchar(256) NOT NULL,
		`event_date` date DEFAULT '0000-00-00',
		`media_size` int(11) NOT NULL DEFAULT '1',
		`clicks` int(11) NOT NULL DEFAULT '1',
		`downloads` int(11) NOT NULL DEFAULT '1',
		`maked_on` int(11) unsigned NOT NULL DEFAULT '0',
		`maked_by` varchar(64) NOT NULL DEFAULT '',
		`revised_on` int(11) unsigned NOT NULL DEFAULT '0',
		`revised_by` varchar(64) NOT NULL DEFAULT '',
		PRIMARY KEY (`mid`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;" ;
	if ($db->query ($sql70)) $okay_create .= "media | ";
	else $nogood_create .= "media | ";
		$sql71 = "INSERT INTO `media` (`mid`, `itemid`, `albumid`, `keywords`, `title`, `author`, `url`, `comment`, `media_name`, `original_name`, `event_date`, `media_size`, `clicks`, `downloads`, `maked_on`, `maked_by`, `revised_on`, `revised_by`) VALUES
		(1, 1, 1, 'sonore.mp3 sonore gcm ', 'sonore', 'gcm', '', '', 'a.000001.mp3', 'sonore.mp3', '2012-10-29', 651496, 11, 1, 1350565620, 'toto', 0, ''),
		(2, 2, 1, 'image.gif image gcm ', 'image', 'gcm', '', '', 'i.000002.gif', 'image.gif', '2012-10-29', 18133, 11, 1, 1350565620, 'toto', 0, ''),
		(3, 3, 1, 'photo.jpg photo gcm ', 'photo', 'gcm', '', '', 'i.000003.jpg', 'photo.jpg', '2012-02-07', 248554, 11, 1, 1350565620, 'toto', 0, ''),
		(4, 4, 1, 'image.png  image gcm ', 'image', 'gcm', '', '', 'i.000004.png', 'image.png', '2012-10-29', 142882, 11, 1, 1350565620, 'toto', 0, ''),
		(5, 5, 1, 'visuel.flv visuel gcm ', 'visuel', 'gcm', '', '', 'v.000005.flv', 'visuel.flv', '2012-10-29', 1306774, 23, 3, 1350565709, 'toto', 0, '');" ;
	if ($db->query ($sql71)) $okay_insert .= "+5 > media | ";
	else $nogood_insert .= "-5 > media | ";

	// table session
	// #############
		$sql80 = "CREATE TABLE IF NOT EXISTS `session` (
		`session_id` varchar(64) NOT NULL,
		`session_login` varchar(16) NOT NULL,
		`session_email` varchar(64) NOT NULL,
		`session_time_limit` int(11) unsigned NOT NULL DEFAULT '0',
		`session_type` varchar(16) DEFAULT NULL,
		`session_ip_adress` varchar(16) DEFAULT NULL,
		`session_date` datetime DEFAULT '0000-00-00 00:00:00',
		PRIMARY KEY (`session_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;" ;
	if ($db->query ($sql80)) $okay_create .= "session | ";
	else $nogood_create .= "session | ";

	// table settings
	// ##############
		$sql90 = "CREATE TABLE IF NOT EXISTS `settings` (
		`param_login` varchar(16) NOT NULL DEFAULT '',
		`param_screen_width` smallint(6) NOT NULL DEFAULT '1280',
		`param_screen_height` smallint(6) NOT NULL DEFAULT '1024',
		`param_max_width` smallint(6) NOT NULL DEFAULT '1600',
		`param_max_height` smallint(6) NOT NULL DEFAULT '1200',
		`param_upload_form` varchar(8) NOT NULL DEFAULT 'detail',
		PRIMARY KEY (`param_login`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;" ;
	if ($db->query ($sql90)) $okay_create .= "settings | ";
	else $nogood_create .= "settings | ";
		$sql91 = "INSERT INTO `settings` (`param_login`, `param_screen_width`, `param_screen_height`, `param_max_width`, `param_max_height`, `param_upload_form`) VALUES
		('anonymous', 800, 600, 800, 600, 'detail');" ;
	if ($db->query ($sql91)) $okay_insert .= "+1 > settings | ";
	else $nogood_insert .= "-1 > settings | ";

	// table user
	// ##########
		$sqlA0 = "CREATE TABLE IF NOT EXISTS `user` (
		`user_login` varchar(16) NOT NULL DEFAULT '',
		`user_email` varchar(64) NOT NULL DEFAULT '',
		`user_pass_md5` varchar(32) NOT NULL,
		`user_first_name` varchar(24) NOT NULL DEFAULT '',
		`user_last_name` varchar(24) NOT NULL DEFAULT '',
		`user_adress` varchar(48) DEFAULT NULL,
		`user_postcode` varchar(8) DEFAULT NULL,
		`user_city` varchar(40) DEFAULT NULL,
		`user_country_id` smallint(4) NOT NULL DEFAULT '173',
		`user_rights` varchar(16) NOT NULL,
		`user_control` varchar(32) DEFAULT NULL,
		`user_make_date` int(11) DEFAULT NULL,
		`user_revised_date` int(11) DEFAULT NULL,
		`user_ip` varchar(32) DEFAULT NULL,
		`user_history` text,
		`user_logs` int(11) DEFAULT '1',
		`user_lang` varchar(2) DEFAULT 'fr',
		PRIMARY KEY (`user_login`),
		UNIQUE KEY `user_email` (`user_email`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;" ;
	if ($db->query ($sqlA0)) $okay_create .= "user | ";
	else $nogood_create .= "user | ";
		if (LANG === "fr") $user_lang = "fr" ;
		else $user_lang = "en" ;
		$sqlA1 = "INSERT INTO `user` (`user_login`, `user_email`, `user_pass_md5`, `user_first_name`, `user_last_name`, `user_adress`, `user_postcode`, `user_city`, `user_country_id`, `user_rights`, `user_control`, `user_make_date`, `user_revised_date`, `user_ip`, `user_history`, `user_logs`, `user_lang`) VALUES
		('anonymous', 'no_email', '', '', '', NULL, NULL, NULL, 173, '', NULL, NULL, NULL, '127.0.0.1', NULL, 1, '$user_lang');" ;
	if ($db->query ($sqlA1)) $okay_insert .= "+1 > user | ";
	else $nogood_insert .= "-1 > user | ";

	// construction du rapport sur la création des tables et l'insertion des données
	if (($okay_create != "") or ($okay_insert != "")) {
		$feed_back = "" ;
		if ($okay_create != "") $feed_back .= OK_DB_TABLE_CREATE . $okay_create . "<br /><br />" ;
		if ($okay_insert != "") $feed_back .= OK_DB_TABLE_INSERT . $okay_insert ;
		$feedback = "<p>" . $feed_back . "</p>" ;
	}
	if (($nogood_create != "") or ($nogood_insert != "")) {
		$war_ning = "" ;
		if ($nogood_create != "") $war_ning .= NOK_DB_TABLE_CREATE . $nogood_create . "<br /><br />" ;
		if ($nogood_insert != "") $war_ning .= NOK_DB_TABLE_INSERT . $nogood_insert ;
		$warning = "<p>" . $war_ning . "</p>" ;
	}
	$retour = array ("0"=>"$feedback", "1"=>"$warning") ;
	return $retour ;
}
// #######################################################################
// #####							Fonctions de formulaire							#####
// #######################################################################
function startform ($name, $action, $entete="none") {
	echo "\t\t\t<div class=\"form_container\">\r\n" ;
	echo "\t\t\t<form id=\"".$name."\" name=\"".$name."\" enctype=\"multipart/form-data\" action=\"".$action."\" method=\"post\">\r\n" ;
	if ($entete != "none") {
		echo "\t\t\t<fieldset>\r\n" ;
		echo "\t\t\t<legend>".$entete."</legend>\r\n" ;
	}
}
function hidden_field ($name, $value) {
	echo "\t\t\t\t<input type=\"hidden\" name=\"" . $name . "\" value=\"" . $value . "\" />\r\n" ;
}
function lib_field ($label, $value) {
	echo "\t\t\t\t<p>\r\n" ;
	echo "\t\t\t\t<label for=\"lib_field\">" . $label . "</label>\r\n" ;
	echo "\t\t\t\t<span class=\"lib_field\">&nbsp;&nbsp;" . $value . "</span>\r\n" ;
	echo "\t\t\t\t</p>\r\n" ;
}
function text_field ($label, $name,  $value, $taille, $error="", $read_only="") {
	if ($read_only == "read_only") $ro = " readonly=\"readonly\"" ;
	else $ro = "" ;
	echo "\t\t\t\t<p>\r\n" ;
	echo "\t\t\t\t\t<label for=\"".$name."\">".$label."</label>\r\n" ;
	if ($error != "") echo "\t\t\t\t\t<span class=\"error\">".$error."</span>\r\n" ;
	if ($taille == 1) {
		$maxlength = 16 ;
		$class = "small" ;
	} elseif ($taille == 2) {
		$maxlength = 32 ;
		$class = "medium" ;
	} else {
		$maxlength = 512 ;
		$class = "large" ;
	}
	echo "\t\t\t\t\t<input class=\"".$class."\" type=\"text\" name=\"".$name."\" title=\"".$label."\" value=\"".$value."\" maxlength=\"".$maxlength."\"".$ro." />\r\n" ;
	echo "\t\t\t\t</p>\r\n" ;
	if ($error != "") echo "\t\t\t\t".fill_it(500,12)."\r\n" ;
}
function password_field ($label, $name,  $value, $error) {
	echo "\t\t\t\t<p>\r\n" ;
	echo "\t\t\t\t<label for=\"".$name."\">".$label."</label>\r\n" ;
	if ($error != "") echo "\t\t\t\t<span class=\"error\">" . $error . "</span>\r\n" ;
	echo "\t\t\t\t<input class=\"medium\" type=\"password\" name=\"".$name."\" value=\"".$value."\" maxlength=\"16\" />\r\n" ;
	echo "\t\t\t\t</p>\r\n" ;
}
function textarea_field ($label, $name, $value,  $rows=2, $cols=48) {
	$txtarea = "\t\t\t\t<p>\r\n" ;
	$txtarea .= "\t\t\t\t<label for=\"".$name."\">".$label."</label>\r\n" ;
	$txtarea .= "\t\t\t\t<textarea style=\"margin: 10px 0 ;\" id=\"".$name."\" name=\"".$name."\" title=\"".$label."\" rows=\"".$rows."\" cols=\"".$cols."\">".$value."</textarea>\r\n" ;
	return $txtarea .= "\t\t\t\t</p>\r\n\t\t\t\t<p>&nbsp;</p>\r\n" ;
}
function select_field ($label, $name, $value, $ARRAY_liste, $error="", $taille=1) {
	$s = "" ;
	if ($label != "") {
		$etiquette = clean($label) ;
		$s .= "\t\t\t\t<p>\r\n" ;
		$s .= "\t\t\t\t<label for=\"".$etiquette."\">".$label."</label>\r\n" ;
	}
	if ($error != "") $s .= "\t\t\t\t<span class=\"error\">".$error."</span>\r\n" ;
	$s .= "\t\t\t\t<select name=\"".$name."\" title=\"".$label."\" size=\"".$taille."\">\r\n" ;
	$content = $key = "" ;
	while (list ($content, $key) = each ($ARRAY_liste)) {
		$content = htmlspecialchars($content);
		$value = htmlspecialchars($value);
		if ($content != $value) $s .= "\t\t\t\t\t<option value=\"".$content."\">".$key."</option>\r\n" ;
		else $s .= "\t\t\t\t\t<option value=\"".$content."\" selected=\"selected\">".$key."</option>\r\n" ;
	}
	$s .= "\t\t\t\t</select>\r\n" ;
	if ($taille > 1) $s .= "\t\t\t\t<p>&nbsp;<br />&nbsp;<br />&nbsp;<br /></p>\r\n" ;
	if ($label != "") return $s . "\t\t\t\t</p>\r\n";
	else return $s ;
}
function radio_field ($label, $name, $value, $ARRAY_liste) {
	$result =  "\t\t\t\t<p>\r\n" ;
	$result .= "\t\t\t\t\t<label for=\"radio_field\">".$label."</label>\r\n" ;
	$nbval = 0 ;
	while (list ($content, $key) = each ($ARRAY_liste)) {
		if ($content == $value) $checked = " checked=\"checked\"" ;
		else $checked = "" ;
		$result .= "\t\t\t\t\t&nbsp; ".$key."<input type=\"radio\" name=\"".$name."\" value=\"".$content."\"".$checked." /> &nbsp; \r\n" ;
		$nbval++ ;
	}
	return $result .= "\t\t\t\t</p>\r\n" ;
}
function submit_field ($label, $name, $value) {
	echo "\t\t\t\t<p>\r\n" ;
	echo "\t\t\t\t\t<input class=\"soumet\" type=\"submit\" name=\"".$name."\" value=\"&nbsp;".$value."\" size=\"0\" maxlength=\"0\" />\r\n" ;
	echo "\t\t\t\t</p>\r\n\t\t\t\t<p>&nbsp;</p>\r\n" ;
}
function end_entete () {
	echo "\t\t\t</fieldset><h1>&nbsp;</h1>\r\n" ;
}
function stopform () {
	echo "\t\t\t</form><!--  #Formulaire de saisie -->\r\n" ;
	echo "\t\t\t</div><!--  #form_container -->\r\n" ;
}
function fill_it ($width=44, $height=12) {
	$alt = "fill_it_".$width."x".$height ;
	return "<img src=\"img/clear.gif\" width=\"".$width."\" height=\"".$height."\" alt=\"".$alt."\" />" ;
}
function clean ($string){
	$a = ' ()ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞ
ßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
	$b = '___aaaaaaaceeeeiiiidnoooooouuuuy
bsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
	$string = utf8_decode($string);    
	$string = strtr($string, utf8_decode($a), $b);
	$string = strtolower($string);
	return utf8_encode($string);
}
// #######################################################################
// #####							Fonctions d'affichage							#####
// #######################################################################
function display_in_box ($feedback, $warning, $information="") {
	if ($feedback != "") {
		echo "\t\t\t<div class=\"feed_can\">\r\n" ;
		echo "\t\t\t\t<div class=\"feedback\">\r\n" ;
			if (!strstr($feedback, "table")) echo "\t\t\t\t\t<p>" . $feedback .  "</p>\r\n" ;
			else echo $feedback ;
		echo "\t\t\t\t</div>\r\n" ;
		echo "\t\t\t</div>\r\n" ;
	}
	if ($warning != "") {
		echo "\t\t\t<div class=\"warn_can\">\r\n" ;
		echo "\t\t\t\t<div class=\"warning\">\r\n" ;
			if (!strstr($warning, "table")) echo "\t\t\t\t\t<p>" . $warning .  "</p>\r\n" ;
			else echo $warning ;
		echo "\t\t\t\t</div>\r\n" ;
		echo "\t\t\t</div>\r\n" ;
	}
	if ($information != "") {
		echo "\t\t\t<div class=\"info_can\">\r\n" ;
		echo "\t\t\t\t<div class=\"information\">\r\n" ;
			if (!strstr($information, "table")) echo "\t\t\t\t\t<p>" . $information .  "</p>\r\n" ;
			else echo $information ;
		echo "\t\t\t\t</div>\r\n" ;
		echo "\t\t\t</div>\r\n" ;
	}
}
?>