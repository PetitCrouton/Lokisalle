<?php
require("inc/init.inc.php");

//*******************************************************
// ENREGISTREMENT DES ARTICLES
//*******************************************************

$id_membre = $_SESSION['utilisateur']['id_membre'];
$id_salle ="";
$commentaire ="";
$note ="";
$pseudo = "";

// déclaration d'un variable de contrôle
$erreur = "";


if( isset($_POST["id_membre"]) && isset($_POST["id_salle"]) && isset($_POST["commentaire"]) && isset($_POST["note"]))
{
    $id_membre = $_SESSION['utilisateur']['id_membre'];
    $id_salle = $_POST["id_salle"];
    $commentaire = $_POST["commentaire"];
    $note = $_POST["note"];

    // verification si le titre n'est pas vide
    if(empty($commentaire))
    {
        $message .= '<div class="alert alert-danger" role="alert" style="margin-top: 20px;">Attention, votre commentaire ne peut être vide</div>';
        $erreur = true;
    }

    if(!$erreur) // équivaut à if($erreur == false)
    {
        // insertion des produits
        $enregistrement = $pdo->prepare("INSERT INTO avis (id_salle, id_membre, commentaire, note, date_enregistrement) VALUES (:id_salle, :id_membre, :commentaire, :note, NOW())");


        $enregistrement->bindParam(":id_salle", $id_salle, PDO::PARAM_STR);
        $enregistrement->bindParam(":id_membre", $id_membre, PDO::PARAM_STR);
        $enregistrement->bindParam(":commentaire", $commentaire, PDO::PARAM_STR);
        $enregistrement->bindParam(":note", $note, PDO::PARAM_STR);
        $enregistrement->execute();

        $message .= '<div class="alert alert-success" role="alert" style="margin-top: 20px;">Merci d\'avoir pris le temps de nous donner votre avis!</div>';
    }
}
$liste_salle = $pdo->query("SELECT * FROM salle ORDER BY id_salle");





// la ligne suivant commence les affichages dans la page
require("inc/header.inc.php");
require("inc/nav.inc.php");
// echo '<pre>'; print_r($_POST); echo '</pre>';
if(utilisateur_est_connecte()) {
    ?>
    <!-- Custom styles for this template -->
    <link href="<?php echo URL; ?>css/style.css" rel="stylesheet">
    </head>

    <body>
    <div class="container">
        <div class="row" style="margin-top:40px;">
            <div class="col-sm-4 col-sm-offset-4">

                <form method="post" action="" enctype="multipart/form-data">
                    <!-- id_membre => caché (hidden) -->
                    <input type="hidden"  name="id_membre" id="id_membre" class="form-control" value="<?php echo $id_membre; ?>"/>

                    <div class="form-group">
                        <label for="id_membre">Pseudo <span style="color: red;">*</span></label>
                        <input type="text"  name="id_membre" id="id_membre" class="form-control" value="<?php echo $pseudo; ?>" />
                    </div>
                    <div class="form-group">
                        <label for="commentaire">Commentaire</label>
                        <textarea name="commentaire" id="commentaire" class="form-control"><?php echo $commentaire; ?></textarea>
                    </div>
                    <?php
                    echo'<div class="form-group">';
                    echo '<label for="id_salle">Salle</label>';
                    echo '<select name="id_salle" id="id_salle" class="form-control" >';
                    while($salles = $liste_salle->fetch(PDO::FETCH_ASSOC))
                    {
                        echo '<option value="' .$salles['id_salle'] . '">' . $salles['id_salle'] . ' - ' . $salles['titre'] . ' - ' . $salles['adresse'] . ', ' . $salles['cp'] . ', ' . $salles['ville'] . ' - ' . $salles['capacite'] . '</option>';
                    }
                    echo'</select>';
                    echo'</div>';
                    ?>

                    <div class="form-group">
                        <label for="note">Note</label>
                        <select name="note" id="note" class="form-control" >
                            <option <?php if($note == "1") { echo 'selected'; } ?> >1</option>
                            <option <?php if($note == "2") { echo 'selected'; } ?> >2</option>
                            <option <?php if($note == "3") { echo 'selected'; } ?> >3</option>
                            <option <?php if($note == "4") { echo 'selected'; } ?> >4</option>
                            <option <?php if($note == "5") { echo 'selected'; } ?> >5</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit"  name="enregistrement" id="enregistrement" class="form-control btn btn-primary"><span class='glyphicon glyphicon-star-empty' style="color: NavajoWhite;"></span> Enregistrement <span class='glyphicon glyphicon-star-empty' style="color: NavajoWhite;"></span></button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <?php
}
require("inc/footer.inc.php");