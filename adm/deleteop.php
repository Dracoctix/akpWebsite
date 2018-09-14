<?php
session_start();
include('../includes/database.php');
include('../includes/errors.php');
?>
    <!doctype html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Panneau d'administration : Opérations</title>
        <meta name="robots" content="noindex,nofollow">
        <link rel="icon" href="../favicon.ico">
        <link rel="stylesheet" href="../style/index.css">
        <link rel="stylesheet" href="../style/adm.css">
        <?php
        include('../includes/bootstrapcss.html');
        ?>
    </head>
    <body>
<?php
if(isset($_SESSION['iduser'])) { // Vérifions que l'utilisateur est bien loggé
    $verifAdmin = $bdd->prepare('SELECT * FROM users WHERE iduser = :iduser');
    $verifAdmin->execute(array("iduser" => $_SESSION['iduser']));
    if($infosAdmin = $verifAdmin->fetch()) { // Ici, on s'assure que l'utilisateur existe bien, au cas-où
        if ($infosAdmin['rank']) { // On vérifie que l'utilisateur soit toujours admin
            if (isset($_GET['idop']) && $_GET['idop']) {
                $testOp = $bdd->prepare('SELECT * FROM op WHERE idop = :idop');
                $testOp->execute(array("idop" => $_GET['idop'])); // On vérifie que l'utilisateur existe.
                if ($infosOp = $testOp->fetch()) {
                    if(!isset($_GET['confirmation']) || !$_GET['confirmation']) {
                        ?>
                        <div class="container">
                            <div class="row">
                                <header class="col-lg-12 center">
                                    <h1 class="page-header">Supprimer l'opération "<?php echo $infosOp['titre']; ?>"</h1>
                                </header>
                            </div>
                            <div class="row">
                                <?php
                                $page = 'delOp';
                                include 'menu.php';
                                ?>
                            </div>
                            <div class="row mt-4">
                                <div class="col-lg-12">
                                    <div class="alert alert-warning">
                                        <div class="row">
                                            <p>Vous vous apprêtez à supprimer l'opération
                                                "<strong><?php echo $infosOp['titre']; ?></strong>".
                                                Celle-ci sera donc définitivement supprimée, de même que les informations ajoutées par les
                                                différents participants, et il sera impossible de récupérer les informations supprimées.
                                                Si vous êtes conscient  de ce que cela implique, cliquez sur le bouton ci-dessous.</p>
                                        </div>
                                        <form action="deleteop.php" method="get">
                                            <input type="hidden" value="<?php echo $infosOp['idop']; ?>" name="idop" id="idop">
                                            <input type="hidden" value="1" name="confirmation" id="confirmation">
                                            <div class="row d-flex justify-content-center">
                                                <button class="btn btn-danger col-lg-6 col-md-12" type="submit"><span class="fas fa-trash"></span> Supprimer l'opération</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    include '../includes/scripts.html';
                    }
                    else {
                    $suppression = $bdd->prepare('DELETE FROM op WHERE idop = :idop');
                    $suppression->execute(array("idop" => $_GET['idop']));
                    ?>
                        <script>
                            alert("L'opération \"<?php echo $infosOp['titre']; ?>\" a bien été supprimée.");
                            document.location.href="op.php";
                        </script>
                    <?php
                    }
                }
                else {
                    showError("unknownOp", "op.php");
                }
            }
            else {
                showError("noIdForUser", "op.php");
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
?></body>