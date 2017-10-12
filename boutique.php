<?php
require("inc/init.inc.php");

$pagination = $pdo->query("SELECT COUNT(id_produit) AS nbArt FROM produit")->fetch(PDO::FETCH_ASSOC);
$nbArt = $pagination['nbArt'];
$perPage = 6;
$nbPage = ceil($nbArt/$perPage);

if (isset($_GET['p']) && $_GET['p']>0 && $_GET['p']<=$nbPage)
{
    $cPage = $_GET['p'];
}
else
{
    $cPage = 1;
}

$liste_salles = $pdo->query("SELECT * FROM salle, produit WHERE salle.id_salle = produit.id_salle LIMIT " . (($cPage-1)*$perPage) .",$perPage");

// requete de récupération de tous les produits
if ($_POST) // Equivaut a if(!empty($_Post))
{
    $condition = "";
    $arg_ville = false;
    $arg_capacite = false;

   if (!empty($_POST['ville']))
   {
       $condition .= " AND ville = :ville";
       $arg_ville = TRUE;
       $filtre_ville = $_POST['ville'];

   }
   if (!empty($_POST['capacite']))
   {
       if ($arg_capacite)
       {
           $condition .= " AND capacite = :capacite ";
       }
       else
       {
           $condition .= " AND capacite = :capacite ";
       }
       $arg_capacite = true;
       $filtre_capacite = $_POST['capacite'];
   }
    $liste_salles = $pdo->prepare("SELECT * FROM salle, produit WHERE salle.id_salle = produit.id_salle $condition LIMIT " . (($cPage-1)*$perPage) .",$perPage");
    if ($arg_ville) // si $arg_couleur == true alors il faut fournir l'argument couleur
    {
        $liste_salles->bindParam(":ville", $filtre_ville, PDO::PARAM_STR);
    }
    if ($arg_capacite) // // si $arg_taille == true alors il faut fournir l'argument couleur
    {
        $liste_salles->bindParam(":capacite", $filtre_capacite, PDO::PARAM_STR);
    }
    $liste_salles->execute();
}
elseif(!empty($_GET['categorie']))
{
    echo 'test';
    $cat = $_GET['categorie'];
	$liste_salles = $pdo->prepare("SELECT * FROM salle, produit WHERE salle.id_salle = produit.id_salle AND categorie = :categorie");
	$liste_salles->bindParam(":categorie", $cat, PDO::PARAM_STR);
	$liste_salles->execute();
}

// requete de récupération des différentes catégories en BDD
$liste_categorie = $pdo->query("SELECT DISTINCT categorie FROM salle");

// requete de récupération des différentes couleurs en BDD
$liste_ville = $pdo->query("SELECT DISTINCT ville FROM salle ORDER BY ville");

// requete de récupération des différentes tailles en BDD
$liste_capacite = $pdo->query("SELECT DISTINCT capacite FROM salle ORDER BY capacite");

// deconnexion de l'utilisateur
if(isset($_GET['action']) && $_GET['action'] == 'deconnexion' )
{
    session_destroy();
    //exit();
}

// vérification de l'existence des indices du formulaire
if(isset($_POST['pseudo']) && isset($_POST['mdp']))
{
    $pseudo = $_POST['pseudo'];
    $mdp = $_POST['mdp'];

    $verif_connexion = $pdo->prepare("SELECT * FROM membre WHERE pseudo = :pseudo AND mdp = :mdp");
    $verif_connexion->bindParam(":pseudo", $pseudo, PDO::PARAM_STR);
    $verif_connexion->bindParam(":mdp", $mdp, PDO::PARAM_STR);
    $verif_connexion->execute();

    if($verif_connexion->rowCount() > 0)
    {
        // si nous avons 1 ligne alors le pseudo et le mdp sont corrects
        $info_utilisateur = $verif_connexion->fetch(PDO::FETCH_ASSOC);
        // on place toutes les informations de l'utilisateur dans la session sauf le mdp

        $_SESSION['utilisateur'] = array();
        $_SESSION['utilisateur']['id_membre'] = $info_utilisateur['id_membre'];
        $_SESSION['utilisateur']['pseudo'] = $info_utilisateur['pseudo'];
        $_SESSION['utilisateur']['nom'] = $info_utilisateur['nom'];
        $_SESSION['utilisateur']['prenom'] = $info_utilisateur['prenom'];
        $_SESSION['utilisateur']['civilite'] = $info_utilisateur['civilite'];
        $_SESSION['utilisateur']['statut'] = $info_utilisateur['statut'];
        $_SESSION['utilisateur']['email'] = $info_utilisateur['email'];
    }
}

