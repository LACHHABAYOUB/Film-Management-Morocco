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
require_once("media/abc.php") ;
// Acquisition albumid a afficher
if (isset ($_GET['albumid'])) $albumid = $_GET['albumid'] ;
else error_page (ERR_UNDEFINED, "media", $path, $footer_lib, $db ) ;
// Recherche album a afficher
$ARRAY_album = search_row_by_id ("album", "album_id", $albumid, $db, TABLEAU) ;
if (is_array($ARRAY_album)) {
	foreach ($ARRAY_album as $cle => $valeur) $ARRAY_album[$cle] = html_convert($valeur);
	$visibility = $ARRAY_album['album_visibility'] ;
// Si album inexistant on arrête là
} else error_page (ERR_NO_PAGE, "media", $path, $footer_lib, $db ) ;

$bandeau = SITE ;
$page_title = SLIDESHOW." : <a href=\"index.php?location=media&amp;action=display_album&amp;albumid=".$albumid."\"  title=\"".BACK_TO_ALBUM."\">".$ARRAY_album['album_title']."</a>" ;

$album_folder = generate_album_dir_name ($albumid) ;
$directory = UPLDP.$album_folder."/" ;

// Construction du tableau des images contenues dans l'album
$rank = 0 ;
if ($handle = opendir($directory)) {
	while (($file = readdir($handle)) !== false) {
		if (($file != ".") and ($file != "..") and (stristr($file, "humb") === FALSE)) {
			list ($type, $code, $ext) = explode (".", $file) ;
			if ($type === "i") {
				$TBLmedia[$rank] = $file ;
				$TBLmedia_code[$rank] = $code ;
				$rank++ ;
			}
		}
	}
}
$media_qty = $rank ;
closedir($handle) ;
define ("ERR_NO_IMAGE", "aucune image dans cet album");
if ($media_qty == 0) error_page (ERR_NO_IMAGE, "media", $path, $footer_lib, $db ) ;
// Tri du tableau par code pour recuperer la premiere et la derniere image
sort($TBLmedia_code) ;
$first_mid = $TBLmedia_code[0] + 0 ;
$last_mid = $TBLmedia_code[($media_qty - 1)] + 0 ;
// premiere et derniere miniatures
$TBLfirst_media = search_row_by_id ("media", "mid", $first_mid, $db, TABLEAU);
$first_name = $TBLfirst_media['media_name'] ;
list ($type1, $code1, $ext1) = explode (".", $first_name) ;
$TBLlast_media = search_row_by_id ("media", "mid", $last_mid, $db, TABLEAU);
$last_name = $TBLlast_media['media_name'] ;
list ($type2, $code2, $ext2) = explode (".", $last_name) ;
$first_thumbnail = $directory."thumbnail/mini.".$code1.".".$ext1 ;
$last_thumbnail = $directory."thumbnail/mini.".$code2.".".$ext2 ;

require ("html/html_meta.inc");
?>
<title><?php echo $bandeau ; ?></title>
<link rel="stylesheet" type="text/css" href="css/look.css" media="all" />
</head>
<body onLoad="focusOnLaunch()">
<!-- (c) robloche@fr.st, 2003 -->
<script language="JavaScript" type="text/javascript">
var rep = "<?php echo $directory ; ?>";
var num = 0;
var myCounter;
var next_img = new Image;
next_img.src = "img/clear.gif";	//rep+"pixel_transparent.gif";
var wPopup;
var TBLmedia;
<?php  conv_tabjs($TBLmedia, "tabImgSave"); ?>
var nb_img = tabImgSave.length;

// Les trois fonctions suivantes (+ eventuellement, "mySplice")
// servent a melanger un tableau quelconque a partir d'une
// permutation tiree aleatoirement

// Methode "splice" si elle n'existe pas
//   tab : tableau
//   s   : debut de la suppression
//   l   : nombre d'elements à supprimer
function mySplice(s, l) {
	if(s+l > this.length) l = this.length-s;
		
	for(var i=s; i<this.length; ++i)
		this[i] = this[i+1];

	delete this[this.length-1];
	this.length--;
}

// Est-ce que la methode "splice" est disponible ?
if(!Array.prototype.splice) {
	// Non, alors on utilise la version "maison"
	Array.prototype.splice = mySplice;
}

// Genere une fonction sous-excedente
function fctSsExc() {
	var fct = new Array;
	for(var i=0; i<nb_img; i++) {
		fct[i] = Math.floor( Math.random()*(nb_img-i) );
	}
	return fct;
}

// Construit une permutation a partir d'une fonction sous-excedente
function buildSigma() {
	var fct_ss_exc = fctSsExc();
	var set_N       = new Array;

	for(var i=0; i<nb_img; i++) {
		set_N[i] = i;
	}

	var sigma = new Array;

	for(var i=0; i<nb_img; i++) {
		sigma[i] = set_N[fct_ss_exc[i]];
		set_N.splice(fct_ss_exc[i],1);
	}
	return sigma;
}

