<?php

$nom = $_POST['ajoutNomClient'];
$prenom = $_POST['ajoutPrenomClient'];
$mail = $_POST['ajoutMailClient'];

	// Appel du script de connexion 
	include("/includes/bddconnexion.php");

	if(($nom != '') && ($prenom != ''))
	{
		$sqladd = "INSERT INTO client (nomClient, prenomClient, mailClient) VALUES ('".$nom."', '".$prenom."', '".$mail."')";
	}
	else
	{
		if($nom != '')
		{
			$sqladd = "INSERT INTO client (nomClient, mailClient) VALUES ('".$nom."', '".$mail."')";
		}
		else
		{
			if($prenom != '')
			{
				$sqladd = "INSERT INTO client (prenomClient, mailClient) VALUES ('".$prenom."', '".$mail."')";
			}
			else
			{
				$sqladd = "INSERT INTO client (mailClient) VALUES ('".$mail."')";
			}
		}
	}
			
			// Envoi de la requête : on récupère le résultat dans la variable $result
			$result= $connexion->exec($sqladd);
			echo $sqladd;
			header("Location: ./index.php");
	
?>