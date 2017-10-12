<?php
require("../inc/init.inc.php");
// restriction d'acces, si l'utilisateur n'est pas admin alors il ne doit pas accéder à cette page.


if(!utilisateur_est_admin())
{
    header("location:../connexion.php");
    exit(); //permet d'arreter l'exécution du script au cas où une personne malveillante ferait des injections via GET
}

// mettre en place un controle pour savoir si l'utilisateur veut une suppression d'un produit.
if(isset($_GET['action']) && $_GET['action'] == 'suppression' && !empty($_GET['id_produit']) && is_numeric($_GET['id_produit']))
{
    // is_numeric permet de savoir si l'information est bien une valeur numérique sans tenir compte de son type (les informations provenant de GET et de POSt sont toujours de type string)

    // on fait une requete pour récupérer les informations de l'article afin de connaitre la photo pour la supprimer
    $id_produit = $_GET['id_produit'];
    $produit_a_supprimer = $pdo->prepare("SELECT * FROM produit WHERE id_produit = :id_produit");
    $produit_a_supprimer->bindParam(":id_produit", $id_produit, PDO::PARAM_STR);
    $produit_a_supprimer->execute();

    $delete_produit = $produit_a_supprimer->fetch(PDO::FETCH_ASSOC);

    $suppression = $pdo->prepare("DELETE FROM produit WHERE id_produit = :id_produit");
    $suppression->bindParam(":id_produit", $id_produit, PDO::PARAM_STR);
    $suppression->execute();
    $message .= '<div class="alert alert-success" role="alert" style="margin-top: 20px;">Le produit: ' . $id_produit . ' a bien été supprimé</div>';

    // on bascule sur l'affichage du tableau
    $_GET['action'] = 'affichage';

}

$liste_salle = $pdo->query("SELECT * FROM salle ORDER BY id_salle");

$id_produit = "";
$id_salle = "";
$date_arrivee = "";
$date_depart = "";
$prix = "";
$etat = "";

// déclaration d'un variable de contrôle
$erreur = "";
//*******************************************************
// RECUPERATION DES INFORMATIONS D'UN Produit A MODIFIER
//*******************************************************
if(isset($_GET['action']) && $_GET['action'] == 'modification' && !empty($_GET['id_produit']) && is_numeric($_GET['id_produit']))
{
    $id_produit = $_GET['id_produit'];
    $produit_a_modif = $pdo->prepare("SELECT * FROM produit WHERE id_produit = :id_produit");
    $produit_a_modif->bindParam(":id_produit", $id_produit, PDO::PARAM_STR);
    $produit_a_modif->execute();
    $produit_actuel = $produit_a_modif->fetch(PDO::FETCH_ASSOC);

    $id_produit = $produit_actuel['id_produit'];
    $id_salle = $produit_actuel['id_salle'];
    $date_arrivee = $produit_actuel['date_arrivee'];
    $date_depart = $produit_actuel['date_depart'];
    $prix = $produit_actuel['prix'];
    $etat = $produit_actuel['etat'];
}

