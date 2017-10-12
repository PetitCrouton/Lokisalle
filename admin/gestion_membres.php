<?php
require("../inc/init.inc.php");

// restriction d'acces, si l'utilisateur n'est pas admin alors il ne doit pas accéder à la page
if(!utilisateur_est_admin())
{
  header("location:../connexion.php");
  exit(); //permet d'arrêter l'exécution du script au cas où une personne malveillante ferait des injections via GET
}

// mettre en place un controle pour savoir si l'utilisateur veut une suppression d'un membre
if(isset($_GET['action']) && $_GET['action'] == 'suppression' && !empty($_GET['id_membre']) && is_numeric($_GET['id_membre']))
{
	// is_numeric permet de savoir si l'information est bien une valeur numérique sans tenir compte de son type (les informations provenant de GET et de POSt sont toujours de type string)
	
	// on fait une requete pour récupérer les informations du membre 
	$id_membre = $_GET['id_membre'];
	$membre_a_supprimer = $pdo->prepare("SELECT * FROM membre WHERE id_membre = :id_membre");
	$membre_a_supprimer->bindParam(":id_membre", $id_membre, PDO::PARAM_STR);
	$membre_a_supprimer->execute();
	
	$membre_a_suppr = $membre_a_supprimer->fetch(PDO::FETCH_ASSOC);

	$suppression = $pdo->prepare("DELETE FROM membre WHERE id_membre = :id_membre");	
	$suppression->bindParam(":id_membre", $id_membre, PDO::PARAM_STR);
	$suppression->execute();
	$message .= '<div class="alert alert-success" role="alert" style="margin-top: 20px;">Le membre numéro ' . $id_membre . ' a bien été supprimé</div>';
	
	// on bascule sur l'affichage du tableau
	$_GET['action'] = 'affichage';
	
}

//*******************************************************
//                  VARIABLES VIDES
//*******************************************************
// Déclaration de variables vides pour affichage dans les values du formulaire
$id_membre = "";
$pseudo = "";
$mdp = "";
$nom = "";
$prenom = "";
$email = "";
$civilite = "";
$statut = "";
$date_enregistrement = ""; 

// variable erreur
$erreur="";

//*******************************************************
// RECUPERATION DES INFORMATIONS D'UN MEMBRE A MODIFIER
//*******************************************************
if(isset($_GET['action']) && $_GET['action'] == 'modification' && !empty($_GET['id_membre']) && is_numeric($_GET['id_membre']))
{
	$id_membre = $_GET['id_membre'];
	$membre_a_modif = $pdo->prepare("SELECT * FROM membre WHERE id_membre = :id_membre");
	$membre_a_modif->bindParam(":id_membre", $id_membre, PDO::PARAM_STR);
	$membre_a_modif->execute();
	$membre_actuel = $membre_a_modif->fetch(PDO::FETCH_ASSOC);
	
	$id_membre = $membre_actuel['id_membre'];
	$pseudo = $membre_actuel['pseudo'];
	$mdp = $membre_actuel['mdp'];
	$nom = $membre_actuel['nom'];
	$prenom = $membre_actuel['prenom'];
	$email = $membre_actuel['email'];
	$civilite = $membre_actuel['civilite'];
	$statut = $membre_actuel['statut'];
	$date_enregistrement = $membre_actuel['date_enregistrement'];
	
	
}

