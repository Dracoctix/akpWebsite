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
        <link rel="icon" href="../favicon.ico">
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
        if ($infosAdmin['rank']) { // On vérifie que l'utilisateur soit toujours admin
            if (isset($_GET['iduser']) && $_GET['iduser']) {
                $testUser = $bdd->prepare('SELECT * FROM users WHERE iduser = :iduser');
                $testUser->execute(array("iduser" => $_GET['iduser'])); // On vérifie que l'utilisateur existe.
                if ($infosUser = $testUser->fetch()) {
                    ?>
                    <body>
                    <div class="container">
                    <?php
                    if(!$infosUser['rank']) {
                        if(!isset($_GET['confirmation']) || !$_GET['confirmation']) {
                        ?>
                        <div class="row">
                            <header class="col-lg-12 center">
                                <h1 class="page-header">Promouvoir le compte de <?php echo $infosUser['username']; ?></h1>
                            </header>
                        </div>
                        <div class="row">
                            <?php
                            $page = "promote";
                            include "menu.php";
                            ?>
                        </div>
                        <div class="row mt-4">
                            <div class="col-lg-12">
                                <div class="alert alert-warning">
                                    <div class="row">
                                        <p>Vous vous apprêtez à promouvoir le compte de
                                            <strong><?php echo $infosUser['username']; ?></strong>.
                                            Celui-ci pourra donc accéder au panneau d'administration, et tout modifier. Si vous êtes
                                            conscient
                                            de ce que cela implique, cliquez sur le bouton ci-dessous.</p>
                                    </div>
                                        <form action="promote.php" method="get">
                                            <input type="hidden" value="<?php echo $infosUser['iduser']; ?>" name="iduser" id="iduser">
                                            <input type="hidden" value="1" name="confirmation" id="confirmation">
                                            <div class="row d-flex justify-content-center">
                                                <button class="btn btn-success col-lg-6 col-md-12" type="submit"><span class="fas fa-arrow-up"></span> Confirmer la promotion</button>
                                            </div>
                                        </form>
                                </div>
                            </div>
                        </div>
                    </div>
                        <?php
                        include "../includes/scripts.html";
                        }
                        else {
                            $promotion = $bdd->prepare('UPDATE users SET rank=1 WHERE iduser = :iduser');
                            $promotion->execute(array("iduser" => $_GET['iduser']));
                            ?>
                            <script>
                                alert("<?php echo $infosUser['username']; ?> a bien été promu.");
                                document.location.href="index.php";
                            </script>
                            <?php
                        }
                    }
                    else {
                    ?>
                        <script>
                            alert("Le compte que vous tentez de promouvoir est déjà administrateur.");
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
}?>
    </body></html>
