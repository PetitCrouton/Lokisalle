<?php
require("../inc/init.inc.php");

// restriction d'acces, si l'utilisateur n'est pas admin alors il ne doit pas accéder à la page
if(!utilisateur_est_admin())
{
  header("location:../connexion.php");
  exit(); //permet d'arrêter l'exécution du script au cas où une personne malveillante ferait des injections via GET
}

// mettre en place un controle pour savoir si l'utilisateur veut une suppression d'un avis
if(isset($_GET['action']) && $_GET['action'] == 'suppression' && !empty($_GET['id_avis']) && is_numeric($_GET['id_avis']))
{
	// is_numeric permet de savoir si l'information est bien une valeur numérique sans tenir compte de son type (les informations provenant de GET et de POSt sont toujours de type string)
	
	// on fait une requete pour récupérer les informations de l'avis 
	$id_avis = $_GET['id_avis'];
	$avis_a_supprimer = $pdo->prepare("SELECT * FROM avis WHERE id_avis = :id_avis");
	$avis_a_supprimer->bindParam(":id_avis", $id_avis, PDO::PARAM_STR);
	$avis_a_supprimer->execute();
	
	$avis_a_suppr = $avis_a_supprimer->fetch(PDO::FETCH_ASSOC);

	$suppression = $pdo->prepare("DELETE FROM avis WHERE id_avis = :id_avis");	
	$suppression->bindParam(":id_avis", $id_avis, PDO::PARAM_STR);
	$suppression->execute();
	$message .= '<div class="alert alert-success" role="alert" style="margin-top: 20px;">L\'avis numéro ' . $id_avis . ' a bien été supprimé</div>';
	
	// on bascule sur l'affichage du tableau
	$_GET['action'] = 'affichage';
	
}

//*******************************************************
//                  VARIABLES VIDES
//*******************************************************
// Déclaration de variables vides pour affichage dans les values du formulaire
$id_avis = "";
$id_membre = "";
$id_salle = "";
$commentaire = "";
$note = "";
$date_enregistrement = ""; 

// variable erreur
$erreur="";




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
        <h1><span class="glyphicon glyphicon-download-alt" style="color:plum;"></span>Gestion des avis</h1>
        <hr/>

        <!-- ##### PHP ##### -->
        <?php //echo $message; // messages destinés à l'utilisateur?>
        <?= $message;?> <!--Raccourci pour faire un echo, égal à la ligne au-dessus-->
      </div>
  
        <?php

       
          $resultat = $pdo->query("SELECT * FROM avis");
          echo 'Nombre d\'avis présents dans la base:' . " " .$resultat->rowCount();
          
          echo '<hr>';
          echo '<div class="row">';
          echo '<div class="col-sm-12">';

        // balise d'ouverture du tableau
        echo '<table border="1" style="width: 80%; margin: 0 auto; border-collapse: collapse; text-align: center;">';
        // premiere ligne du tableau pour le nom des colonnes
        echo '<tr>';
        // récupération du nombre de colonnes dans la requete:
        $nb_col = $resultat->columnCount();

        // création de variables supprimer
        $actions = 'Actions';

        for($i = 0; $i < $nb_col; $i++)
        {
            //echo '<pre>'; echo print_r($resultat->getColumnMeta($i)); echo '</pre>'; echo '<hr/>';
            $colonne = $resultat->getColumnMeta($i); // on récupére les informations del a colonne en cours afin ensuite de demander le name
            echo '<th style="padding: 10px;">' . $colonne['name'] . '</th>';
        }
        
            echo '<th style="padding: 10px;">' . $actions . '</th>';

        echo '</tr>';

        while($ligne = $resultat->fetch(PDO::FETCH_ASSOC))
        {
            echo '<tr>';

            foreach($ligne AS $indice => $info)
            {
              
                echo '<td style="padding: 10px;">' . $info . '</td>';
                
            }

            echo '<td><a class="btn btn-primary"><span class="glyphicon glyphicon-search"></span></a>';
            echo '<a class="btn btn-info"><span class="glyphicon glyphicon-pencil"></span></a>';
            echo '<a onclick="return(confirm(\'Etes vous sûr\'));" href="?action=suppression&id_avis=' . $ligne['id_avis'] . '" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></a></td>';

            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';
        echo '</div>';
        
         
        ?>


    </div><!-- /.container -->

<?php

require("../inc/footer.inc.php"); 
