<html>
<meta charset="UTF-8">
<head>
<script type="text/javascript">
	function verifMail()
{
   var reg = /^(([^<>()[]\.,;:s@]+(.[^<>()[]\.,;:s@]+)*)|(.+))@(([[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}])|(([a-zA-Z-0-9]+.)+[a-zA-Z]{2,}))$/;
 var mail = document.getElementById("modifMailClient").value;
    if(reg.test(mail))
      {
		return true;
      }
    else
      {
		return false;
      }
}
function verifForm()
{
   var mailOk = verifMail();
   
   if(mailOk)
      return true;
   else
   {
      alert("Veuillez entrez une adresse email correcte");
      return false;
   }
}
</script>
</head>
<?php

// Appel du script de connexion 
include("/includes/bddconnexion.php");

// Ecriture de la requête d'extraction en SQL 
$sql = "SELECT * FROM client WHERE idClient = '" . $_POST['ListeVisiteur'] . "'";

// Envoi de la requête : on récupère le résultat dans la variable $result
$result = $connexion->query($sql);

// exécution de la requête et création d’un tableau contenant toutes les lignes de la réponse  
$ligne = $result->fetch();

// On attribue une variable à chaque valeur
$idClientSelect     = $ligne["idClient"];
$nomClientSelect    = $ligne["nomClient"];
$prenomClientSelect = $ligne["prenomClient"];
$mailClientSelect   = $ligne["mailClient"];

?>
<hr/>

<form name = "modificationClient" id = "modificationClient" method = "post" action = "ModificationClient.php" onSubmit = "return verifForm()">

<label><h2>MODIFIER UN CLIENT</h2></label>
<hr/>
	<table>
		<tr><td><i>Les champs marqués d'un * sont obligatoires</i></td></tr>
		<tr>
			<td>Nom du client : </td>
			<td><input type = "text" name = "modifNomClient" value = <?php
echo '"' . $nomClientSelect . '"';
?>/></td>
		</tr>
		<tr>
			<td>Prénom du client :</td>
			<td><input type = "text" name = "modifPrenomClient" value = <?php
echo '"' . $prenomClientSelect . '"';
?>/></td>
		</tr>
		<tr>
			<td>Adresse e-mail du client : *</td>
			<td><input type = "text" name = "modifMailClient" id = "modifMailClient" value = '<?php
echo $mailClientSelect;
?>' onblur = "verifMail()" /></td>
		</tr>
		<tr><td><input type = "hidden" name = "idClient" id = "idClient" value = <?php
echo '"' . $idClientSelect . '"';
?>/><input type = "submit" value = "Enregistrer les modifications" name="modifClient"/></td>
		</form>
		<form name = "annuleModificationClient" id = "annuleModificationClient" method = "post" action = "index.php">
		<td><input type = "submit" value = "Annuler les modifications" name="annuleModifClient"/></td>
		</tr>
	</table>
</form>
</html>