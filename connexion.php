<?php
require("inc/init.inc.php");

// deconnexion de l'utilisateur
if(isset($_GET['action']) && $_GET['action'] == 'deconnexion' )
{
	session_destroy();
	//exit();
}


// vérification si l'utilisateur est connecté dans ce cas on le redirige sur profil
if(utilisateur_est_connecte())
{
	header("location:profil.php");
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
		
		// on redirige sur profil
		header("location:profil.php");
		
		// même chose avec un foreach
		/* 
		$_SESSION['utilisateur'] = array();
		foreach($info_utilisateur AS $indice => $valeur)
		{			
			if($indice != 'mdp')
			{
				$_SESSION['utilisateur'][$indice] = $valeur;
			}
		}
		 */
	}
	else {
		$message .= '<div class="alert alert-danger" role="alert" style="margin-top: 20px;">Attention, erreur sur le pseudo ou le mot de passe<br />Veuillez recommencer !</div>';
	}
	
}
// la ligne suivant commence les affichages dans la page
require("inc/header.inc.php");
require("inc/nav.inc.php");
//echo '<pre>'; print_r($_SESSION); echo '</pre>';
?>

    <!-- Custom styles for this template -->
    <link href="<?php echo URL; ?>css/style.css" rel="stylesheet">
    </head>

    <body>
    <div class="container">

      <div class="starter-template">
        <h1><span class="glyphicon glyphicon-user" style="color: Aqua;"></span> Connexion</h1>
        <?php // echo $message; // messages destinés à l'utilisateur ?>
		<?= $message; // cette balise php inclue un echo // cdette ligne php est equivalente à la ligne au dessus. ?>
      </div>
	  <div class="row">
		<div class="col-sm-4 col-sm-offset-4">
			<form method="post" action="">
				<div class="form-group">
					<label for="pseudo">Pseudo</label>
					<input type="text"  name="pseudo" id="pseudo" class="form-control" value="" />
				</div>
				<div class="form-group">
					<label for="mdp">Mot de passe</label>
					<input type="text"  name="mdp" id="mdp" class="form-control" value="" />
				</div>
				<div class="form-group">
					<button type="submit"  name="inscription" id="inscription" class="form-control btn btn-warning"><span class='glyphicon glyphicon-star-empty' style="color: Aqua;"></span> Connexion <span class='glyphicon glyphicon-star-empty' style="color: Aqua;"></span></button>
				</div>
				
			</form>
		</div>
	  </div>
	  

    </div><!-- /.container -->
	
<?php
require("inc/footer.inc.php");

















