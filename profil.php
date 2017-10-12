<?php
require("inc/init.inc.php");

// vérification si l'utilisateur est connecté sinon on le redirige sur connexion
if(!utilisateur_est_connecte())
{
	header("location:connexion.php");
}

$statut = $_SESSION['utilisateur']['statut'];
if($statut == 1)
{
	$role = "Administrateur";
}
else {
	$role = "Membre";
}
// la ligne suivant commence les affichages dans la page
require("inc/header.inc.php");
require("inc/nav.inc.php");
?>
    <!-- Custom styles for this template -->
    <link href="<?php echo URL; ?>css/style.css" rel="stylesheet">
    </head>

    <body>
    <div class="container">

      <div class="starter-template">
        <h1><span class="glyphicon glyphicon-user" style="color: NavajoWhite;"></span> Profil (<?php echo $role; ?>)</h1>
        <?php // echo $message; // messages destinés à l'utilisateur ?>
		<?= $message; // cette balise php inclue un echo // cette ligne php est equivalente à la ligne au dessus. ?>
      </div>
	  <div class="row">
		<div class="col-sm-7">
			<ul class="list-group">
			  <li class="list-group-item active">Vos informations</li>
			  <li class="list-group-item"><span style="display: inline-block; width: 140px; font-style: italic;">Pseudo: </span><?php echo $_SESSION['utilisateur']['pseudo']; ?></li>
			  <li class="list-group-item"><span style="display: inline-block; width: 140px; font-style: italic;">Nom: </span><?php echo $_SESSION['utilisateur']['nom']; ?></li>
			  <li class="list-group-item"><span style="display: inline-block; width: 140px; font-style: italic;">Prénom: </span><?php echo $_SESSION['utilisateur']['prenom']; ?></li>
			  <li class="list-group-item"><span style="display: inline-block; width: 140px; font-style: italic;">Civilite: </span><?php echo $_SESSION['utilisateur']['civilite']; ?></li>
			  <li class="list-group-item"><span style="display: inline-block; width: 140px; font-style: italic;">Email: </span><?php echo $_SESSION['utilisateur']['email']; ?></li>
			</ul>
		</div>
		<div class="col-sm-5">
			<img src="img/profil.jpg" />
		</div>
		<div class="col-sm-12">
			<hr />
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed interdum eleifend congue. Duis facilisis porttitor enim eu accumsan. Maecenas sit amet tempor dui. Integer pharetra sagittis eleifend. Vivamus vitae mollis neque. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur vitae venenatis sem, vel gravida mi. Duis interdum, leo ut faucibus dictum, nisl diam rhoncus est, et condimentum elit lorem sit amet dolor. Aliquam cursus, eros non aliquam convallis, sapien felis lacinia lorem, id efficitur nisl urna ut odio. Donec sagittis volutpat eros. Phasellus in massa ac risus interdum dictum. Sed imperdiet purus id dolor pellentesque varius.<br /><br />

			Donec nec porta nulla. Etiam ultrices sem a nulla tempus, quis interdum nunc tincidunt. Praesent sed scelerisque nisl, sed viverra elit. Nunc faucibus efficitur quam pharetra facilisis. Aenean in tempus lorem. Nullam condimentum euismod lacus, sit amet laoreet enim ultricies sed. Ut vitae lacinia ex. Aliquam efficitur diam nulla. Duis dolor nisl, blandit ut pulvinar bibendum, fermentum blandit libero.</p>
		</div>
	  </div>

    </div><!-- /.container -->
	
<?php
require("inc/footer.inc.php");