// controle sur tous les champs provenant du formulaire (sauf bouton validation) pour voir si ils existent
if(isset($_POST['id_membre']) && isset($_POST['pseudo']) && isset($_POST['mdp']) && isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['email']) && isset($_POST['civilite']) && isset($_POST['statut']))
{
    $id_membre = $_POST['id_membre'];
	$pseudo = $_POST['pseudo'];
	$mdp = $_POST['mdp'];
	$nom = $_POST['nom'];
	$prenom = $_POST['prenom'];
	$email = $_POST['email'];
	$civilite = $_POST['civilite'];
	$statut = $_POST['statut'];

  // Contrôle anti doublons sur la référence si on est dans le cas d'un ajout car lors de la modification la refernce existera touojurs
  $verif_doublon_pseudo = $pdo->prepare("SELECT * FROM membre WHERE pseudo = :pseudo");
  $verif_doublon_pseudo->bindParam(":pseudo", $pseudo, PDO::PARAM_STR);
  $verif_doublon_pseudo->execute();

  if($verif_doublon_pseudo->rowCount() > 0 && isset($_GET['action']) && $_GET['action'] == 'ajout' ) 
  // rq on fait un rowCount car un pdostatement ne retourne false que si on a fait une erreur dans la requete
  {
      $message .= '<div class="alert alert-danger" role="alert" style="margin-top: 20px;">Attention! Ce pseudo existe déjà, choisissez un autre pseudo !</div>';
      $erreur = true;
  }

  // on vérifie que le pseudo n'est pas vide
  if(empty($pseudo))
  {
    $message .= '<div class="alert alert-danger" role="alert" style="margin-top: 20px;">Attention! Le pseudo est obligatoire!</div>';
    $erreur = true;
  }

  
// Insertion d'un membre dans la base de données
if(!$erreur) // s'il n'y a pas d'erreurs
{
    // pour crypter (par hachage) le mdp
    // $mdp = password_hash($mdp, PASSWORD_DEFAULT);
    // pour voir la gestion du mdp lors de la connexion, voir le fichier connexion_avec_mdp_hash.php à récuperer de Mathieu
    if(isset($_GET['action']) && $_GET['action'] == 'ajout')
    {
    $enregistrement = $pdo->prepare("INSERT INTO membre (pseudo, mdp, nom, prenom, email, civilite, statut, date_enregistrement) VALUES (:pseudo, :mdp, :nom, :prenom, :email, :civilite, :statut, NOW())");
    }
    elseif(isset($_GET['action']) && $_GET['action'] == 'modification'){
      $enregistrement = $pdo->prepare("UPDATE membre SET pseudo = :pseudo, mdp = :mdp, nom = :nom, prenom = :prenom, email = :email, civilite = :civilite, statut = :statut WHERE id_membre = :id_membre");
      $id_membre = $_POST['id_membre'];
      $enregistrement->bindParam(":id_membre", $id_membre, PDO::PARAM_STR);
    }

    $enregistrement->bindParam(":pseudo", $pseudo, PDO::PARAM_STR);
    $enregistrement->bindParam(":mdp", $mdp, PDO::PARAM_STR);
    $enregistrement->bindParam(":nom", $nom, PDO::PARAM_STR);
    $enregistrement->bindParam(":prenom", $prenom, PDO::PARAM_STR);
    $enregistrement->bindParam(":email", $email, PDO::PARAM_STR);
    $enregistrement->bindParam(":civilite", $civilite, PDO::PARAM_STR);
    $enregistrement->bindParam(":statut", $statut, PDO::PARAM_STR);
    $enregistrement->execute();

    $message .= '<div class="alert alert-success" role="alert" style="margin-top: 20px;">Le membre ' . $pseudo . ' a bien été enregistré</div>';
}

} // fin de if isset

