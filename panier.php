<?php
require("inc/init.inc.php");

$erreur = '';

// Vider le panier
if (isset($_GET['action']) && ($_GET['action'] == 'delete'))
{
    unset($_SESSION['panier']); // Permet de supprimer la partie panier de la session
}

// Retirer un article du panier
if (isset($_GET['action']) && $_GET['action'] == 'retirer' && !empty($_GET['id_produit']))
{
    retirer_produit_du_panier($_GET['id_produit']);
}

// Creation du panier
creation_panier();

if (isset($_POST['ajout_panier']))
{
    // Si l'indice existe dans un post alors l'utilisateur a cliqué sur le bouton ajouter au panier (depuis la page fiche_salle.php)
    $info_produit = $pdo->prepare("SELECT * FROM produit, salle WHERE id_produit = :id_produit AND produit.id_salle = salle.id_salle");
    $info_produit->bindParam(":id_produit", $_POST['id_produit'], PDO::PARAM_STR);
    $info_produit->execute();

    $produit = $info_produit->fetch(PDO::FETCH_ASSOC);
    //echo '<pre>1'; var_dump($produit); echo '</pre>';

    // Ajout de la tva sur le prix
    $produit['prix'] = $produit['prix']*1.2;

    // On ajoute l'article dans le panier via cette fonction (voir dans fonction.inc.php)
    ajouter_un_produit_au_panier($_POST['id_produit'], $produit['prix'], $produit['categorie'], $produit['titre'], $produit['photo'], $produit['date_arrivee'], $produit['date_depart'], $produit['adresse'], $produit['ville'], $produit['capacite'], $produit['etat']);

    // On redirige sur la meme pas pour perdre les informations dans POST afin de na pas renvoyer les informations quand l'utilisateur actualise la page (F5).
    header("location:panier.php");
}

// Validation du paiement du panier
if (isset($_GET['action']) && $_GET['action'] == 'payer' && !empty($_SESSION['panier']['prix']))
{
    // Si l'utilisateur clic sur le bouton "payer le panier"
    // 1ere action: Vérification du stock disponible en comparaison des quantités demandées.
    if(($_SESSION['panier']['etat']) == 'libre')
    {
        $resultat = $pdo->query("SELECT * FROM produit WHERE id_produit = " . $_SESSION['panier']['id_produit']);
        $verif_stock = $resultat->fetch(PDO::FETCH_ASSOC);

       
    
         $message .= '<div class="alert alert-success" role="alert" style="margin-top: 20px;">Ok pour la reservation de la salle "' . $_SESSION['panier']['titre'] . '" !</div>';
    }
            else
            {
                $message .= '<div class="alert alert-danger" role="alert" style="margin-top: 20px;">Attention, la salle n\'est plus dispo "' . $_SESSION['panier']['titre'] . '" !</div>';

                // Si le stock est a 0 alors on enleve l'article du panier.
                retirer_produit_du_panier($_SESSION['panier']['id_produit']);

            }
            $erreur = true;
        
    
    if (!$erreur) // ou if($erreur != true)
    {
        $id_membre = $_SESSION['utilisateur']['id_membre'];
        $montant_commande = montant_total();
        $pdo->query("INSERT INTO commande (id_commande, id_membre, id_produit, date) VALUES ($id_commande, $id_membre, $id_produit, NOW())");
        $id_commande = $pdo->lastInsertId(); // On récupère l'id par la dernière requete
       /*
        $nb_tout_panier = count($_SESSION['panier']['titre']);

        for ($i = 0; $i < $nb_tout_panier; $i++)
        {
            $id_produit_commande = $_SESSION['panier']['id_produit'][$i];
            $quantite_commande = $_SESSION['panier']['titre'][$i];
            $prix_commande = $_SESSION['panier']['prix'][$i];

            $pdo->query("INSERT INTO commande (id_commande, id_membre, id_produit, date_enregistrement) VALUES ($id_commande, $id_produit_commande, $quantite_commande, $prix_commande)");

            // Mise a jour du stock
            $pdo->query("UPDATE article SET stock = stock - $quantite_commande WHERE id_article = $id_article_commande");
        } */
        unset($_SESSION['panier']);
    }
}