// la ligne suivant commence les affichages dans la page
require("inc/header.inc.php");
require("inc/nav.inc.php");
//echo '<pre>'; print_r($_GET); echo '</pre>';
//echo '<pre>'; print_r($_SESSION); echo '</pre>';
//echo '<pre>'; print_r($liste_salles); echo '</pre>';
    if(!utilisateur_est_connecte())
    {
    ?>
    <div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="loginmodal-container">
                <h1>Connectez-Vous</h1><br>
                <form method="post" action="">
                    <input type="text" id="pseudo" name="pseudo" placeholder="Identifiant">
                    <input type="text" id="mdp" name="mdp" placeholder="Mot de passe">
                    <input type="submit" name="connexion" class="login loginmodal-submit" value="Connexion">
                </form>
                <div class="login-help">
                    <a href="inscription.php">Enregistrez-vous</a> - <a href="#">Mot de passe oublié ?</a>
                </div>
            </div>
        </div>
    </div>
<?php
}
?>

    <!-- Custom styles for this template -->
    <link href="<?php echo URL; ?>css/shop-homepage.css" rel="stylesheet">
    </head>

    <body>
    <div class="container">

        <div class="row">

            <div class="col-md-3">
                <p class="lead">Lokisalle</p>
                <?php // récupérer toutes les catégories en BDD et les afficher dans une liste ul li sous forme de lien a href avec une information GET par exemple: ?categorie=pantalon

                echo '<ul class="list-group">';
                echo '<li class="list-group-item"><a href="boutique.php">Tous les articles</a></li>';
                while($categorie = $liste_categorie->fetch(PDO::FETCH_ASSOC))
                {
                    echo '<li class="list-group-item"><a href="?categorie=' . $categorie['categorie'] . '">' . $categorie['categorie'] . '</a></li>';
                }
                echo '</ul>';
                echo '<hr>';
                echo '<form method="post" action="">';
                // Affichage Ville
                echo '<div class="form-group">';
                echo '<label for="ville">Ville</label>';
                echo '<select name="ville" id="ville" class="form-control">';
                echo '<option></option>';
                while($ville = $liste_ville->fetch(PDO::FETCH_ASSOC))
                {
                    echo '<option>' . $ville['ville'] . '</option>';
                }
                echo '</select></div>';
                // Affichage Capacite
                echo '<div class="form-group">';
                echo '<label for="capacite">Capacite</label>';
                echo '<select name="capacite" id="capacite" class="form-control">';
                echo '<option></option>';
                while($capacite = $liste_capacite->fetch(PDO::FETCH_ASSOC))
                {
                    echo '<option>' . $capacite['capacite'] . '</option>';
                }
                echo '</select></div>';
                ?>
                <div class="form-group">
                    <label for="date_arrivee">Date d'arrivée</label>
                    <input type='text' class="form-control" id='date_arrivee' name="date_arrivee" value="" >
                </div>
                <div class="form-group">
                    <label for="date_depart">Date de départ</label>
                    <input type='text' class="form-control" id='date_depart' name="date_depart" value="" >
                </div>

                <?php
                echo '<div class="form-group">';
                echo '<button type="submit" name="filtrer" id="filtrer" class="form-control btn btn-primary">Filtrer</button>';
                echo '</div>';
                echo '</form>';
                ?>
            </div>

            <div class="col-md-9">

                <div class="row carousel-holder">
                    <div class="col-md-12">
                        <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                            <ol class="carousel-indicators">
                                <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                                <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                                <li data-target="#carousel-example-generic" data-slide-to="2"></li>
                            </ol>
                            <?php
                            $random_photo = $pdo->query("SELECT photo FROM salle ORDER BY RAND() LIMIT 1")->fetch(PDO::FETCH_ASSOC);
                            $random_photo1 = $pdo->query("SELECT photo FROM salle ORDER BY RAND() LIMIT 1")->fetch(PDO::FETCH_ASSOC);
                            $random_photo2 = $pdo->query("SELECT photo FROM salle ORDER BY RAND() LIMIT 1")->fetch(PDO::FETCH_ASSOC);
                            ?>
                            <div class="carousel-inner">
                                <div class="item active">
                                    <?php echo '<img class="img-responsive portfolio-item" style="width: 100%; height: 300px;" src="'  . URL . 'photo/' . $random_photo['photo'] . '"alt="">';?>
                                </div>
                                <div class="item">
                                    <?php echo '<img class="img-responsive portfolio-item" style="width: 100%; height: 300px;" src="'  . URL . 'photo/' . $random_photo1['photo'] . '"alt="">';?>
                                </div>
                                <div class="item">
                                    <?php echo '<img class="img-responsive portfolio-item" style="width: 100%; height: 300px;" src="'  . URL . 'photo/' . $random_photo2['photo'] . '"alt="">';?>
                                </div>
                            </div>
                            <a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
                                <span class="glyphicon glyphicon-chevron-left"></span>
                            </a>
                            <a class="right carousel-control" href="#carousel-example-generic" data-slide="next">
                                <span class="glyphicon glyphicon-chevron-right"></span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="row">
                        <?php

                        while($salles = $liste_salles->fetch(PDO::FETCH_ASSOC))
                        {
                            echo '<div class="col-sm-4 col-lg-4 col-md-4">';
                            echo '<div class="thumbnail">';
                            echo '<img src="' . URL . 'photo/' .$salles['photo'] . '" style="height: 150px;" alt="">';
                            echo '<div class="caption">';
                            echo '<h4 class="pull-right">' . $salles['prix'] . '€</h4>';
                            echo '<h4><a href="fiche_salle.php?id_produit=' . $salles['id_produit'] . '">' . $salles['titre'] . '</a></h4>';
                            echo '<p>' . substr($salles['description'], 0, 60) . '...</p></div>';
                            echo '<div class="ratings">';
                            echo '<p class="pull-right">15 avis</p>';
                            echo '<span class="glyphicon glyphicon-star"></span>';
                            echo '<span class="glyphicon glyphicon-star"></span>';
                            echo '<span class="glyphicon glyphicon-star"></span>';
                            echo '<span class="glyphicon glyphicon-star"></span>';
                            echo '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<a href="fiche_salle.php?id_produit=' . $salles['id_produit'] . '"><span class="glyphicon glyphicon-search"></span></a>';
                            echo '</div></div></div>';
                        }
                        ?>
                </div>
                <div class="row">
                    <p class="pull-right">
                    <?php
                        for ($i = 1; $i <= $nbPage; $i++)
                        {
                            if ($i == $cPage)
                            {
                                echo " $i -";
                            }
                            else
                            {
                                echo " <a href=\"boutique.php?p=$i\">$i -</a> ";
                            }
                        }
                        ?>
                    </p>
                </div>

            </div>
        </div>
    </div><!-- /.container -->
   
<?php
require("inc/footer.inc.php");

















