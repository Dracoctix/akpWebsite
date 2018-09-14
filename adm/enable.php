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
                    if(!$infosUser['active']) {
                        $activation = $bdd->prepare('UPDATE users SET active = 1 WHERE iduser = :iduser');
                        $activation->execute(array(
                            "iduser" => $_GET['iduser']
                        )); // On effectue la modification sur la table de l'utilisateur, en mettant son activation à 1.
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
                        $mail->Subject = "Votre compte sur l'AKP Business Team a été activé par un Administrateur !";
                        $mail->Body = "<p>Bonjour <strong>" . $infosUser['username'] . "</strong>.</p>
                                  <a>J'ai la joie de vous informer que votre compte sur l'AKP Business Team vient d'être
                                  validé par un administrateur. Vous pouvez dès maintenant <a href='https://www.alakapuri.tk/index.php'>
                                  vous connecter</a>. À bientôt !</p>";
                        $mail->AltBody = "Bonjour, " . $infosUser['username'] . ". Votre compte sur l'AKP Business Team a été validé
                    par un administrateur. Vous pouvez maintenant vous y connecter en utilisant vos identifiants.
                    À bientôt !";
                        if (!$mail->send()) {
                            ?>
                            <script>
                                alert("Le mail n'a pas pu être envoyé. L'erreur est la suivante : <?php echo $mail->ErrorInfo; ?>");
                            </script>
                        <?php
                        }
                    ?>
                        <script>
                            alert("Le compte de <?php echo $infosUser['username']; ?> a bien été activé.");
                            document.location.href="index.php";
                        </script>
                        <?php
                    }
                    else {
                        ?>
                        <script>
                            alert("Le compte de <?php echo $infosUser['username']; ?> est déjà activé.");
                            document.location.href="index.php"
                        </script>
                        <?php
                    }
                } else {
                    showError("unknownUser");
                }
            } else {
                showError("noIdForUser");
            }
        } else {
            showError("notAdmin", "../index.php");
        }
    }
    else {
        showError("sessionError", "../index.php");
    }
}
else {
    showError("unlogged", "../index.php");
}