<?php
require("inc/init.inc.php");


// on vérifie si l'indice id_article existe dans GET ou s'il n'est pas vide || on teste aussi si la valeur est bien un chiffre
if(empty($_GET['id_produit']) || !is_numeric($_GET['id_produit']))
{
	header("location:boutique.php");
}

// récupération des informations de l'article en bdd
$id_produit = $_GET['id_produit'];
$recup_produit = $pdo->prepare("SELECT * FROM produit, salle WHERE id_produit = :id_produit AND produit.id_salle = salle.id_salle");
$recup_produit->bindParam(":id_produit", $id_produit, PDO::PARAM_STR);
$recup_produit->execute();

// vérififcation si l'on a bien récupérer un article ou si nous avons un réponse vide (exemple changement d'id_article dans l'url par l'utilisateur.)
if($recup_produit->rowCount() < 1)
{
	// s'il y a moins d'une ligne alors la réponse de la BDD est vide donc on redirige vers l'accueil
	header("location:boutique.php");
}

$produit = $recup_produit->fetch(PDO::FETCH_ASSOC);

$id_produit = $produit['id_produit'];
$id_salle = $produit['id_salle'];
$date_arrivee = $produit['date_arrivee'];
$date_depart = $produit['date_depart'];
$prix = $produit['prix'];
$etat = $produit['etat'];






// la ligne suivant commence les affichages dans la page
require("inc/header.inc.php");
require("inc/nav.inc.php");
// echo '<pre>'; print_r($article); echo '</pre>';
// dans cette page affichez les informations de l'article sauf le stock
// mettre également en place un lien retour vers votre sélection sur la boutique
?>


    <!-- Custom styles for this template -->
    <link href="<?php echo URL; ?>css/portfolio-item.css" rel="stylesheet">
    </head>

    <body>
    <div class="container">

        <!-- Portfolio Item Heading -->
        <div class="row">
            <div class="col-lg-5">
                <h1 class="page-header"><?php echo $produit['titre']; ?>
                    <small>Item Subheading</small>
                </h1>
            </div>
            <div class="col-lg-4 col-lg-offset-8">
                <form method="post" action="panier.php" >
                <input type="hidden" name="id_produit" value="<?= $produit['id_produit'];?>" />
                <input class="btn btn-success" type="submit" name="ajout_panier" value="Réserver" />
                </form>
                    
            </div>
        </div>
        <!-- /.row -->

        <!-- Portfolio Item Row -->
        <div class="row">
            <div class="col-md-8">
                <img class="img-responsive" width="100%" style="height: 450px;" src="<?php echo URL . 'photo/' .$produit['photo']; ?>" alt="">
            </div>
            <div class="col-md-4 description-fiche">
                <h3>Description</h3>
                <p><?php echo substr($produit['description'], 0, 150); ?></p>
                <h3>Localisation</h3>
                <iframe width="370" height="277" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.it/maps?q=<?php echo $produit['adresse'] . $produit['ville'] . $produit['cp'] . $produit['pays']; ?>&output=embed"></iframe>

            </div>
        </div>
        <!-- /.row -->
        <div class="row">

            <div class="col-lg-12">
                <h3 class="page-header">Informations complementaires</h3>
                <div class="row">
                    <div class="col-sm-4 col-xs-6">Arrivée : <?php echo $produit['date_arrivee']; ?></div>
                    <div class="col-sm-4 col-xs-6">Capacité : <?php echo $produit['capacite']; ?></div>
                    <div class="col-sm-4 col-xs-6">Adresse : <?php echo $produit['adresse'] . ', ' . $produit['cp'] . ', ' . $produit['ville']; ?></div>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-xs-6">Départ : <?php echo $produit['date_depart']; ?></div>
                    <div class="col-sm-4 col-xs-6">Catégorie : <?php echo $produit['categorie']; ?></div>
                    <div class="col-sm-4 col-xs-6">Tarif : <?php echo $produit['prix']; ?></div>
                </div>
            </div>

        </div>

        <!-- Related Projects Row -->
        <div class="row">

            <div class="col-lg-12">
                <h3 class="page-header">Autres produits</h3>
            </div>


            <?php
            $count = 0;
            while( $count < 4)
            {
                        $random_photo = $pdo->query("SELECT DISTINCT photo FROM salle ORDER BY RAND() LIMIT 4")->fetch(PDO::FETCH_ASSOC);
                        echo '<div class="col-sm-3 col-xs-6">';
                        echo '<a href="#"><img class="img-responsive portfolio-item" style="height:200px" src="'  . URL . 'photo/' . $random_photo['photo'] . '"alt=""></a>';
                        echo '</div>';
                        $count ++;
            }
            ?>
        </div>
        <!-- /.row -->

        <hr>

        <!-- Footer -->
        <footer>
            <div class="row">
                <div class="col-lg-12">
                    <div class="col-sm-6 col-xs-12">
                    <p><a href="avis.php">Déposer un commentaire et une note</a></p>
                    </div>
                    <div class="col-sm-6 col-xs-12">
                    <p class="pull-right"><a href="boutique.php">Retour vers le catalogue</a></p>
                    </div>
                </div>
            </div>
        </footer>
    </div><!-- /.container -->
<?php
require("inc/footer.inc.php");

















