<html>
<meta charset="UTF-8"> <!-- format spécifique pour un bon affichage HTML -->
<head>
<script type = "text/javascript">
function showSpoiler(obj) //fonction pour afficher un bloc quand un objet est sur "on"
{
var inner = obj.parentNode.getElementsByTagName("div")[0];
if (inner.style.display == "none")
inner.style.display = "";
else
inner.style.display = "none";
}
</script>
</head>
<?php

if (file_exists("finEnvoiMail.txt")) // si le fichier qui confirme la fin des envois du dernier mailing
    {
    if (file_exists("finEnvoiMail.txt"))		
    	unlink("finEnvoiMail.txt");			// on supprime 
    if (file_exists("mailIncorrect.txt"))
    	unlink("mailIncorrect.txt");			
    if (file_exists("numeroMail.txt"))		// les fichiers
    	unlink("numeroMail.txt"); 						
	if (file_exists("nombreEnvoi.txt"))
		unlink("nombreEnvoi.txt");			// de sauvegarde
	if (file_exists("EnvoiOK.txt"))
		unlink("EnvoiOK.txt");					
}

use PHPMailer\PHPMailer\PHPMailer; // on donne des
use PHPMailer\PHPMailer\SMTP; 	   // allias aux
use PHPMailer\PHPMailer\Exception; // différentes classes

require_once('/includes/PHPMailer-master/src/PHPMailer.php'); // on inclut
require_once('/includes/PHPMailer-master/src/SMTP.php'); // les libraries
require_once('/includes/PHPMailer-master/src/Exception.php'); // de PHPMailer

$mail = new PHPMailer; // On crée un objet

$mail->isSMTP(); // Paramétrer le Mailer pour utiliser SMTP 
$mail->Host       = 'smtp.gmail.com'; // Spécifier le serveur SMTP
$mail->SMTPAuth   = true; // Activer authentication SMTP
$mail->Username   = 'votre_mail'; // Votre adresse email d'envoi
$mail->Password   = 'votre_mdp'; // Le mot de passe de cette adresse email
$mail->SMTPSecure = 'tls'; // Accepter SSL/TLS
$mail->Port       = 587; // Port d'identification: 465 pour SSL et 587 pour TLS

if (isset($_POST["object"])) //si un objet de mail a été saisi
    $object = $_POST["object"]; //on récupère la valeur de l'objet puis on donne cette valeur à la variable object
else
    $object = ''; //sinon object vaut un vide

if (isset($_POST["body"])) //si un corps de mail a été saisi
    $body = $_POST["body"]; //on récupère la valeur du corps puis on donne cette valeur à la variable body
else
    $body = ' '; //sinon body vaut un vide

$mail->setFrom('votre_mail', 'Test'); // Personnaliser l'envoyeur

// Appel du script de connexion 
include("/includes/bddconnexion.php");
// Ecriture de la requête d'extraction en SQL 
$sql = "SELECT * FROM client ORDER BY idClient";

// Envoi de la requête : on récupère le résultat dans la variable $result
$result = $connexion->query($sql);

// exécution de la requête et création d’un tableau contenant toutes les lignes de la réponse
$ligne = $result->fetchAll();

// Déterminer le nombre de lignes retournées par la requête
$nbLig = $result->rowCount();

$fichier = fopen("numeroMail.txt", "a+"); // $fichier ouvre/crée le fichier "numeroMail.txt" en plaçant le curseur à la fin du fichier
$i       = fgets($fichier); // $i vaut la valeur dans le fichier
fclose($fichier); // fermeture du fichier

$fichierNombreEnvoi = fopen("nombreEnvoi.txt", "a+"); // $fichierNombreEnvoi ouvre/crée le fichier "nombreEnvoi.txt" en plaçant le curseur à la fin du fichier
$nombreEnvoi        = fgets($fichierNombreEnvoi); // $nombreEnvoi vaut la valeur dans le fichier
if ($nombreEnvoi == "") // si $nombreEnvoi ne vaut rien
    {
    $nombreEnvoi == 0; // on lui donne la valeur de 0
    fwrite($fichierNombreEnvoi, $nombreEnvoi); // on écrit ce 0 dans le fichier
}
fclose($fichierNombreEnvoi); // on ferme le fichier "nombreEnvoi.txt"

