<?php
// connexion à la BDD
$pdo = new PDO('mysql:host=localhost;dbname=lokisalle', 'root', 'root', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

// appel du fichier contenant toutes nos fonctions.
require_once("function.inc.php");

// création de variables pouvant nous servir dans le cadre du projet:
// variable pour afficher des messages à l'utilisateur
$message = "";

// ouverture de la session
session_start();


// définition de constante pour le chemin absolu ainsi que pour la racine serveur
// racine site
define("URL", "/lokisalle/");

// racine serveur
define("RACINE_SERVEUR", $_SERVER['DOCUMENT_ROOT'] . URL );













