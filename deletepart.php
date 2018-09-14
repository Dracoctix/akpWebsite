<?php
session_start();
include('includes/database.php');
include('includes/errors.php');
?>
    <!doctype html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Supprimer une participation</title>
        <link rel="icon" href="favicon.ico">
        <link rel="stylesheet" href="style/index.css">
        <meta name="robots" content="noindex,nofollow">
        <?php include('includes/bootstrapcss.html'); ?>
    </head>
<?php
$adminMode = false;
if(isset($_GET['idpart']) && $_GET['idpart']) {
    if(isset($_SESSION['iduser']) && $_SESSION['iduser'] && isset($_SESSION['rank'])) {
        // On vérifie que la participation existe
        $reqParticipation = $bdd->prepare('SELECT * FROM participation WHERE idpart = :idpart');
        $reqParticipation->execute(array("idpart" => $_GET['idpart']));
        if($partInfos = $reqParticipation->fetch()) {
            $reqUtilisateur = $bdd->prepare('SELECT * FROM users WHERE iduser = :iduser');
            $reqUtilisateur->execute(array("iduser" => $_SESSION['iduser']));
            if($userInfos = $reqUtilisateur->fetch()) {
                $retour = "viewop.php?idop=".$partInfos['idop'];
                if ($_SESSION['iduser'] == $partInfos['iduser'] || $userInfos['rank']) {
                    if($userInfos['rank']) {
                        $adminMode = true;
                    }
                    if(isset($_GET['confirmation']) && $_GET['confirmation']) {
                        $suppression = $bdd->prepare('DELETE FROM participation WHERE idpart = :idpart');
                        $suppression->execute(array("idpart" => $_GET['idpart']));
                        ?>
                        <script>
                            alert("La participation a bien été supprimée.");
                            document.location.href="<?php echo $retour; ?>";
                        </script>
                        <?php
                    }
                    else {
                        ?>
                        <body>
                        <div class="container">
                            <div class="row">
                                <h1 class="page-header center col-lg-12">Suppression d'une participation</h1>
                            </div>
                            <?php $notIndex = true;
                            include('navbar.php');?>
                            <div class="alert alert-danger my-4">
                                <div class="row">
                                        <div class="col-lg-12">Vous vous apprêtez à supprimer une participation. Il sera impossible de la récupérer
                                    par la suite. Si vous êtes sûr de vouloir faire ceci, cliquez sur le bouton ci-dessous.
                                    Sinon, vous pouvez revenir sur la page de l'opération pour, éventuellement, modifier
                                    l'information.</div>
                                </div>
                                <form action="deletepart.php" class="center mt-4" method="get">
                                    <input type="hidden" value="<?php echo $_GET['idpart']; ?>" name="idpart" id="idpart">
                                    <input type="hidden" value="1" name="confirmation" id="confirmation">
                                    <button class="btn btn-danger col-lg-12" type="submit"><span class="fas fa-trash"></span> Je confirme la suppression</button>
                                    <a href="viewop.php?idop=<?php echo $partInfos['idop']; ?>" class="btn btn-primary col-lg-12 mt-2"><span class="fas fa-chevron-left"></span> Annuler</a>
                                </form><br>
                            </div>
                        </div>
                        <?php
                        include('includes/scripts.html');
                        ?>
                        </body>
                        <?php
                    }
                }
                else {
                    if($userInfos['rank'] != $_SESSION['rank']) {
                        showError("sessionError");
                    }
                    showError("unauthorizedAction", $retour);
                }
            }
            else {
                showError("sessionError");
            }
        }
        else {
            showError("unknownPart");
        }
    }
    else {
        showError("unlogged");
    }
}
else {
    showError("noIdForUser");
}
?>