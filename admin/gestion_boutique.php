<?php
require("../inc/init.inc.php");
// restriction d'acces, si l'utilisateur n'est pas admin alors il ne doit pas accéder à cette page.


if(!utilisateur_est_admin())
{
	header("location:../connexion.php");
	exit(); //permet d'arreter l'exécution du script au cas où une personne malveillante ferait des injections via GET
}

// mettre en place un controle pour savoir si l'utilisateur veut une suppression d'un produit.
if(isset($_GET['action']) && $_GET['action'] == 'suppression' && !empty($_GET['id_salle']) && is_numeric($_GET['id_salle']))
{
	// is_numeric permet de savoir si l'information est bien une valeur numérique sans tenir compte de son type (les informations provenant de GET et de POSt sont toujours de type string)
	
	// on fait une requete pour récupérer les informations de l'article afin de connaitre la photo pour la supprimer
	$id_salle = $_GET['id_salle'];
	$salle_a_supprimer = $pdo->prepare("SELECT * FROM salle WHERE id_salle = :id_salle");
	$salle_a_supprimer->bindParam(":id_salle", $id_salle, PDO::PARAM_STR);
	$salle_a_supprimer->execute();
	
	$delete_salle = $salle_a_supprimer->fetch(PDO::FETCH_ASSOC);
	// on vérifie si la photo existe
	if(!empty($delete_salle['photo']))
	{
		// on vérifie le chemin si le fichier existe
		$chemin_photo = RACINE_SERVEUR . 'photo/' . $delete_salle['photo'];
		// $message .= $chemin_photo;
		if(file_exists($chemin_photo))
		{
			unlink($chemin_photo); // unlink() permet de supprimer un fichier sur le serveur.			 
		}
	}
	$suppression = $pdo->prepare("DELETE FROM salle WHERE id_salle = :id_salle");
	$suppression->bindParam(":id_salle", $id_salle, PDO::PARAM_STR);
	$suppression->execute();
	$message .= '<div class="alert alert-success" role="alert" style="margin-top: 20px;">La salle numéro ' . $id_salle . ' a bien été supprimée</div>';
	
	// on bascule sur l'affichage du tableau
	$_GET['action'] = 'affichage';
	
}


$id_salle ="";
$titre ="";
$description ="";
$pays ="";
$ville ="";
$adresse ="";
$cp ="";
$capacite ="";
$categorie ="";
$photo_bdd ="";

// déclaration d'un variable de contrôle
$erreur = "";

//*******************************************************
// RECUPERATION DES INFORMATIONS D'UN ARTICLE A MODIFIER
//*******************************************************
if(isset($_GET['action']) && $_GET['action'] == 'modification' && !empty($_GET['id_salle']) && is_numeric($_GET['id_salle']))
{
	$id_salle = $_GET['id_salle'];
	$salle_a_modif = $pdo->prepare("SELECT * FROM salle WHERE id_salle = :id_salle");
	$salle_a_modif->bindParam(":id_salle", $id_salle, PDO::PARAM_STR);
	$salle_a_modif->execute();
	$salle_actuelle = $salle_a_modif->fetch(PDO::FETCH_ASSOC);
	
	$id_salle = $salle_actuelle['id_salle'];
	$titre = $salle_actuelle['titre'];
	$description = $salle_actuelle['description'];
	$pays = $salle_actuelle['pays'];
    $ville = $salle_actuelle['ville'];
	$adresse = $salle_actuelle['adresse'];
	$cp = $salle_actuelle['cp'];
	$capacite = $salle_actuelle['capacite'];
	$categorie = $salle_actuelle['categorie'];
	// on récupère la photo de l'article dans une nouvelle variable
	$photo_actuelle = $salle_actuelle['photo'];
}


