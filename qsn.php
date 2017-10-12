<?php
require("inc/init.inc.php");
require("inc/header.inc.php");
require("inc/nav.inc.php");
?>
 <link href="<?php echo URL; ?>css/shop-homepage.css" rel="stylesheet">
    </head>
  <body>
    <div class="container">

    <div class="starter-template">
        <h1>Qui sommes nous ?</h1>
        <?php // echo $message; // messages destinés à l'utilisateur ?>
		<?= $message; // cette balise php inclue un echo // cette ligne php est equivalente à la ligne au dessus. ?>
      </div>
        <br />
        <p>LOKISALLE est une société proposant la location de salles de réunion à ses clients. </p>        
        <p>Raison sociale : LOKISALLE</p>
        <p>Adresse : 300 Boulevard de Vaugirard, 75015 Paris, France</p>
        <p>Mission : La société est spécialisée dans la location de salle pour l’organisation de réunions par les entreprises ou les particuliers.</p>
        <p>Périmètre géographique de l’activité : La société dispose de salles de réunions à Paris, Lyon et Marseille</p>
        </div>
    </body>
<?php
require("inc/footer.inc.php");