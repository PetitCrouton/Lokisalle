<?php
require("inc/init.inc.php");


// formulaire contact
 /*
$expediteur = $_POST['expediteur'];
$email = $_POST['email'];
$tel = $_POST['tel'];
$sujet = $_POST['sujet'];
$message = $_POST['message']; */

if(isset($_POST['expediteur']) && isset($_POST['email']) && isset($_POST['tel']) && isset($_POST['sujet']) && isset($_POST['message']))
{
    $expediteur = $_POST['expediteur'];
    $email = $_POST['email'];
    $tel = $_POST['tel'];
    $sujet = $_POST['sujet'];
    $message = $_POST['message'];

    //envoi d'un mail via la fonction prédéfinie mail()
    $expediteur = "FROM: $expediteur \n";
    $expediteur .= "MIME-Version: 1.0 \r\n";
    $expediteur .= "Content-type: text/html; charset=iso-8859-1 \r\n";

    mail("geraldine.gabas@gmail.com", $sujet, $email, $tel, $message);
}

require("inc/header.inc.php");
require("inc/nav.inc.php");
?>
 <link href="<?php echo URL; ?>css/shop-homepage.css" rel="stylesheet">
    </head>
<body>
 <section id="contact">
            <div class="container">
                <div class="row">
                    <div class="col-sm-8 col-sm-push-2 col-xs-10 col-xs-push-1">
                      <h3>Contact</h3>
                    
                        <form action="contact.php" id="contact-form"
                            class="form-horizontal" method="post">
                        
                            <!-- Nom et Prénom -->
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <input type="text" class="form-control" name="expediteur"
                                        placeholder="Pseudo" required>
                                </div>
                            </div>

                            <!-- Email et Téléphone -->
                            <div class="form-group"> 
                                <div class="col-sm-6">
                                    <input type="email" class="form-control"
                                        placeholder="Saisissez votre Email" name="email" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-6">
                                    <input type="tel" class="form-control"
                                        placeholder="Saisissez votre Telephone" name="tel" required>
                                </div>
                            </div>

                            <!-- Sujet -->
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <input type="text" class="form-control"
                                        placeholder="Saisissez votre Sujet" name="sujet" required>
                                </div>
                            </div>

                            <!-- Message -->
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <textarea id="message" rows="5" class="form-control" name="message"
                                        placeholder="Message..."></textarea>
                                </div>
                            </div>

                            <!-- Bouton d'Envoi -->
                            <div class="form-group">
                                <div class="col-xs-12">
                                   <button type="submit" class="btn btn-primary"
                                    value="Envoyer ma Demande">Envoyer ma Demande</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </section>

<?php
require("inc/footer.inc.php");
