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

class ceckdb {
	// Partie privée : propriétés
	var $connexion ;
	// Erreurs
	var $error_code = 0 ;

	// Constructeur de la classe ceckdb
	function ceckdb ($server, $login, $password, $base) {
		// Initialisations

		// Connexion au serveur 
		$this->connexion = @mysql_pconnect ($server, $login, $password) ;
		if (!$this->connexion) {
			$this->error_message ("Problème de connexion au serveur : " . $server . "<br />") ;
			$this->error_code = 1 ;
		}
		// Connexion à la base
		if (!mysql_select_db ($base, $this->connexion)) {
			$this->error_message ("Problème d'accès à	la base : " . $base) ;
			if ($this->connexion) $this->error_message ("MySQL retourne : " . mysql_error ($this->connexion)) ;
			$this->error_code = 1 ;
		}
	}
	// Fin du constructeur

	// Partie privee : méthode pour afficher les messages
	function error_message ($error_message) {
		echo "<p><strong><span class=\"rouge\">Erreur : </span></strong>" . $error_message . "</p>" ;
	}
	// #################################
	// ###		Partie publique		###
	// #################################
	// méthode d'execution d'une requete
	function query ($request) {
		$result = @mysql_query ($request, $this->connexion) ;
		if (!$result) {
			$this->error_message ("Problème d'exécution de la requête : " . $request ."<br />") ;
			$this->error_message ("MySQL retourne : " . mysql_error ($this->connexion) . "<br />") ;
		} else return $result ;
	}
	// accès a la prochaine ligne - retour objet
	function fetch_object ($result) {
		return mysql_fetch_object  ($result) ;
	}
	// accès a la prochaine ligne - retour tableau associatif
	function fetch_assoc ($result) {
		return mysql_fetch_assoc  ($result) ;
	}
	// accès a la prochaine ligne - retour objet
	function fetch_row ($result) {
		return mysql_fetch_row  ($result) ;
	}
	// Preparation des donnes avant insertion
	function real_escape_string ($string) {
		return mysql_real_escape_string ($string) ;
	}
	// charset de communication php <> mysql
	function listen_talk ($string) {
		return mysql_set_charset ($string) ;
	}
	// méthodes decrivant le resultat d'une requete
	function num_rows ($result) {
		return mysql_num_rows  ($result) ;
	}
	function num_fields ($result) {
		return mysql_num_fields  ($result) ;
	}
	function field_name ($result, $position) {
		return mysql_field_name ($result, $position) ;
	}
	// méthode indiquant si une erreur s'est produite
	function is_error () {
		return $this->error_message ;
	}
	// ########################
	// Destructeur de la classe
	function __destruct () {
		@mysql_close ($this->connexion) ;
	}
// Fin de la classe
}
?>