<?php
session_start();
include('includes/database.php');
include('includes/errors.php');
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Édition d'un commentaire</title>
    <meta name="robots" content="noindex,nofollow">
    <link rel="stylesheet" type="text/css" href="style/index.css">
    <?php
    include('includes/bootstrapcss.html');
    ?>
</head>
<?php
if(isset($_SESSION['iduser']) && $_SESSION['iduser'] && isset($_SESSION['rank'])) {
    if(isset($_GET['idcomment']) && $_GET['idcomment']) {
        $reqComment = $bdd->prepare('SELECT * FROM comments WHERE idcomment = :idcomment');
        $reqComment->execute(array("idcomment" => $_GET['idcomment']));
        if($infosComment = $reqComment->fetch()) {
            $reqUser = $bdd->prepare('SELECT * FROM users WHERE iduser = :iduser AND active = 1 AND banned = 0');
            $reqUser->execute(array("iduser" => $_SESSION['iduser']));
            if($infosUser = $reqUser->fetch()) {
                $redirectPage = 'viewop.php?idop='.$infosComment['idop'];
                if($infosComment['iduser'] == $_SESSION['iduser'] || $infosUser['rank']) {
                    $reqOp = $bdd->prepare('SELECT * FROM op WHERE idop = :idop');
                    $reqOp->execute(array("idop" => $infosComment['idop']));
                    $infosOp = $reqOp->fetch();
                    $commentaireDefault = (isset($_SESSION['erroredComment']) && $_SESSION['erroredComment']) ? $_SESSION['erroredComment'] : $infosComment['text'];
                    ?>
                    <body>
                        <div class="container">
                            <div class="row">
                                <h1 class="page-header center col-lg-12">Modifier le commentaire sur l'opération "<?php echo $infosOp['titre']; ?>"</h1>
                            </div>
                            <div class="row">
                                <?php
                                include('navbar.php');
                                ?>
                            </div>
                            <div class="row">
                                <form action="exeditcomment.php" class="col-lg-12 form-vertical" method="POST">
                                    <div class="row">
                                        <div class="form-group">
                                            <label for="commentContent" class="form-control-label col-lg-12">Contenu du commentaire :</label>
                                        </div>
                                    </div>
                                    <div class="row d-flex justify-content-center">
                                        <div class="form-group input-group">
                                            <textarea name="commentContent" id="commentContent" placeholder="Contenu du commentaire..."
                                                  rows="25" class="col-lg-12 form-control" autofocus><?php echo $commentaireDefault; ?></textarea>
                                            <small id="commentContentText" class="form-text text-muted droite col-lg-12">Les commentaires doivent comporter plus de 3 caractères.</small>
                                        </div>
                                    </div>
                                    <div class="row d-flex justify-content-center">
                                        <input type="hidden" name="idcomment" id="idcomment" value="<?php echo $_GET['idcomment']; ?>">
                                        <div class="form-group center col-lg-12">
                                            <div class="btn-group col-lg-12">
                                                <button class="btn btn-primary col-lg-6" type="submit"><span class="fas fa-edit"></span> Modifier le commentaire</button><button class="btn btn-danger col-lg-6" type="reset"><span class="fas fa-undo"></span> Réinitialiser le formulaire</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </body>
                    <?php
                    include('includes/scripts.html');
                    ?>
                    <script src="scripts/commentVerif.js"></script>
                    <?php
                    $_SESSION['erroredComment'] = NULL;
                }
                else {
                    showError("unauthorizedAction", $redirectPage);
                }
            } else {
                showError("sessionError");
            }
        } else {
            showError("unknownComment");
        }
    } else {
        showError("wtf");
    }
}
else {
    showError("unlogged");
}
?>
</html>