// Retourne une version melangee du tableau passe en parametre
function shuffleArray(myArray) {
	var sigma    = buildSigma();
	var newArray = new Array;

	for(var i=0; i<nb_img; i++) {
		newArray[i] = myArray[sigma[i]];
	}
	return newArray;
}
//
// Fin des fonctions de melange
//

// Donne le focus au bouton "Lancer"
function focusOnLaunch() {
	window.document.forms.settings.bLaunch.focus();
}

// Donne le focus au bouton "Stopper"
function focusOnStop() {
	window.document.forms.settings.bStop.focus();
}
function next() {
	// Est-ce que l'image suivante est prechargee ?
	if(next_img.complete) {
		// Oui, alors apres le temps de pause choisi par l'utilisateur, cette image remplacera l'actuelle
		myCounter = setTimeout("launch()", 1000*window.document.settings.tempo.value);
	}
	else {
		// Non, alors on continue d'attendre qu'elle le soit
		myCounter = setTimeout("next()", 250);
	}
}

// Lance le slideshow
function launchFirst() {
	// Petite verification de la temporisation choisie
	if(window.document.settings.tempo.value == "") {
		alert("Précisez une temporisation entre 0 et 60 secondes...");
		return false;
	}

	// Ordre normal ou aleatoire
	if(window.document.settings.order[1].checked) {
		TBLmedia = shuffleArray(tabImgSave);
	}
	else {
		TBLmedia = tabImgSave;
	}

	// Avant de lancer le slideshow, on desactive tous les elements du formulaire
	// et on active le bouton "Stopper"
	next_img.src = rep+TBLmedia[0];
	window.document.forms.settings.bStop.disabled    = false;
	focusOnStop();
	window.document.forms.settings.bLaunch.disabled  = true;
	window.document.forms.settings.repeat.disabled   = true;
	window.document.forms.settings.order[0].disabled = true;
	window.document.forms.settings.order[1].disabled = true;
	window.document.forms.settings.tempo.disabled    = true;
	
	launch();
}

// Poursuit le slideshow
function launch() {
	// Si la fenetre n'existe pas ou est fermee, on la reouvre
	if(!wPopup || wPopup.closed) {
		wPopup = window.open('', 'img_popup', 'status=no, directories=no, toolbar=no, location=no, menubar=no, scrollbars=no, resizable=yes, fullscreen=yes');
	}
	// On ecrit le contenu de la fenetre popup
	wPopup.document.clear();
	wPopup.document.write("<HTML><HEAD><TITLE>Slideshow : "+(num+1)+"/"+nb_img+"</TITLE></HEAD>");
	wPopup.document.write('<SCRIPT language="JavaScript">\nfunction checkSize() { if(document.images && document.images[0].complete) { w = document.images[0].width+50; h = document.images[0].height+100; window.moveTo((screen.width-w)/2, (screen.height-h)/2); document.images[0].style.visibility = "visible"; window.focus(); if(opener.next_img.src != opener.rep+opener.TBLmedia[opener.num]) { opener.next_img.src = opener.rep+opener.TBLmedia[opener.num]; } } else { setTimeout("checkSize()", 250); } }\n</'+'SCRIPT>');
	wPopup.document.write('<BODY bgcolor="#000000" leftMargin="0" topMargin="0" marginWidth="0" marginHeight="0">');
	wPopup.document.write('<table width="100%" height="100%" align="center" cellpadding="0" cellspacing="0"><tr valign="middle"><td align="center"><img src="'+next_img.src+'" border="0" onLoad="checkSize()" onClick="window.opener.stop()" style="visibility:hidden"></td></tr></table>');
	wPopup.document.write('</BODY></HTML>');
	wPopup.document.close();
	num++;

	// On a passé toutes les images, on repart du début
	if(num == nb_img) num = 0;
	
	// Si "Repeter" n'est pas cochee, on stoppe le slideshow
	if(num == 0 && !window.document.settings.repeat.checked) {
		setTimeout("stop()", 1000*window.document.settings.tempo.value);
		return false;
	}

	// En cas de repetition en mode aleatoire, on remelange les images
	if(num == 0 && window.document.settings.order[1].checked) {
		TBLmedia = shuffleArray(tabImgSave);
	}

	next();
}

// Stoppe le slideshow
function stop() {
	clearTimeout(myCounter);
	wPopup.close();
	// On reactive tous les elements du formulaire
	// et on desactive le bouton "Stopper"
	window.document.forms.settings.bLaunch.disabled  = false;
	focusOnLaunch();
	window.document.forms.settings.bStop.disabled    = true;
	window.document.forms.settings.repeat.disabled   = false;
	window.document.forms.settings.order[0].disabled = false;
	window.document.forms.settings.order[1].disabled = false;
	window.document.forms.settings.tempo.disabled    = false;
	num = 0;
}

