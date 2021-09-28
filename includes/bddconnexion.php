<?php
	// Définitions de constantes pour la connexion à MySQL
	$hote="127.0.0.1";
	$login="root";
	$mdp="root";
	$nombd="infocom";

	// Connection au serveur
	try {
			$connexion = new PDO("mysql:host=$hote;dbname=$nombd",$login,$mdp);
	} catch ( Exception $e ) {
		  die("\nConnexion à '$hote' impossible : ".$e->getMessage());
	}
?>