//*******************************************************
// ENREGISTREMENT DES ARTICLES
//*******************************************************
if( isset($_POST["id_salle"]) && isset($_POST["titre"]) && isset($_POST["description"]) && isset($_POST["pays"]) && isset($_POST["ville"]) && isset($_POST["adresse"]) && isset($_POST["cp"]) && isset($_POST["capacite"]) && isset($_POST["categorie"]) )
{
	$id_salle = $_POST["id_salle"];
	$titre = $_POST["titre"];
	$description = $_POST["description"];
    $pays = $_POST["pays"];
	$ville = $_POST["ville"];
	$adresse = $_POST["adresse"];
	$cp = $_POST["cp"];
	$capacite = $_POST["capacite"];
	$categorie = $_POST["categorie"];


	
	// contrôle sur la disponibilité de la reference en BDD si on est dans le cas d'un ajout car lors de la modification la reference existera toujours.
	$verif_titre = $pdo->prepare("SELECT * FROM salle WHERE titre = :titre");
	$verif_titre->bindParam(":titre", $titre, PDO::PARAM_STR);
	$verif_titre->execute();
	if($verif_titre->rowCount() > 0 && isset($_GET['action']) && $_GET['action'] == 'ajout')
	{
		// si l'on obtient au moins 1 ligne de resultat alors la référence est déjà prise.
		$message .= '<div class="alert alert-danger" role="alert" style="margin-top: 20px;">Attention, référence déjà utilisée<br />Veuillez vérifier votre saisie</div>';
		$erreur = true;
	}
	// verification si le titre n'est pas vide
	if(empty($titre))
	{
		$message .= '<div class="alert alert-danger" role="alert" style="margin-top: 20px;">Attention, le titre est obligatoire</div>';
		$erreur = true;
	}

	// récupération de l'ancienne photo dans le cas d'une modification
	if(isset($_GET['action']) && $_GET['action'] == "modification")
	{
		if(isset($_POST['ancienne_photo']))
		{
			$photo_bdd = $_POST['ancienne_photo'];
		}
	}
	
	// vérification si l'utilisateur a chargé une image
	if(!empty($_FILES['photo']['name']))
	{
		// si ce n'est pas vide alors un fichier a bien été chargé via le formulaire.
		
		// on concatène la référence sur le titre afin de ne jamais avoir un fichier avec un nom déjà existant sur le serveur.
		$photo_bdd = $id_salle . '_' . $_FILES['photo']['name'];
		
		// vérification de l'extension de l'image (extension acceptées: jpg jpeg, png, gif)
		$extension = strrchr($_FILES['photo']['name'], '.'); // cette fonction prédéfinie permet de découper une chaine selon un caractère fourni en 2eme argument (ici le .). Attention, cette fonction découpera la chaine à partir de la dernière occurence du 2eme argument (donc nous renvoie la chaine comprise après le dernier point trouvé)
		// exemple: maphoto.jpg => on récupère .jpg
		// exemple: maphoto.photo.png => on récupère .png
		// var_dump($extension);
		
		// on transforme $extension afin que tous les caractères soient en minuscule
		$extension = strtolower($extension); // inverse strtoupper()
		// on enlève le .
		$extension = substr($extension, 1); // exemple: .jpg => jpg
		// les extensions acceptées
		$tab_extension_valide = array("jpg", "jpeg", "png", "gif");
		// nous pouvons donc vérifier si $extension fait partie des valeur autorisé dans $tab_extension_valide
		$verif_extension = in_array($extension, $tab_extension_valide); // in_array vérifie si une valeur fournie en 1er argument fait partie des valeurs contenues dans un tableau array fourni en 2eme argument.
		
		if($verif_extension && !$erreur)
		{
			// si $verif_extension est égal à true et que $erreur n'est pas égal à true (il n'y a pas eu d'erreur au préalable)
			$photo_dossier = RACINE_SERVEUR . 'photo/' . $photo_bdd;
			
			copy($_FILES['photo']['tmp_name'], $photo_dossier);
			// copy() permet de copier un fichier depuis un emplacement fourni en premier argument vers un autre emplacement fourni en deuxième argument.
		}
		elseif(!$verif_extension) {
			$message .= '<div class="alert alert-danger" role="alert" style="margin-top: 20px;">Attention, la photo n\' a pas une extension valide (extension acceptées: jpg / jpeg / png / gif)</div>';
			$erreur = true;
		}
		
	}
	if(!$erreur) // équivaut à if($erreur == false)
	{
		
		if(isset($_GET['action']) && $_GET['action'] == 'ajout')
		{
			// insertion des produits		
			$enregistrement = $pdo->prepare("INSERT INTO salle (titre, description, photo, pays, ville, adresse, cp, capacite, categorie) VALUES (:titre, :description, :photo, :pays, :ville, :adresse, :cp, :capacite, :categorie)");
		}
		elseif(isset($_GET['action']) && $_GET['action'] == 'modification')
        {
			$enregistrement = $pdo->prepare("UPDATE salle SET titre = :titre, description = :description, photo = :photo, pays = :pays, ville = :ville, adresse = :adresse, cp = :cp, capacite = :capacite, categorie = :categorie WHERE id_salle = :id_salle");
			$id_salle = $_POST['id_salle'];
			$enregistrement->bindParam(":id_salle", $id_salle, PDO::PARAM_STR);
		}		
		
		$enregistrement->bindParam(":titre", $titre, PDO::PARAM_STR);
		$enregistrement->bindParam(":description", $description, PDO::PARAM_STR);
		$enregistrement->bindParam(":photo", $photo_bdd, PDO::PARAM_STR);
		$enregistrement->bindParam(":pays", $pays, PDO::PARAM_STR);
		$enregistrement->bindParam(":ville", $ville, PDO::PARAM_STR);
		$enregistrement->bindParam(":adresse", $adresse, PDO::PARAM_STR);
		$enregistrement->bindParam(":cp", $cp, PDO::PARAM_STR);
		$enregistrement->bindParam(":capacite", $capacite, PDO::PARAM_STR);
		$enregistrement->bindParam(":categorie", $categorie, PDO::PARAM_STR);
		$enregistrement->execute();

        $message .= '<div class="alert alert-success" role="alert" style="margin-top: 20px;">Bravo la salle a bien été ajoutée !</div>';
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
?>
    <!-- Custom styles for this template -->
    <link href="<?php echo URL; ?>css/style.css" rel="stylesheet">
    </head>

    <body>
    <div class="container">

      <div class="starter-template">
        <h1><span class="glyphicon glyphicon-user" style="color: NavajoWhite;"></span> Gestion des salles</h1>
        <?php // echo $message; // messages destinés à l'utilisateur ?>
		<?= $message; // cette balise php inclue un echo // cette ligne php est equivalente à la ligne au dessus. ?>
		<hr />
		<a href="?action=ajout" class="btn btn-warning">Ajouter une salle</a>
		<a href="?action=affichage" class="btn btn-info">Afficher les salles</a>
      </div>
	
	<?php 
	// affichage de tous les produits dans un tableau html
	// exercice: couper la description si elle est trop longue 
	// exercice: afficher l'image dans une balise <img src="" />
	if(isset($_GET['action']) && $_GET['action'] == 'affichage') 
	{
		$resultat = $pdo->query("SELECT * FROM salle");

		echo '<hr />';
		echo '<div class="row">';
		echo '<div class="col-sm-12">';		
		echo '<table class="table table-bordered" >';
		
		echo '<tr>';
		$nb_colonne = $resultat->columnCount(); // on récupère le nb de colonne
		
		for($i = 0; $i < $nb_colonne; $i++)
		{
			$info_colonne = $resultat->getColumnMeta($i);
			// echo '<pre>'; print_r($info_colonne); echo '</pre>';
			echo '<th>' . $info_colonne['name'] . '</th>';
		}	
		echo '<th>Actions</th>';
		echo '</tr>';
		
		while($room = $resultat->fetch(PDO::FETCH_ASSOC))
		{
			echo '<tr>';
			foreach($room AS $indice => $valeur)
			{
				if($indice == 'photo')
				{
					echo '<td style="width: 20%;">';
					echo '<a href="#" class="thumbnail" data-toggle="modal" data-target="#lightbox">';
					echo '<img src="' . URL . 'photo/' . $valeur . '" class="img-thumbnail" width="140" />';
					echo '</a>';
					echo '</td>';
				}
				elseif($indice == "description") {
					echo '<td style="width: 20%;">' . substr($valeur, 0, 70) . '<a href="../fiche_salle.php?id_produit=' . $room['id_salle'] . '">Voir la fiche article</a></td>';
				}
				else {
					echo '<td style="width: 10%;">' . $valeur . '</td>';
				}
			}
			echo '<td><a class="btn btn-primary"><span class="glyphicon glyphicon-search"></span></a>';
			echo '<a href="?action=modification&id_salle=' . $room['id_salle'] . '" class="btn btn-warning"><span class="glyphicon glyphicon-refresh"></span></a>';
			echo '<a onclick="return(confirm(\'Etes vous sûr\'));" href="?action=suppression&id_salle=' . $room['id_salle'] . '" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></a></td>';
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
			<p> <span style="color: red;">* <small>(champ obligatoire)</small></span></p>
			<form method="post" action="" enctype="multipart/form-data">
				<!-- id_article => caché (hidden) -->
				<input type="hidden"  name="id_salle" id="id_salle" class="form-control" value="<?php echo $id_salle; ?>"/>
				<div class="form-group">
					<label for="titre">Titre <span style="color: red;">*</span></label>
					<input type="text"  name="titre" id="titre" class="form-control" value="<?php echo $titre; ?>" />
				</div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control"><?php echo $description; ?></textarea>
                </div>
                <?php
                // affichage de la photo actuelle dans le cas d'une modification d'article
                if(isset($salle_actuelle)) // si cette variable existe alors nous sommes dans le cas d'une modification
                {
                    echo '<div class="form-group">';
                    echo '<label>Photo actuelle</label><br />';
                    echo '<img src="' . URL . 'photo/' . $photo_actuelle . '" class="img-thumbnail" width="210" />';
                    // on crée un champ caché qui contiendra la nom de la photo afin de le récupérer lors de la validation du formulaire.
                    echo '<input type="hidden" name="ancienne_photo" value="' . $photo_actuelle . '" />';
                    echo '</div>';
                }
                ?>
                <div class="form-group">
                    <label for="photo">Photo</label>
                    <input type="file"  name="photo" id="photo" class="form-control" />
                </div>
                <div class="form-group">
                    <label for="capacite">Capacite</label>
                    <select name="capacite" id="capacite" class="form-control" >
                        <option <?php if($capacite == "10") { echo 'selected'; } ?> >10</option>
                        <option <?php if($capacite == "15") { echo 'selected'; } ?> >15</option>
                        <option <?php if($capacite == "20") { echo 'selected'; } ?> >20</option>
                        <option <?php if($capacite == "25") { echo 'selected'; } ?> >25</option>
                        <option <?php if($capacite == "30") { echo 'selected'; } ?> >30</option>
                        <option <?php if($capacite == "35") { echo 'selected'; } ?> >35</option>
                        <option <?php if($capacite == "40") { echo 'selected'; } ?> >40</option>
                    </select>
                </div>
				<div class="form-group">
					<label for="categorie">Catégorie</label>
					<select name="categorie" id="categorie" class="form-control" >
						<option <?php if($categorie == "reunion") { echo 'selected'; } ?> >Réunion</option>
						<option <?php if($categorie == "bureau") { echo 'selected'; } ?> >Bureau</option>
						<option <?php if($categorie == "formation") { echo 'selected'; } ?> >Formation</option>
					</select>
				</div>
                <div class="form-group">
                    <label for="pays">Pays</label>
                    <select name="pays" id="pays" class="form-control" >
                        <option <?php if($pays == "france") { echo 'selected'; } ?> >France</option>
                        <option <?php if($pays == "suisse") { echo 'selected'; } ?> >Suisse</option>
                        <option <?php if($pays == "belgique") { echo 'selected'; } ?> >Belgique</option>
                        <option <?php if($pays == "portugal") { echo 'selected'; } ?> >Portugal</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="ville">Ville</label>
                    <select name="ville" id="ville" class="form-control" >
                        <option <?php if($ville == "paris") { echo 'selected'; } ?> >Paris</option>
                        <option <?php if($ville == "marseille") { echo 'selected'; } ?> >Marseille</option>
                        <option <?php if($ville == "bordeaux") { echo 'selected'; } ?> >Bordeaux</option>
                        <option <?php if($ville == "toulouse") { echo 'selected'; } ?> >Toulouse</option>
                        <option <?php if($ville == "baal") { echo 'selected'; } ?> >Bale</option>
                        <option <?php if($ville == "geneve") { echo 'selected'; } ?> >Genève</option>
                        <option <?php if($ville == "bruxelles") { echo 'selected'; } ?> >Bruxelles</option>
                        <option <?php if($ville == "liege") { echo 'selected'; } ?> >Liège</option>
                        <option <?php if($ville == "porto") { echo 'selected'; } ?> >Porto</option>
                        <option <?php if($ville == "lisbonne") { echo 'selected'; } ?> >Lisbonne</option>
                    </select>
                </div>
				<div class="form-group">
					<label for="adresse">Adresse</label>
					<textarea name="adresse" id="adresse" class="form-control"><?php echo $adresse; ?></textarea>
				</div>
                <div class="form-group">
                    <label for="cp">Code Postal<span style="color: red;">*</span></label>
                    <input type="text"  name="cp" id="cp" class="form-control" value="<?php echo $cp; ?>" />
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
	<div id="lightbox" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<button type="button" class="close hidden" data-dismiss="modal" aria-hidden="true">×</button>
			<div class="modal-content">
				<div class="modal-body">
					<img src="" alt="" />
				</div>
			</div>
		</div>
	</div>

    </div><!-- /.container -->
	
<?php
require("../inc/footer.inc.php");

