// Verification de la temporisation a chaque modification de celle-ci
function checkTempo() {
	var t = window.document.settings.tempo.value;
	if(isNaN(t) || t<0 || t>60) {
		window.document.settings.tempo.value = 3;
		alert("Mauvaise temporisation...\nEntrez un temps compris entre 0 et 60 secondes.");
		return false;
	}
}
</script>
<div id="global">
	<div id="header">
		<table>
			<tr>
				<td style="width:185px"><a href="index.php?location=media&amp;action=none" title="Accueil"><img src="img/logo_gcm.png" width="180"  height="43" alt="Home" title="Accueil"></a></td>
				<td style="width:315px"><form method="post" action="index.php?location=media&amp;action=find" name="general_search" id="general_search">
				<input type="text" name="search_string" value="Chercher" ; onfocus="if(this.value=='Chercher') this.value='';" onblur="if(this.value=='') this.value='Chercher';" size="16" maxlength="16" />
				&nbsp;&nbsp;
				<select name="item_name" title="" size="1">
					<option value="all">Partout</option>
					<option value="Audio-mp3">Audio-mp3</option>
					<option value="Image-gif">Image-gif</option>
					<option value="Image-jpg">Image-jpg</option>
					<option value="Image-png">Image-png</option>
					<option value="Vidéo-flv">Vidéo-flv</option>
				</select>
				&nbsp;&nbsp;
				<input type="submit" name="send" value=" Trouver " size="0" maxlength="0" />
				</form>				</td>
				<td style="width:50px"><img src="img/actions/action_browse.png" width="44"  height="24" alt="action_browse" /></td>
				<td style="width:50px"><img src="img/actions/action_add.png" width="44"  height="24" alt="action_add" /></td>
				<td style="width:50px"><img src="img/actions/action_settings.png" width="44"  height="24" alt="action_settings" /></td>
				<td style="width:100px">Diaporama</td>
				<td style="width:50px"><img src="img/actions/action_logout.png" width="44"  height="24" title="Déconnexion" alt="action_logout" onclick="javascript:open_logout();" /></td>
			</tr>
		</table>
	</div><!-- #header -->
	<!-- DEBUT du CONTENU DYNAMIQUE -->
	<div id="container_one">
		<div id="full_content">
			<h1><?php echo $page_title ; ?></h1>
			<table width='750' border='0' align='center' cellspacing='0' cellpadding='0'>
				<tr valign="middle">
					<td width="170" align="center"><img src="<?php echo $first_thumbnail ; ?>" border="0"></td>
					<td width="50">&nbsp;</td>
					<td><!-- debut du formulaire -->
						<form name="settings">					  
							<table align="center" cellpadding="2" cellspacing="0" border="0">
								<tr> 
									<td><p> <input type="radio" name="order" value="normal" checked /> ordre normal&nbsp;&nbsp;&nbsp;<input type="radio" name="order" value="aleatoire" /> ordre aléatoire</p></td>
								</tr>
								<tr> 
									<td><p><input type="checkbox" name="repeat" /> répétition</p></td>
								</tr>
								<tr> 
									<td><p>Temporisation : <input type="text" name="tempo" value="3" size="4" maxlength="2" onKeyUp="checkTempo()" onChange="checkTempo()" /> seconde(s)</p>			</td>
								</tr>
							</table>
							<br />
							<table align="center" cellpadding="2" cellspacing="0" border="0">
								<tr> 
									<td align="center"><input type="button" onClick="launchFirst()" name="bLaunch" value="Lancer"  style="width: 100px" />&nbsp;&nbsp;&nbsp;&nbsp; <input type="button" onClick="stop()" name="bStop" value="Stopper" style="width: 100px" disabled /></td>
								</tr>
							</table>
							<br />
							<div align="center"><?php echo SLIDE_INFO ; ?></div>
						</form><!-- FIN du FORMULAIRE -->
					</td>
					<td width="50">&nbsp;</td>
					<td width="170" align="center"><img src="<?php echo $last_thumbnail ; ?>" border="0" ></td>
				</tr>
			</table>
<?php
function conv_tabjs($tableau, $nomjs, $prempass=true) {
	if($prempass) {
		$taille = count($tableau);
		echo "var ".$nomjs." = new Array(".$taille.");\n";
		foreach($tableau as $key => $val) {
			if(is_string($key)) $key = "'".$key."'";
			conv_tabjs($val, $nomjs."[".$key."]", false);
		}
	} else {
		if(is_array($tableau)) {
			echo($nomjs." = new Array(".count($tableau).");\n");
			foreach($tableau as $key => $val) {
				if(is_string($key)) $key = "'".$key."'";
				conv_tabjs($val, $nomjs."[".$key."]", false);
			}
		} else {
			if(is_string($tableau)) $tableau = "'".addcslashes($tableau,"'")."'";
			echo($nomjs." = ".$tableau.";\n");
		}
	}
}
// fin de page classique
page_footer($footer_lib) ;
?>