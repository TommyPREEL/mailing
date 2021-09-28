<html>
<meta charset="UTF-8">
<head>
  
 <!-- <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js"></script>
  <script>tinymce.init({selector:'textarea'});</script> -->
  
  <script type="text/javascript" src="//cdn.ckeditor.com/4.11.4/full/ckeditor.js"></script>
<script type="text/javascript">
function spoilerCheckbox()
{
var etat = document.getElementById("boxAjouterClient").checked;
var bloc = document.getElementById("divAjouterClient");
if (etat)
divAjouterClient.style.display = "block";
else
divAjouterClient.style.display = "none";
}
</script>
  <script type="text/javascript">
  /* function ConfirmMessage() {
       if (window.confirm("Etes-vous certain de vouloir supprimer le client sélectionné ? Cette action sera irréversible")) 
       {
       	document.gestionClient.confirmSuppr.value = true;
       	return true;
       }
       else
       	return false;
   }*/

/*function spoilerBouton(obj)
{
var divmodif = obj.parentNode.getElementsByTagName("div")[1];
if (divmodif.style.display == "none")
divmodif.style.display = "";
else
divmodif.style.display = "none";
}*/
function verifMail()
{
   var reg = /^(([^<>()[]\.,;:s@]+(.[^<>()[]\.,;:s@]+)*)|(.+))@(([[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}])|(([a-zA-Z-0-9]+.)+[a-zA-Z]{2,}))$/;
 var mail = document.getElementById("ajoutMailClient").value;
    if(reg.test(mail))
      {
      	if()//si ce mail n'existe pas dans la base de donnees
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


<label><h2>GESTION DU MAILING</h2></label>
<hr/>
<form action = "mailtest.php" method = "post">
	<table>
		<tr>
			<td><label>Objet du mail : </label></td>
			<td><input type = "text" name = "object"/></td>
		</tr>
		<tr>
			<td><label>Corps du mail : </label></td>
			<td><textarea name = "body" rows = 50 cols = 150 required></textarea></td>
			<script>CKEDITOR.replace('body');</script>
		</tr>
		<tr>
			<td><input type = "submit" value = "Envoyer"/></td>
		</tr>
		
	</table>
</form>
			<hr/>
			
<form name = "gestionClient" id = "gestionClient" method = "post" action = "ModifierClient.php">

			<label><h2>GESTION DES CLIENTS</h2></label>
			<hr/>
			<table>
			<tr>
			<td><label>Liste actuelle des clients : </label></td>
			<td><select name = "ListeVisiteur">
			<?php
			// Appel du script de connexion 
			include("/includes/bddconnexion.php");

			// Ecriture de la requête d'extraction en SQL 
		    $sql= "SELECT * FROM client ORDER BY mailClient";
			
			// Envoi de la requête : on récupère le résultat dans la variable $result
			$result= $connexion->query($sql);
		     
			 // exécution de la requête et création d’un tableau contenant toutes les lignes de la réponse  
			$ligne = $result->fetchAll();
			
			// Déterminer le nombre de lignes retournées par la requête
			$nbLig=$result->rowCount();

			// Boucle FOR pour parcourir toutes les lignes du jeu d'enregistrements
			for ($i=0;$i<$nbLig;$i++)
			{
				// On attribue une variable à chaque valeur
				$idClient=$ligne[$i]["idClient"];
				$mailClient=$ligne[$i]["mailClient"];

				// On affiche le résultat

				echo "<br/>";
				echo "<option name = 'visiteur[]' value = '$idClient'>".($i+1)." - ".$mailClient."</option>";
			}
			// On ferme le curseur
			$result -> closeCursor();
			// on ferme la connexion
		    $connexion = null;
			?>
			</select></td>

			<td><input type = "submit" name = "Suppression" value = "Supprimer le client" onClick="document.gestionClient.action='SuppressionClient.php';return(window.confirm('Etes-vous certain de vouloir supprimer le client sélectionné ? Cette action sera irréversible'))"/></td>
			
</form>

			<td><input type = "submit" name = "Modification" value = "Modifier le client" onClick = "document.gestionClient.action='ModifierClient.php'"/></td>

	</form>
		</tr>
		<tr>
			<td>Cochez la case pour ajouter un client : </td>
		<td><input type = "checkbox" name = "boxAjouterClient" id = "boxAjouterClient" onClick = 'spoilerCheckbox()' ></td>
		</tr>
	</table>

	<div class="divAjouterClient" name = "divAjouterClient" id = "divAjouterClient" style="display: none;">
	<hr/>
			<form name = "AjoutClient" id = "AjoutClient" method = "post" action = "AjoutClient.php">
			<label><h2>AJOUTER UN CLIENT</h2></label>
			<hr/>
	<table>
		<tr><td><i>Les champs marqués d'un * sont obligatoires</i></td></tr>
		<tr>
			<td>Nom du client : </td>
			<td><input type = "text" name = "ajoutNomClient"/></td>
		</tr>
		<tr>
			<td>Prénom du client :</td>
			<td><input type = "text" name = "ajoutPrenomClient"/></td>
		</tr>
		<tr>
			<td>Adresse e-mail du client : *</td>
			<td><input type = "text" name = "ajoutMailClient" id = "ajoutMailClient" required/></td>
		</tr>
		<tr><td><input type = "submit" value = "Ajouter le client" name="ajoutClient"/></td></tr>
	</table>
	</form>
	</div>
</html>