// la ligne suivant commence les affichages dans la page
require("inc/header.inc.php");
require("inc/nav.inc.php");
//echo '<pre>'; var_dump($_GET); echo '</pre>';
//echo '<pre>'; print_r($_SESSION); echo '</pre>';
?>

    <div class="container">

        <div class="starter-template">
            <h1 style="margin-top: 50px;"><span class="glyphicon glyphicon-shopping-cart" style="color: NavajoWhite;"></span> Panier</h1>
            <?php // echo $message; // messages destinés à l'utilisateur ?>
            <?= $message; // cette balise php inclue un echo // cette ligne php est equivalente à la ligne au dessus. ?>
        </div>

        <div class="row">
            <div class=" col-sm-offset-2 col-sm-8">
                <table class="table table-bordered">
                    <tr>
                        <th colspan="10">Panier</th>
                    </tr>
                    <tr>
                        <th>Nom de la salle</th>
                        <th>Photo</th>
                        <th>Categorie</th>
                        <th>Adresses</th>
                        <th>Ville</th>
                        <th>Capacité</th>
                        <th>Date arrivée</th>
                        <th>Date départ</th>
                        <th>Prix</th> 
                        <th>Action</th>                     
                    </tr>
                    <?php
                        // Vérification si la panier est vide sur n'importe quel tableau array au dernier niveau (id_article / prix / quantite ou titre)
                        if (empty($_SESSION['panier']['id_produit']))
                        {
                            echo '<tr><th colspan="10" style="color: red; text-align: center;">Aucun produit dans votre panier</th></tr>';
                        }
                        else
                        {
                            // Sinon on affiche tous les produits dans un tableau html
                            $taille_tableau = count($_SESSION['panier']['titre']);
                            for ($i = 0; $i < $taille_tableau; $i++)
                            {
                                echo '<tr>';
                               // echo '<td>' . $_SESSION['panier']['id_produit'][$i] . '</td>';
                                echo '<td>' . $_SESSION['panier']['titre'][$i] . '</td>';
                                echo '<td><img src="' . URL . 'photo/' . $_SESSION['panier']['photo'][$i] . '"  class="img-responsive" /></td>';                               
                                echo '<td>' . $_SESSION['panier']['categorie'][$i] . '</td>';
                                echo '<td>' . $_SESSION['panier']['adresse'][$i] . '</td>';
                                echo '<td>' . $_SESSION['panier']['ville'][$i] . '</td>';
                                echo '<td>' . $_SESSION['panier']['capacite'][$i] . '</td>';
                                echo '<td>' . $_SESSION['panier']['date_arrivee'][$i] . '</td>';
                                echo '<td>' . $_SESSION['panier']['date_depart'][$i] . '</td>';

                                // number_format() permet de forcer l'affichage des 0 apres la virgule
                                echo '<td>' . number_format($_SESSION['panier']['prix'][$i], 2, ',', ' ') .  '€</td>';
                                //echo '<td>' . number_format($_SESSION['panier']['prix'][$i] * $_SESSION['panier']['etat'][$i], 2, ',', ' ') . '€</td>';
                                echo '<td style="text-align: center"><a href="?action=retirer&id_article=' . $_SESSION['panier']['id_produit'][$i] . '"<button class="btn btn-warning form-control">Delete</button></a></td>';
                                echo '</tr>';
                            }

                            // Affichage du prix total du panier
                            echo '<tr>';
                            echo '<td colspan="4">Montant Total <b>TTC</b></td>';
                            echo '<td colspan="3"><b>' . number_format(montant_total(), 2, ',', ' ') . '€</b></td>';
                            echo '</tr>';

                            // Affichage du bouton payer si l'utilisateur est connecté
                            if(utilisateur_est_connecte())
                            {
                                echo '<tr>';
                                echo '<td colspan="10"><a href="?action=payer" class="btn btn-success form-control">Payer</a></td>';
                                echo '</tr>';
                            }
                            else
                            {
                                echo '<tr>';
                                echo '<td colspan="7" style="color: red; text-align: center">Vous devez vous inscrire ou vous connecter pour valider votre panier.</td>';
                                echo '</tr>';
                                echo '<tr>';
                                echo '<td colspan="4" style="color: red; text-align: center"><a href="connexion.php" class="btn btn-info form-control">Connexion</td>';
                                echo '<td colspan="3" style="color: red; text-align: center"><a href="inscription.php" class="btn btn-info form-control">Inscription</td>';
                                echo '</tr>';
                            }

                            // Bouton vider le panier
                            echo '<tr>';
                            echo '<td colspan="10" style="text-align: right"><a href="?action=delete" class="btn btn-danger form-control">Vider Panier</a></td>';
                            echo '</tr>';
                        }

                        // Rajouter une ligne du tableau qui affiche un lien a href (?action=payer) pour payer le panier si l'utilisateur est connecté. Sinon afficher un texte pour proposer a l'utilisateur de s'inscrire ou de se connecter

                        // Rajouter une ligne du tableau qui afficher un bouton vider le panier uniquement si le panier n'est pas vide. Et faire le traitement afin que si on click sur le bouton, il faut vider le panier (unset).
                        // Rajouter une ligne pour afficher le prix total du panier
                    ?>
                </table>
                
              
            </div>
        </div>
        <div class="row">

        </div>
    </div><!-- /.container -->

<?php
require("inc/footer.inc.php");

















