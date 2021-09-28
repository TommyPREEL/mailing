<?php
	// Appel du script de connexion 
	include("/includes/bddconnexion.php");
	$sqldelete = "DELETE FROM client WHERE idClient = '".$_POST['ListeVisiteur']."'";
	// Envoi de la requête : on récupère le résultat dans la variable $result
	$resultdelete = $connexion->exec($sqldelete);
	// on ferme la connexion
	$connexion = null;
	header("Location: ./index.php");
	
?>