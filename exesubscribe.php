<?php
session_start();
include('includes/database.php');
include('includes/errors.php');
?>
    <!doctype html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Inscription</title>
        <meta name="robots" content="noindex,nofollow"/>
    </head>
<?php
if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email']) && isset($_POST['confirmation']) && !isset($_SESSION['iduser']) && isset($_POST['description'])) {
    $_SESSION['errorUsername'] = $_POST['username'];
    $_SESSION['errorEmail'] = $_POST['email'];
    $_SESSION['errorDesc'] = $_POST['description'];
    if($_POST['username'] != NULL && $_POST['password'] != NULL && $_POST['confirmation'] != NULL && $_POST['email']) {
        $username = htmlspecialchars($_POST['username']);
        $password = $_POST['password'];
        $confirmation = $_POST['confirmation'];
        $email = htmlspecialchars($_POST['email']);
        $description = htmlspecialchars($_POST['description']);
        if(strlen($username) <= 255 && strlen($username) >= 3) {
                if(strlen($password) <= 255 && strlen($password)) {
                    if(strlen($email) <= 255 && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        if ($password == $confirmation) {
                            $verifUsername = $bdd->prepare('SELECT * FROM users WHERE username = :username');
                            $verifUsername->execute(array("username" => $username));
                                if ($verifUsername->fetch()) {
                                    ?>
                                    <script>
                                        alert("Le nom d'utilisateur demandé existe déjà.");
                                        document.location.href = "subscribe.php";
                                    </script>
                                    <?php
                                }
                                else {
                                    $verifEmail = $bdd->prepare('SELECT * FROM users WHERE email = :email');
                                    $verifEmail->execute(array("email" => $email));
                                    if ($verifEmail->fetch()) {
                                    ?>
                                        <script>
                                            alert("Un compte existe déjà avec cette adresse mail.");
                                            document.location.href = "subscribe.php";
                                        </script>
                                    <?php
                                    }
                                    else {
                                        $hashedPass = password_hash($password, PASSWORD_DEFAULT);
                                        $addUser = $bdd->prepare('INSERT INTO users (username, password, email, description)
                                                                             VALUES (:username, :password, :email, :description)');
                                        $addUser->execute(array(
                                            "username" => $username,
                                            "password" => $hashedPass,
                                            "email"    => $email,
                                            "description" => $description
                                        ));
                                        $_SESSION['errorUsername'] = $_SESSION['errorEmail'] = $_SESSION['errorDesc'] = NULL;
                                        ?>
                                        <script>
                                            alert("Votre compte a bien été créé. Toutefois, il requiert l'activation " +
                                                "par l'administrateur.");
                                            document.location.href="index.php";
                                        </script>
                                        <?php
                                    }
                                }

                        }
                        else {
                            ?>
                            <script>
                                alert("Le mot de passe et la confirmation ne correspondent pas.");
                                document.location.href="subscribe.php";
                            </script>
                            <?php
                        }
                    }
                    else {
                        ?>
                        <script>
                            alert("L'adresse mail n'en est pas une" +
                                " ou elle est trop longue. Elle ne doit pas excéder 255 caractères.")
                        </script>
                        <?php
                    }
            }
            else {
                ?>
                <script>
                    alert("Le mot de passe doit comprendre entre 3 et 255 caractères.");
                    document.location.href="subscribe.php";
                </script>
                <?php
            }
        }
        else {
            ?>
            <script>
                alert("Le nom d'utilisateur doit comprendre entre 3 et 255 caractères.");
                document.location.href="subscribe.php";
            </script>
            <?php
        }
    }
    else {
        showError("saisieVide", "subscribe.php");
    }
}
else {
    showError("wtf");
}
?>