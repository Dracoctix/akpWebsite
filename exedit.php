<?php
session_start();
include('includes/database.php');
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex,nofollow">
    <title>Modification des informations</title>
    <?php
    if(isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['newpassword']) && isset($_POST['description'])
        && isset($_POST['confirmation']) && isset($_SESSION['iduser']) && $_SESSION['iduser'] != '') {
        $_SESSION['descErreur'] = $_POST['description'];
        $recupUser = $bdd->prepare('SELECT * FROM users WHERE iduser = :iduser');
        $recupUser->execute(array('iduser' => $_SESSION['iduser']));
    if($userInfo = $recupUser->fetch()) {
    if(password_verify($_POST['password'], $userInfo['password'])) {
        $newUsername = $userInfo['username'];
        $newEmail = $userInfo['email'];
        $newPassword = $userInfo['password'];
        $newDesc = $userInfo['description'];
        $edited = False;
        $desc = htmlspecialchars($_POST['description']);
        $username = htmlspecialchars($_POST['username']);
        $email = htmlspecialchars($_POST['email']);

    if($username != '' && $username != $userInfo['username']) {
        // L'utilisateur a saisi un nom différent du sien, on part donc du principe qu'il veut le changer, et on fait les tests en conséquence.
    if(strlen($username) >= 3 && strlen($username) <= 255) {
        // Le nom d'utilisateur spécifié est potentiellement valable. Néanmoins, on doit vérifier s'il existe un utilisateur disposant du même pseudo. Si c'est le cas, on lui refuse le changement.
        $verifUsername = $bdd->prepare('SELECT * FROM users WHERE username = :username');
        $verifUsername->execute(array('username' => $username));
    if(!($verifUsername->fetch())) {
        // Si la condition est vraie, tout est bon. On peut procéder au changement.
        $newUsername = $username;
        $edited = True;
    }
    else {
        ?>
        <script>
            alert("Une erreur est survenue lors de la modification du nom d'utilisateur : Un utilisateur existe déjà avec ce nom.");
        </script>
    <?php
    }
    }
    else {
    ?>
        <script>
            alert('Une erreur est survenue lors de la modification du nom d\'utilisateur : Il doit comporter 3 à 255 caractères.');
        </script>
    <?php
    }
    }
    if($desc != '' && $desc != $userInfo['description']) {
        // L'utilisateur veut changer sa description
        $newDesc = $desc;
        $edited = true;
    }
    if($email != '' && $email != $userInfo['email']) {
    // L'utilisateur semble vouloir changer d'email. On fait donc les vérifications qui s'imposent.
    if(strlen($email) <= 255) {
        if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $verifEmail = $bdd->prepare('SELECT * FROM users WHERE email = :email');
            $verifEmail->execute(array('email' => $email));
            if(!($verifEmail->fetch())) {
                $newEmail = $email;
                $edited = True;
            }
            else {
            ?>
                <script>
                    alert("Une erreur est survenue lors de la modification de l'adresse mail : L'adresse mail est déjà utilisée.")
                </script>
            <?php
            }
        }
        else {
        ?>
            <script>
                alert("Une erreur est survenue lors de la modification de l'adresse mail : L'adresse mail n'est pas valide.")
            </script>
        <?php
        }
    }
    else {
    ?>
        <script>
            alert("Une erreur est survenue lors de la modification de l'adresse mail : L'adresse mail ne peut excéder 255 caractères.");
        </script>
    <?php
    }
    }
    if($_POST['newpassword'] != '') {
    if(strlen($_POST['newpassword']) >= 3 && strlen($_POST['newpassword']) <= 255) {
    if($_POST['newpassword'] == $_POST['confirmation']) {
        $newPassword = password_hash($_POST['newpassword'], PASSWORD_DEFAULT);
        $edited = True;
    }
    else {
    ?>
        <script>
            alert("Les deux mots de passe ne correspondent pas.");
        </script>
    <?php
    }
    }
    else {
    ?>
        <script>
            alert("Une erreur est survenue durant la modification du mot de passe : Celui-ci doit comprendre entre 3 et 255 caractères.');
        </script>
    <?php
    }
    }

    if($edited) {
    $editProfil = $bdd->prepare('UPDATE users SET username = :newUsername, password = :newPassword, email = :newEmail, description = :description WHERE iduser = :iduser');
    $editProfil->execute(array(
        'newUsername' => $newUsername,
        'newPassword' => $newPassword,
        'newEmail' => $newEmail,
        'description' => $newDesc,
        'iduser'=> $_SESSION['iduser']
    ));
    $_SESSION['username'] = $newUsername;
    $_SESSION['email'] = $newEmail;
    ?>
        <script>
            alert('Vos informations ont correctement été modifiées.');
            document.location.href="edituser.php";
        </script>
    <?php
    }
    else {
    ?>
        <script>
            alert("Aucune modification n'a été demandée. Vos informations sont inchangées.");
            document.location.href="edituser.php";
        </script>
    <?php
    }
    $_SESSION['descErreur'] = NULL;
    }
    else {
    ?>
        <script>
            alert('Le mot de passe que vous avez indiqué est incorrect.');
            document.location.href="edituser.php";
        </script>
    <?php
    }
    }
    else {
    session_destroy();
    ?>
        <script>
            alert('Une erreur est survenue. Veuillez vous reconnecter.');
            document.location.href="index.php"
        </script>
    <?php
    }
    $recupUser->closeCursor();
    }
    else {
    ?>
        <script>
            document.location.href="edituser.php";
        </script>
        <?php
    }
    ?>
</head>
</html>
