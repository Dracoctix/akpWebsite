<?php
session_start();
include('../includes/database.php');
include('../includes/errors.php');
?>
    <!doctype html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Panneau d'administration : Utilisateurs</title>
        <meta name="robots" content="noindex,nofollow">
    </head>
<?php
if(isset($_SESSION['iduser'])) { // Vérifions que l'utilisateur soit bien loggé
    $verifAdmin = $bdd->prepare('SELECT * FROM users WHERE iduser = :iduser');
    $verifAdmin->execute(array("iduser" => $_SESSION['iduser']));
    if($infosAdmin = $verifAdmin->fetch()) { // Ici, on s'assure que l'utilisateur existe bien, au cas-où
        if ($infosAdmin['rank']) { // On vérifie que l'utilisateur soit toujours admin
            if (isset($_GET['iduser']) && $_GET['iduser']) {
                $testUser = $bdd->prepare('SELECT * FROM users WHERE iduser = :iduser');
                $testUser->execute(array("iduser" => $_GET['iduser'])); // On vérifie que l'utilisateur existe.
                if ($infosUser = $testUser->fetch()) {
                    if($infosUser['rank']) {
                        if(!($infosUser['iduser'] == $_SESSION['iduser'])) {
                        $destitution = $bdd->prepare('UPDATE users SET rank=0 WHERE iduser = :iduser');
                        $destitution->execute(array("iduser" => $_GET['iduser']));
                        ?>
                        <script>
                            alert("<?php echo $infosUser['username']; ?> a bien été déstitué.");
                            document.location.href = "index.php";
                        </script>
                        <?php
                        }
                        else {
                            ?>
                            <script>
                                alert("Il est impossible de se déstituer soi-même.");
                                document.location.href="index.php";
                            </script>
                            <?php
                        }
                    }
                    else {
                    ?>
                        <script>
                            alert("Le compte que vous tentez de déstituer n'est pas administrateur.");
                            document.location.href="index.php";
                        </script>
                        <?php
                    }
                }
                else {
                    showError("unknownUser");
                }
            }
            else {
                showError("noIdForUser");
            }
        }
        else {
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