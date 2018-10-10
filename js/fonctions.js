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
function delete_warning(url){
	window.open(url, "yes","height=200, width=400,resizable=0,menubar=0,toolbar=0,location=0,directories=0,scrollbars=0,status=0")
	setTimeout("self.close();",5000)
}
function edit_popup(url) {
	window.open(url, '','width=580, height=500, top='+(screen.height-500)/2+', left='+(screen.width-580)/2+', resizable=0, menubar=0, toolbar=0, location=0, directories=0, scrollbars=0, status=0');
}
function exif_popup(url) {
	window.open(url, '','width=390, height=330, top='+(screen.height-330)/2+', left='+(screen.width-390)/2+', resizable=0, menubar=0, toolbar=0, location=0, directories=0, scrollbars=0, status=0');
}
function get_help(url){
	window.open(url, "help","height=400, width=500,resizable=0,menubar=1,toolbar=0,location=0,directories=0,scrollbars=1,status=0") 
}
function poster(url, largeur, hauteur) {
	window.open(url, '', 'width='+largeur+', height='+hauteur+', top='+(screen.height-hauteur)/2+', left='+(screen.width-largeur)/2+', status=0, directories=0, toolbar=0, location=0, menubar=0, scrollbars=0, resizable=1');
}

function refreshParent(url) {
	window.opener.location.href = url;
	if (window.opener.progressWindow) {
		window.opener.progressWindow.close()
	}
	window.close();
}

function open_logout() {
	width = 300;
	height = 120;
	if (window.innerWidth) {
		var left = (window.innerWidth-width)/2;
		var top = (window.innerHeight-height)/2;
	} else {
		var left = (document.body.clientWidth-width)/2;
		var top = (document.body.clientHeight-height)/2;
	}
	window.open('index.php?location=admin&action=logout','logout_popup','menubar=no, scrollbars=no, top='+top+', left='+left+', width='+width+', height='+height+'');
}