// Boucle while pour parcourir toutes les lignes du jeu d'enregistrements
while ($i < $nbLig) // 1ere boucle avec le numeroMail afin de récupérer la sauvegarde si le script a été coupé.
    {
    $fichier = fopen("numeroMail.txt", "a+"); // $fichier ouvre/crée le fichier "numeroMail.txt" en plaçant le curseur à la fin du fichier
    $i       = fgets($fichier); // $i vaut la valeur dans le fichier
    fclose($fichier); // fermeture du fichier
    if ($i < $nbLig) // si le numéro de mail est inférieur au nombre de lignes retournées de la base de données
        {
        // On attribue une variable à chaque valeur
        $destinataire = $ligne[$i]["mailClient"];
        $destinataire = htmlspecialchars($destinataire); // transformation des caractères spéciaux de la variable $destinataire
        if (preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#i", $destinataire)) //si le format de mail est correct
            {
            $fichierEnvoiOK = fopen("EnvoiOK.txt", "a+"); // $fichierEnvoiOk ouvre/crée le fichier "EnvoiOK.txt" en plaçant le curseur à la fin du fichier
            $nbLignes       = count(file("EnvoiOK.txt")); // On compte le nombre de lignes des mails ayant un format correct
            $compteLigne    = 1; // $compteLigne vaut 1 (variable de test de la boucle while)
            $existe         = false; // $existe est à faux (en cas de doublon)
            while (($compteLigne <= $nbLignes) && ($existe == false)) // tant que $compteLigne (varaible de test) est inférieur au nombre de lignes du fichier "EnvoiOK.txt" et que $destinataire est unique dans le fichier
                {
                $mailEnvoye = chop(fgets($fichierEnvoiOK)); // On récupère ligne par ligne (à chaque tour de boucle) la valeur de cette ligne qu'on met dans $mailEnvoye
                if ($mailEnvoye == $destinataire) // si le mail existe déjà
                    {
                    $existe = true; // $existe vaut true, on sort donc de la boucle
                } else
                    $compteLigne++; // sinon on augmente le compteur
            }
            if ($existe == false) // si le mail du $destinataire est unique
                {
                fclose($fichierEnvoiOK); // On ferme le fichier "EnvoiOK.txt"
                $nom    = $ligne[$i]["nomClient"]; // $nom vaut la valeur de nomClient dans la base de données
                $prenom = $ligne[$i]["prenomClient"]; // $prenom vaut la valeur de prenomClient dans la base de données
                if ($nom == null) // si il n'y a pas de nom
                    $nom = ""; // nom vaudra un vide
                if ($prenom == null) // si il n'y a pas de prénom
                    $prenom = ""; // prénom vaudra un vide
                
                $mail->addAddress($destinataire, $nom . ' ' . $prenom); // Ajouter le destinataire
                //$mail->addReplyTo('info@example.com', 'Information'); // L'adresse de réponse
                //$mail->addCC('cc@example.com');
                //$mail->addBCC('bcc@example.com');
                //$mail->addAttachment('telephone.jpg'); // Ajouter une pièce jointe 
                $mail->isHTML(true); // Paramétrer le format des emails en HTML ou non
                $mail->Subject = $object; // Ajouter un objet
                $mail->Body = $body; // Ajouter le corps
                //$mail->AltBody = $body; //'Corps brut pour les clients de messagerie n'interpretant pas le HTML;
                //$mail->SMTPDebug = 2; // Voir les transferts de données (0 pour tout cacher) plus le chiffre est haut, plus les détails sont importants
                
                $date = date("d/m/Y à H:i:s"); // $date vaut la date de l'envoi du mail
                if (!$mail->Send()) // Si on ne peut pas envoyer le mail
                    {
                    echo "Erreur: " . $mail->ErrorInfo; // afficher l'erreur en question
                } else // si l'envoi s'effectue
                    {
                    echo "Mail n°" . ($nombreEnvoi + 1) . " envoyé à " . $destinataire . " => le $date<BR><BR>"; // On affiche le numéro du mail depuis le lancement du script, le destinataire et la date d'envoi
                    
                    $fichier    = fopen("numeroMail.txt", "w+"); // $fichier ouvre/crée le fichier "numeroMail.txt" en écrasant tout le fichier
                    $numeroMail = $i + 1; // On ajoute 1 à la valeur précédente du fichier
                    fwrite($fichier, $numeroMail); // On écrit cette valeur dans le fichier
                    fclose($fichier); // Fermeture du fichier
                    $fichierNombreEnvoi = fopen("nombreEnvoi.txt", "w+"); // $fichierNombreEnvoi ouvre/crée le fichier "nombreEnvoi.txt" en écrasant tout le fichier
                    $nombreEnvoi        = $nombreEnvoi + 1; // On ajoute 1 à la valeur précédente du fichier
                    fwrite($fichierNombreEnvoi, $nombreEnvoi); // On écrit cette valeur dans le fichier
                    fclose($fichierNombreEnvoi); // Fermeture du fichier
                    $fichierEnvoiOK = fopen("EnvoiOK.txt", "a+"); // $fichierEnvoiOk ouvre/crée le fichier "EnvoiOK.txt" en plaçant le curseur à la fin du fichier
                    $mailEnvoye     = $destinataire; // $mailEnvoye vaut $destinataire
                    fwrite($fichierEnvoiOK, $mailEnvoye . "\n"); // On écrit cette valeur dans le fichier puis on saute une ligne
                    fclose($fichierEnvoiOK); // fermeture du fichier
                    $mail->clearAddresses(); // On supprime le carnet d'adresses du mail
                }
                sleep(1); // On "endort" le programme pour ne pas dépasser la limite d'envoi des boites mail
            } else // si le mail du destinataire entré est un doublon
                {
                $mailIncorrect = fopen("mailIncorrect.txt", "a+"); // $mailIncorrect ouvre/crée le fichier "mailIncorrect.txt" en plaçant le curseur à la fin du fichier
                fwrite($mailIncorrect, $destinataire . " ligne n°" . ($i + 1) . " : doublon\n"); // On écrit dans le fichier le mail en doublon, sa ligne et le fait que c'est un doublon
                fclose($mailIncorrect); // On ferme le fichier
                $fichier    = fopen("numeroMail.txt", "w+"); // $fichier ouvre/crée le fichier "numeroMail.txt" en écrasant le contenu du fichier
                $numeroMail = $i + 1; // on ajoute 1 à la valeur précédente du fichier
                fwrite($fichier, $numeroMail); // On écrit cette valeur dans le fichier
                fclose($fichier); // fermeture du fichier
            }
        } else // si le mail du destinataire a un format incorrect
            {
            $mailIncorrect = fopen("mailIncorrect.txt", "a+"); // $mailIncorrect ouvre/crée le fichier "mailIncorrect.txt" en plaçant le curseur à la fin du fichier
            fwrite($mailIncorrect, $destinataire . " ligne n°" . ($i + 1) . " : format incorrect\n"); // On écrit dans le fichier le mail au format incorrect, sa ligne et le fait que le format est incorrect
            fclose($mailIncorrect); // On ferme le fichier
            $fichier    = fopen("numeroMail.txt", "w+"); // $fichier ouvre/crée le fichier "numeroMail.txt" en écrasant le contenu du fichier
            $numeroMail = $i + 1; // on ajoute 1 à la valeur précédente du fichier
            fwrite($fichier, $numeroMail); // On écrit cette valeur dans le fichier
            fclose($fichier); // fermeture du fichier
        }
    } // si le numéro de mail supérieur ou égale aux nombres de lignes de la base de données
} // même condition, pour le fichier de sauvegarde
$fichierNombreEnvoi = fopen("nombreEnvoi.txt", "a+"); // $fichierNombreEnvoi ouvre/crée le fichier "nombreEnvoi.txt" en plaçant le curseur à la fin du fichier
$nombreEnvoi        = fgets($fichierNombreEnvoi);	 // $nombreEnvoi vaut la valeur du fichier
if (file_exists("mailIncorrect.txt")) // si le fichier mailIncorrect.txt existe
    $nombreNonEnvoi = count(file("mailIncorrect.txt")); // on compte le nombre de lignes de ce fichier
else // si mailIncorrect n'existe pas
    $nombreNonEnvoi = 0; // $nombreNonEnvoi vaut 0
echo "Fin des envois: <br> \t- " . $nombreEnvoi . " mails envoyés <br> \t- "; // affichages du nombre de mails envoyés
if ($nombreNonEnvoi != 0){ // si des mails n'ont pas été envoyés
    $ligneNonEnvoi = 0; // $ligneNonEnvoi vaut 0 (valeur test pour chaque ligne)
    echo $nombreNonEnvoi . " mails non envoyés <br/>"; // afficher le nombre de mails non envoyés
    echo '<input onclick="showSpoiler(this);" value="Afficher/Cacher la liste d\'erreurs" type="button">'; // Création d'un bouton pour afficher ou cacher la liste d'erreur
    echo '<div class="inner" style="display: none;">'; // Début du bloc à afficher/cacher
    $fichierMailIncorrect = fopen("mailIncorrect.txt", "r"); // $mailIncorrect ouvre le fichier "mailIncorrect.txt" en lecture
    while ($ligneNonEnvoi < $nombreNonEnvoi) { // tant que le curseur n'est pas à la fin du fichier
        $messageErreur = fgets($fichierMailIncorrect); // On récupère la valeur de chaque ligne du fichier "MailIncorrect.txt"
        $ligneNonEnvoi++; // on augmente le compteur de ligne de 1
        echo $messageErreur . "<br/>"; // On affiche le message d'erreur pour chaque mail incorrect
    }
    fclose($fichierMailIncorrect); // fermeture du fichier
    echo '</div>'; // fin du bloc à afficher/cacher
} else { // si aucun mail n'est incorrect
    echo $nombreNonEnvoi . " mail non envoyé"; // on affiche 0 mail non envoyé
}
echo "<br/>"; // saut de ligne
echo "Suppression des fichiers de sauvegarde..."; // On affiche que l'on supprime les sauvegarde
fclose($fichierNombreEnvoi);
$finEnvoiMail = fopen("finEnvoiMail.txt", "a+"); // On crée le fichier "finEnvoiMail.txt" pour prouver la fin de l'envoi des mails
fclose($finEnvoiMail); // fermeture du fichier
unlink("numeroMail.txt"); 						// on supprime 
unlink("nombreEnvoi.txt");						// les fichiers
unlink("EnvoiOK.txt");							// de sauvegarde
?>
</html>