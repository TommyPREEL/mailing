<?php

$nom = $_POST['modifNomClient'];
$prenom = $_POST['modifPrenomClient'];
$mail = $_POST['modifMailClient'];
$id = $_POST['idClient'];

	// Appel du script de connexion 
	include("/includes/bddconnexion.php");

	$sqlupdate = "UPDATE client SET nomClient = '".$nom."', prenomClient = '".$prenom."', mailClient = '".$mail."' WHERE idClient = '".$id."'";
			
	// Envoi de la requête : on récupère le résultat dans la variable $result
	$resultupdate= $connexion->exec($sqlupdate);
	header("Location: ./index.php");
	
?>