// c'est à partir de la ligne suivante que commencent les affichages dans la page
require("../inc/header.inc.php"); 
require("../inc/nav.inc.php");    
//echo '<pre>'; print_r($_POST) ; echo '</pre>';
//echo '<pre>'; print_r($_FILES) ; echo '</pre>';
?>
    <!-- Custom styles for this template -->
    <link href="<?php echo URL; ?>css/style.css" rel="stylesheet">
    </head>

    <body>
    <div class="container">

      
      <div class="starter-template">
        <h1><span class="glyphicon glyphicon-download-alt" style="color:plum;"></span>Enregistrement d'un membre</h1>
        <hr/>
        <a href="?action=ajout" class="btn btn-warning">Ajouter un membre</a>
        <a href="?action=affichage" class="btn btn-info">Afficher les membres</a>

       

        <!-- ##### PHP ##### -->
        <?php //echo $message; // messages destinés à l'utilisateur?>
        <?= $message;?> <!--Raccourci pour faire un echo, égal à la ligne au-dessus-->
      </div>

      
        
        <?php

        if(isset($_GET['action']) && $_GET['action'] == 'affichage')
        {
          $resultat = $pdo->query("SELECT * FROM membre");
          echo 'Nombre de membres présents dans la base:' . " " .$resultat->rowCount();
          
          echo '<hr>';
          echo '<div class="row">';
          echo '<div class="col-sm-12">';

        // balise d'ouverture du tableau
        echo '<table border="1" style="width: 80%; margin: 0 auto; border-collapse: collapse; text-align: center;">';
        // premiere ligne du tableau pour le nom des colonnes
        echo '<tr>';
        // récupération du nombre de colonnes dans la requete:
        $nb_col = $resultat->columnCount();

        // création des variables modifier et supprimer
        $modifier = 'Modifier';
        $supprimer = 'Supprimer';

        for($i = 0; $i < $nb_col; $i++)
        {
            //echo '<pre>'; echo print_r($resultat->getColumnMeta($i)); echo '</pre>'; echo '<hr/>';
            $colonne = $resultat->getColumnMeta($i); // on récupére les informations del a colonne en cours afin ensuite de demander le name
            echo '<th style="padding: 10px;">' . $colonne['name'] . '</th>';
        }
        
            echo '<th style="padding: 10px;">' . $modifier . '</th>';
            echo '<th style="padding: 10px;">' . $supprimer . '</th>';

        echo '</tr>';

        while($ligne = $resultat->fetch(PDO::FETCH_ASSOC))
        {
            echo '<tr>';

            foreach($ligne AS $indice => $info)
            {
              
                echo '<td style="padding: 10px;">' . $info . '</td>';
                
            }

            echo '<td><a href="?action=modification&id_membre=' . $ligne['id_membre'] . '" class="btn btn-warning"><span class="glyphicon glyphicon-refresh"></span></td>';

            echo '<td><a onclick="return(confirm(\'Etes vous sûr\'));" href="?action=suppression&id_membre=' . $ligne['id_membre'] . '" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></a></td>';

            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';
        echo '</div>';
        }
         
        ?>

        <?php 
        // affichage du formulaire d'enregistrement
        if(isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification')) { ?>  


      <div class="row">
        <div class="col-sm-4 col-sm-offset-4">
            <!--En cas de pièce jointe, on met type file et on ajoute enctype="multipart/form-data"-->
          <form action="" method="post" enctype="multipart/form-data">

            <div class="form-group">
            <!--id_article caché (hidden) et sans label-->
                <input type="hidden" name="id_membre" id="id_membre" class="form-control" value="<?php echo $id_membre;?>" />
            </div>

            <div class="form-group">
                <span style="color: red; font-size: 11px; margin-bottom:20px;" class="glyphicon glyphicon-asterisk">(Champs obligatoires)</span><br/>
                <label for="pseudo">Pseudo<span style="color: red; font-size: 11px;" class="glyphicon glyphicon-asterisk"></span></label>
                <input type="text" name="pseudo" id="pseudo" class="form-control" value="<?php echo $pseudo;?>" />
            </div>

            <div class="form-group">
                <label for="mdp">Mot de passe</label>
                <input type="text" name="mdp" id="mdp" class="form-control" value="<?php echo $mdp;?>" />
            </div>

            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" name="nom" id="nom" class="form-control" value="<?php echo $nom;?>" />
            </div>

            <div class="form-group">
                <label for="prenom">Prenom</label>
                <input type="text" name="prenom" id="prenom" class="form-control" value="<?php echo $prenom;?>" />
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" name="email" id="email" class="form-control" value="<?php echo $email;?>" />
            </div>

            <div class="form-group">
                <label for="civilite">Civilité</label>
                <select name="civilite" id="civilite" class="form-control" >
                    <option value="m">Homme</option>
                    <option value="f" <?php if($civilite == 'f') { echo 'selected'; } ?> >Femme</option>
                </select>
            </div>

            <div class="form-group">
                <label for="statut">Statut</label>
                <select name="statut" id="statut" class="form-control" >
                    <option value="0">Membre</option>
                    <option value="1" <?php if($statut == '1') { echo 'selected'; } ?> >Admin</option>
                </select>
            </div>


              <div class="form-group">
                <button class="form-control btn btn-info"><span class="glyphicon glyphicon-star" style="color: red;"></span><span class="glyphicon glyphicon-star" style="color: red;"></span><span class="glyphicon glyphicon-star" style="color: red;"></span>Enregistrer<span class="glyphicon glyphicon-star" style="color: red;"></span><span class="glyphicon glyphicon-star" style="color: red;"></span><span class="glyphicon glyphicon-star" style="color: red;"></span></button>
              </div>

          </form>
        </div><!-- /.col-sm-4 -->
      </div><!-- /.row -->

      <?php
      } // fermeture de est-ce que l'admin a cliqué sur Ajouter un produit
      ?>


    </div><!-- /.container -->

<?php

require("../inc/footer.inc.php"); 