//*******************************************************
// ENREGISTREMENT DES PRODUITS 
//*******************************************************
if( isset($_POST["id_produit"]) && isset($_POST["date_arrivee"]) && isset($_POST["date_depart"]) && isset($_POST["prix"]) )
{
    $id_produit = $_POST["id_produit"];
    $date_arrivee = $_POST["date_arrivee"];
    $date_depart = $_POST["date_depart"];
    $prix = $_POST["prix"];
    $id_salle = $_POST['id_salle'];

    // contrôle sur la disponibilité de l'id produit en BDD si on est dans le cas d'un ajout car lors de la modification la reference existera toujours.
    $verif_id_produit = $pdo->prepare("SELECT * FROM produit WHERE id_produit = :id_produit");
    $verif_id_produit->bindParam(":id_produit", $id_produit, PDO::PARAM_STR);
    $verif_id_produit->execute();
    if($verif_id_produit->rowCount() > 0 && isset($_GET['action']) && $_GET['action'] == 'ajout')
    {
        // si l'on obtient au moins 1 ligne de resultat alors la référence est déjà prise.
        $message .= '<div class="alert alert-danger" role="alert" style="margin-top: 20px;">Attention, l\'id_produit déjà utilisé<br />Veuillez vérifier votre saisie</div>';
        $erreur = true;
    }

    // contrôle sur la disponibilité du produit en BDD si on est dans le cas d'un ajout car lors de la modification la reference existera toujours.
    // si post ..
    $test = getDatesFromRange( $date_arrivee, $date_depart);
    foreach($test AS $check_date)
    {
        $verif_dispo = $pdo->prepare("SELECT * FROM produit WHERE id_salle = :id_salle AND $check_date != :date_arrivee AND $check_date != :date_depart");
            
        $verif_dispo->bindParam(":id_salle", $id_salle, PDO::PARAM_STR);
        $verif_dispo->bindParam(":date_arrivee", $date_arrivee, PDO::PARAM_STR);
        $verif_dispo->bindParam(":date_depart", $date_depart, PDO::PARAM_STR);
        $verif_dispo->execute();
        if($verif_dispo->rowCount() > 1 && isset($_GET['action']) && $_GET['action'] == 'ajout')
        {
            // si l'on obtient au moins 1 ligne de resultat alors la référence est déjà prise.
            $message = '<div class="alert alert-danger" role="alert" style="margin-top: 20px;">Attention, la salle et/ou les dates sont indisponibles<br />Veuillez vérifier votre saisie</div>';
            $erreur = true;
        }
    }
    //echo '<pre>'; print_r($test); echo '</pre>';

    


    if(!$erreur) // équivaut à if($erreur == false)
    {

        if(isset($_GET['action']) && ($_GET['action'] == 'ajout'  || $_GET['action'] == 'affichage'))
        {
            // insertion des produits
            $enregistrement = $pdo->prepare("INSERT INTO produit (id_salle, date_arrivee, date_depart, prix) VALUES (:id_salle, :date_arrivee, :date_depart, :prix)");
        }
        elseif(isset($_GET['action']) && $_GET['action'] == 'modification')
        {
            $enregistrement = $pdo->prepare("UPDATE produit SET date_arrivee = :date_arrivee, date_depart = :date_depart, prix = :prix WHERE id_produit = :id_produit");
            $id_produit = $_POST['id_produit'];
            $enregistrement->bindParam(":id_produit", $id_produit, PDO::PARAM_STR);
        }

        $enregistrement->bindParam(":id_salle", $id_salle, PDO::PARAM_STR);
        $enregistrement->bindParam(":date_arrivee", $date_arrivee, PDO::PARAM_STR);
        $enregistrement->bindParam(":date_depart", $date_depart, PDO::PARAM_STR);
        $enregistrement->bindParam(":prix", $prix, PDO::PARAM_STR);
        $enregistrement->execute();
    }
}
//*******************************************************
// FIN ENREGISTREMENT DES ARTICLES
//*******************************************************


// la ligne suivant commence les affichages dans la page
require("../inc/header.inc.php");
require("../inc/nav.inc.php");
//    echo '<pre>'; print_r($_POST); echo '</pre>';
// echo '<pre>'; print_r($_FILES); echo '</pre>';

/*
// si post ..
$test = getDatesFromRange( '2010-10-01', '2010-10-05' );
foreach($test AS $check_date)
{
    $check = $pdo->prepare("SELECT * FROM produit WHERE id_salle = :id_salle AND $check_date >= date_arrivee AND $check_date <= date_depart");
    ->bindparam
    ->fetch
    ->si une ligne => probleme
}
echo '<pre>'; print_r($test); echo '</pre>';
*/
?>
    <!-- Custom styles for this template -->
    <link href="<?php echo URL; ?>css/style.css" rel="stylesheet">
    </head>

    <body>
    <div class="container">

        <div class="starter-template">
            <h1><span class="glyphicon glyphicon-user" style="color: NavajoWhite;"></span> Gestion des produits</h1>
            <?php // echo $message; // messages destinés à l'utilisateur ?>
            <?= $message; // cette balise php inclue un echo // cette ligne php est equivalente à la ligne au dessus. ?>
            <hr />
            <a href="?action=ajout" class="btn btn-warning">Ajouter un produit</a>
            <a href="?action=affichage" class="btn btn-info">Afficher les produits</a>
        </div>

