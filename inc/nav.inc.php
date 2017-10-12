    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo URL; ?>boutique.php">Lokisalle</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li><a href="<?php echo URL; ?>qsn.php">Qui Sommes Nous</a></li>
            <li><a href="<?php echo URL; ?>contact.php">Contact</a></li>
            <li><a href="<?php echo URL; ?>panier.php">Panier</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
              <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-user"></span> Espace Membre <span class="caret"></span></a>
              <ul class="dropdown-menu">
              <?php 
                    if(!utilisateur_est_connecte())
                    {
              ?>			
                          <li><a href="<?php echo URL; ?>inscription.php">Inscription</a></li>
                          <li><a href="<?php echo URL; ?>connexion.php">Connexion</a></li>			
              <?php 
                    }
                    else {  
              ?>
                          <li><a href="<?php echo URL; ?>profil.php">Profil</a></li>
                          <li><a href="<?php echo URL; ?>connexion.php?action=deconnexion">Déconnexion</a></li>
           
              <?php
                }		
              ?>
            
            </ul>

      <?php
			// rajout des liens d'administration si l'utilisateur est admin
			if(utilisateur_est_admin())
			{
				echo '<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Administration <span class="caret"></span></a>';
				echo '<ul class="dropdown-menu">';
				echo '<li><a href="' . URL . 'admin/gestion_boutique.php">Gestion salles</a></li>';
				echo '<li><a href="' . URL . 'admin/gestion_commande.php">Gestion commandes</a></li>';
				echo '<li><a href="' . URL . 'admin/gestion_membres.php">Gestion membres</a></li>';
				echo '<li><a href="' . URL . 'admin/gestion_produit.php">Gestion produits</a></li>';
				echo '<li><a href="' . URL . 'admin/gestion_avis.php">Gestion avis</a></li>';

				echo '</ul></li>';
			}			
		?>
       
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>