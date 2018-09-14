<?php
session_start();
include('../includes/database.php');
include('../includes/errors.php');
include('../includes/phpmailer.php');
require '../PHPMailer-master/PHPMailerAutoload.php';
?>
    <!doctype html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Panneau d'administration : Utilisateurs</title>
        <meta name="robots" content="noindex,nofollow">
        <link rel="stylesheet" href="../style/index.css">
        <link rel="stylesheet" href="../style/adm.css">
        <?php
        include('../includes/bootstrapcss.html');
        ?>
    </head>
<?php
if(isset($_SESSION['iduser'])) { // Vérifions que l'utilisateur est bien loggé
    $verifAdmin = $bdd->prepare('SELECT * FROM users WHERE iduser = :iduser');
    $verifAdmin->execute(array("iduser" => $_SESSION['iduser']));
    if($infosAdmin = $verifAdmin->fetch()) { // Ici, on s'assure que l'utilisateur existe bien, au cas-où
        if ($infosAdmin['rank']) {
            if (isset($_GET['iduser']) && $_GET['iduser']) {
                $testUser = $bdd->prepare('SELECT * FROM users WHERE iduser = :iduser');
                $testUser->execute(array("iduser" => $_GET['iduser'])); // On vérifie que l'utilisateur existe.
                if ($infosUser = $testUser->fetch()) {
                    if(!$infosUser['active'] || $infosUser['banned']) {
                            if(isset($_GET['validation']) && $_GET['validation']) {
                                if(!$infosUser['active']) { // Si l'utilisateur est inactif, on envoie un maiL.
                        // On supprime l'entrée correspondante.
                                $mail = new PHPMailer;
                                // $mail->SMTPDebug = 3;
                                $mail->setLanguage('fr');
                                $mail->isSMTP();
                                $mail->Host = SMTP_SERVER;
                                $mail->SMTPAuth = true;
                                $mail->Username = SMTP_USER;
                                $mail->Password = SMTP_PASSWORD;
                                $mail->SMTPSecure = 'tls';
                                $mail->Port = 587;
                                $mail->CharSet = "UTF-8";
                                $mail->setFrom(MAIL_POSTER, 'AKP Business Team');
                                $mail->addAddress($infosUser['email'], $infosUser['username']);
                                //$mail->addAddress('web-xgnix@mail-tester.com', $infosUser['username']);

                                $mail->isHTML(true);
                                $mail->Subject = "Votre compte sur le l'AKP Business Team a été refusé par un Administrateur !";
                                $mail->Body = "<p>Bonjour <strong>" . $infosUser['username'] . "</strong>.</p>
                                          <a>Je suis désolé de vous informer que votre compte sur l'AKP Business Team vient d'être
                                          supprimé par un administrateur. Si vous pensez qu'il s'agit d'une erreur, contactez un administrateur sur Urban Rivals.</p>";
                                $mail->AltBody = "Bonjour, " . $infosUser['username'] . ". Votre compte sur l'AKP Business Team a été supprimé
                            par un administrateur. Si vous pensez qu'il s'agit d'une erreur, contactez un administrateur sur Urban Rivals.
                            À bientôt !";
                                if (!$mail->send()) {
                                    ?>
                                    <script>
                                        alert("Le mail n'a pas pu être envoyé. L'erreur est la suivante : <?php echo $mail->ErrorInfo; ?>");
                                    </script>
                                <?php
                                }
                                }
                                $activation = $bdd->prepare('DELETE FROM users WHERE iduser = :iduser');
                                $activation->execute(array(
                                    "iduser" => $_GET['iduser']
                                ));
                            ?>
                                <script>
                                    alert("Le compte de <?php echo $infosUser['username']; ?> a bien été supprimé.");
                                    document.location.href="index.php";
                                </script>
                            <?php
                            }
                            else {
                                ?>
                                <body>
                                <div class="container">
                                    <div class="row">
                                        <header class="center col-lg-12">
                                            <h1 class="page-header">Supprimer le compte de <?php echo $infosUser['username']; ?></h1>
                                        </header>
                                    </div>
                                    <div class="row">
                                        <?php
                                        $page = "delete";
                                        include "menu.php";
                                        ?>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class=" mt-4 alert alert-warning">
                                                <div class="row"><p>Vous vous apprêtez à supprimer le compte de <strong><?php echo $infosUser['username']; ?></strong>. Il ne pourra donc plus
                                                accéder à la plateforme. En outre, ses participations aux opérations seront effacées.
                                                Les opérations dont il est l'auteur seront modifiées, et leur créateur deviendra Inconnu.
                                                        Si vous êtes sûr de vouloir faire ceci, cliquez sur le bouton ci-dessous.</p></div>
                                                <form action="delete.php" method="get">
                                                    <input type="hidden" value="<?php echo $infosUser['iduser']; ?>" name="iduser" id="iduser">
                                                    <input type="hidden" value="1" name="validation" id="validation">
                                                    <div class="row d-flex justify-content-center">
                                                        <button class="col-lg-6 col-md-12 btn btn-danger" type="submit"><span class="fas fa-trash"></span> Confirmer la suppression</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                include "../includes/scripts.html";
                        }
            }
            else {
            ?>
            <script>
                alert("Le compte de <?php echo $infosUser['username']; ?> est actif et ne peut être supprimé.");
                document.location.href="index.php"
            </script>
             <?php
            }

                }else {
                showError("noIdForUser");
            }
        } else {
            showError("notAdmin", "../index.php");
        }
    }
    else {
        showError("sessionError", "../index.php");
    } } }
else {
    showError("unlogged", "../index.php");
}