<?php
session_start();
include('includes/database.php');
include('includes/errors.php');
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Connexion</title>
    <meta name="robots" content="noindex,nofollow"/>
</head>
<?php
if(isset($_POST['username']) && isset($_POST['password']) && !isset($_SESSION['iduser'])) {
    if($_POST['username'] != NULL && $_POST['password'] != NULL) {
        $verifUser = $bdd->prepare('SELECT * FROM users WHERE username = :username');
        $verifUser->execute(array("username" => htmlspecialchars($_POST['username'])));
        if($infosUser = $verifUser->fetch()) {
            if(password_verify($_POST['password'], $infosUser['password'])) {
                if($infosUser['active'] && !$infosUser['banned']) { // On vérifie que l'utilisateur ait bien utilisé le formulaire legit, et on vérifie qu'il ait spécifié des identifiants corrects.
                    $_SESSION['iduser'] = $infosUser['iduser'];
                    $_SESSION['rank'] = $infosUser['rank'];
                    $_SESSION['username'] = $infosUser['username'];
                    $_SESSION['email'] = $infosUser['email']; // On ajoute dans une session les différentes informations dont on pourrait avoir besoin, pour limiter les appels à la BDD.
                    ?>
                    <script>
                        document.location.href = "index.php";
                    </script>
                    <?php
                }
                else {
                    $_SESSION['errorUsername'] = $_POST['username'];
                    showError("inactive");
                }
            }
            else {
                $_SESSION['errorUsername'] = $_POST['username'];
                showError("loginInvalide");
            }
        }
        else {
            $_SESSION['errorUsername'] = $_POST['username'];
            showError("loginInvalide");
        }
    }
    else {
        showError("saisieVide");
    }
}
else {
    showError("wtf");
}
?>
</html>