<?php
// affichage de tous les produits dans un tableau html
// exercice: couper la description si elle est trop longue
// exercice: afficher l'image dans une balise <img src="" />
if(isset($_GET['action']) && $_GET['action'] == 'affichage')
{
    $resultat = $pdo->query("SELECT produit.id_produit, produit.date_arrivee, produit.date_depart, salle.id_salle, salle.titre, produit.prix, produit.etat FROM produit, salle WHERE salle.id_salle = produit.id_salle");

    echo '<hr />';
    echo '<div class="row">';
    echo '<div class="col-sm-12">';
    echo '<table class="table table-bordered">';

    echo '<tr>';
    $nb_colonne = $resultat->columnCount(); // on récupère le nb de colonne

    for($i = 0; $i < $nb_colonne; $i++)
    {
        $info_colonne = $resultat->getColumnMeta($i);
        // echo '<pre>'; print_r($info_colonne); echo '</pre>';
        echo '<th>' . $info_colonne['name'] . '</th>';
    }
    echo '<th>Modif</th>';
    echo '<th>Suppr</th>';
    echo '</tr>';

    while($product = $resultat->fetch(PDO::FETCH_ASSOC))
    {
        echo '<tr>';
        foreach($product AS $indice => $valeur)
        {
                echo '<td>' . $valeur . '</td>';
        }
        echo '<td><a href="?action=modification&id_produit=' . $product['id_produit'] . '" class="btn btn-warning"><span class="glyphicon glyphicon-refresh"></span></a></td>';

        echo '<td><a onclick="return(confirm(\'Etes vous sûr\'));" href="?action=suppression&id_produit=' . $product['id_produit'] . '" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></a></td>';
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
                    <form method="post" action="" enctype="multipart/form-data">
                        <!-- id_article => caché (hidden) -->
                        <input type="hidden"  name="id_produit" id="id_produit" class="form-control" value="<?php echo $id_produit; ?>"/>
<!--                    <div class="form-group">-->
<!--                            <label for="date_arrivee">Date d'arrivée</label>-->
<!--                            <input type='text' class="form-control" id='date_arrivee' name="date_arrivee" value="--><?php //echo $date_arrivee; ?><!--">-->
<!--                    </div>-->
                        <div class="form-group">
                            <label for="date_arrivee">Date d'arrivée</label>
                            <div class='input-group' id='date_arrivee'>
                                     <span class="input-group-addon">
                                         <span class="glyphicon glyphicon-calendar"></span>
                                     </span>
                                <input type='text' class="form-control" id='date_arrivee' name="date_arrivee" value="<?php echo $date_arrivee; ?>" >
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="date_depart">Date de départ</label>
                            <div class='input-group' id='date_depart'>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                <input type='text' class="form-control" id='date_depart' name="date_depart" value="<?php echo $date_depart; ?>" >

                            </div>
                        </div>
<!--                    <div class="form-group">-->
<!--                            <label for="date_depart">Date de départ</label>-->
<!--                            <input type="text" class="form-control" name="date_depart" id='date_depart' value="--><?php //echo $date_depart; ?><!--">-->
<!--                    </div>-->
                    <?php
                    echo'<div class="form-group">';
                        echo '<label for="id_salle">Salle</label>';
                        echo '<select name="id_salle" id="id_salle" class="form-control" >';
                    while($salles = $liste_salle->fetch(PDO::FETCH_ASSOC))
                    {
                        echo '<option value="' .$salles['id_salle'] . '">' . $salles['id_salle'] . ' - ' . $salles['titre'] . ' - ' . $salles['adresse'] . ', ' . $salles['cp'] . ', ' . $salles['ville'] . ' - ' . $salles['capacite'] . '</option>';
                    }
                        echo'</select>';
                    echo'</div>';
                    ?>
                    <div class="form-group">
                        <label for="prix">Tarif<span style="color: red;">*</span></label>
                        <input type="text"  name="prix" id="prix" class="form-control" />
                    </div>
                    <div class="form-group">
                        <button type="submit"  name="enregistrement" id="enregistrement" class="form-control btn btn-primary"><span class='glyphicon glyphicon-star-empty' style="color: NavajoWhite;"></span> Enregistrement <span class='glyphicon glyphicon-star-empty' style="color: NavajoWhite;"></span></button>
                    </div>

                    </form>
                </div>
            </div>
            <?php } // accolade correspondante à la condition sur l'affichage du formulaire 
			// if(isset($_GET['action']) && $_GET['action'] == 'ajout')
	?>
<?php
require("../inc/footer.inc.php");


















