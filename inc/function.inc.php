<?php

// fonction pour savoir si un utilisateur est connecté
function utilisateur_est_connecte()
{
	if(isset($_SESSION['utilisateur']))
	{
		// si l'indice utilisateur existe alors l'utilisateur est connecté car il est passé par la page de connexion
		return true; // si on passe sur cette ligne, on sort de la fonction et le return false en dessous ne sera pas pris en compte.
	}
	return false; // si on rentre pas dans le if, on retourne false.
}

// fonction pour savoir si un utilisateur est connecté mais aussi a le statut administrateur.
function utilisateur_est_admin()
{
	if(utilisateur_est_connecte() && $_SESSION['utilisateur']['statut'] == 1)
	{
		return true;
	}
	return false;
}

// Creation du panier
function creation_panier()
{
    if (!isset($_SESSION['panier']))
    {
        $_SESSION['panier'] = array();
        $_SESSION['panier']['id_produit'] = array();
        $_SESSION['panier']['prix'] = array();
        $_SESSION['panier']['categorie'] = array();
        $_SESSION['panier']['titre'] = array();
        $_SESSION['panier']['photo'] = array();
        $_SESSION['panier']['date_arrivee'] = array();
        $_SESSION['panier']['date_depart'] = array();
        $_SESSION['panier']['adresse'] = array();
        $_SESSION['panier']['ville'] = array();
        $_SESSION['panier']['capacite'] = array();
        $_SESSION['panier']['etat'] = array();

    }
}

// Création de l'ajout d'un article au panier
function ajouter_un_produit_au_panier($id_produit, $prix, $categorie, $titre, $photo, $date_arrivee, $date_depart, $adresse, $ville, $capacite, $etat)
{
    // Avant d'ajouter, on vérifie si l'article n'est pas deja présent dans le panier, si c'est le cas on ne fait que modifier sa quantité.
    $position = array_search($id_produit, $_SESSION['panier']['id_produit']);
    // array_search() permet de vérifier si une valeur se trouve dans un tableau array. Si c'est le cas, on récupère l'indice correspondant.


    if ($position !== FALSE) {
        $_SESSION['panier']['id_produit'][] = $id_produit;
        $_SESSION['panier']['prix'][] = $prix;
        $_SESSION['panier']['categorie'][] = $categorie;
        $_SESSION['panier']['titre'][] = $titre;
        $_SESSION['panier']['photo'][] = $photo;
        $_SESSION['panier']['date_arrivee'][] = $date_arrivee;
        $_SESSION['panier']['date_depart'][] = $date_depart;
        $_SESSION['panier']['adresse'][] = $adresse;
        $_SESSION['panier']['ville'][] = $ville;
        $_SESSION['panier']['capacite'][] = $capacite;
        $_SESSION['panier']['etat'][] = $etat;
        return true;
    } else {
        return false;
    }

}
// Retirer un aproduit du panier
    function retirer_produit_du_panier($id_produit)
    {
        $position = array_search($id_produit, $_SESSION['panier']['id_produit']);
        // On vérifie si l'article est bien présent dans le panier et avec array_search on récupere son indice correspondant.
        if ($position !== FALSE)
        {
            array_splice($_SESSION['panier']['id_produit'], $position, 1);
            array_splice($_SESSION['panier']['prix'], $position, 1);
            array_splice($_SESSION['panier']['titre'], $position, 1);
            array_splice($_SESSION['panier']['photo'], $position, 1);
            array_splice($_SESSION['panier']['date_arrivee'], $position, 1);
            array_splice($_SESSION['panier']['date_depart'], $position, 1);
            array_splice($_SESSION['panier']['adresse'], $position, 1);
            array_splice($_SESSION['panier']['ville'], $position, 1);
            array_splice($_SESSION['panier']['capacite'], $position, 1);
            array_splice($_SESSION['panier']['etat'], $position, 1);

            // array_splice() permet de supprimer un élément dans un tableau et surtout de réordonner les indices afin de ne pas avoir de trou dans notre tableau
            // array_splice(le tableau concerné, indice a supprimer, nombre d'élément a supprimer)
        }
    }


// Fonction calcul du montant total du panier
function montant_total()
{
// Calcul du montant total du panier
    if (!empty($_SESSION['panier']['id_produit'])) {
        $taille_tab = sizeof($_SESSION['panier']['id_produit']);
        $total = 0;
        for ($i = 0; $i < $taille_tab; $i++) {
            $total += $_SESSION['panier']['prix'][$i];
        }
        return $total;
    }
}
/*
function montant_ligne()
{
    // Calcul du montant total de chaque ligne du panier
    if (!empty($_SESSION['panier']['titre'])) {
        $taille_tab = sizeof($_SESSION['panier']['id_article']);
        $total = 0;
        for ($i = 0; $i < $taille_tab; $i++) {
            $total += $_SESSION['panier']['prix'][$i] * $_SESSION['panier']['quantite'][$i];
        }
        return $total;
    }
}
*/
// Fonction pour afficher toutes les dates comprises entre 2 dates (date_arrivee et date_depart)
function getDatesFromRange($start, $end)
{
    $interval = new DateInterval('P1D');
    $realEnd = new DateTime($end);
    $realEnd->add($interval);
    $period = new DatePeriod(new DateTime($start), $interval, $realEnd);
    foreach ($period as $date) {
        $array[] = $date->format('Y-m-d');
    }
    return $array;
}











