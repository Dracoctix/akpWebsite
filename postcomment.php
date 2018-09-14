<?php
session_start();
include('includes/database.php');
include('includes/errors.php');
?>
    <!doctype html>
    <html>
    <head>
        <meta charset="utf-8">
        <meta name="robots" content="noindex,nofollow">
        <title>Publication d'un commentaire</title>
<?php
if(isset($_SESSION['iduser']) && $_SESSION['iduser']) {
    if(isset($_POST['idop']) && $_POST['idop'] && isset($_POST['commentContent'])) {
        $testOp = $bdd->prepare('SELECT * FROM op WHERE idop = :idop');
        $testOp->execute(array("idop" => $_POST['idop']));
        if($infosOp = $testOp->fetch()) {
            $testUser = $bdd->prepare('SELECT * FROM users WHERE iduser = :iduser');
            $testUser->execute(array("iduser" => $_SESSION['iduser']));
            if($infosUser = $testUser->fetch()) {
                if($infosUser['active'] && !$infosUser['banned']) {
                    $redirectUrl = 'viewop.php?idop=' . $_POST['idop'];
                    if ($infosOp['status'] == 1 || $infosUser['rank']) {
                        $_SESSION['erroredComment'] = $_POST['commentContent'];
                        if (strlen($_POST['commentContent']) > 3) {
                            $commentaire = htmlspecialchars($_POST['commentContent']);
                            $insertionCommentaire = $bdd->prepare('INSERT INTO comments (iduser, idop, text)
                              VALUES (:iduser, :idop, :text)');
                            $insertionCommentaire->execute(array(
                                    "iduser" => $_SESSION['iduser'],
                                    "idop" => $_POST['idop'],
                                    "text" => $commentaire
                            ));
                            $nombreCommentaires = $bdd->prepare('SELECT COUNT(*) AS nombre FROM comments WHERE idop = :idop');
                            $nombreCommentaires->execute(array("idop" => $_POST['idop']));
                            $nombreTotal = $nombreCommentaires->fetch()['nombre'];
                            $nombrePages = ceil($nombreTotal/15);
                            $_SESSION['erroredComment'] = NULL;
                            ?>
                            <script>
                                alert("Votre commentaire a bien été posté sur l'opération \"<?php echo $infosOp['titre']; ?>\".");
                                document.location.href="viewop.php?idop=<?php echo $_POST['idop']; ?>&page=<?php echo $nombrePages;?>#comment<?php echo $nombreTotal;?>";
                            </script>
                            <?php
                        } else {
                            ?>
                            <script>
                                alert("Votre commentaire doit comporter plus de 3 caractères.");
                                document.location.href = "<?php echo $redirectUrl; ?>";
                            </script>
                        <?php
                        }
                    }
                    else {
                    ?>
                        <script>
                            alert("L'opération que vous essayez de commenter ne peut plus être commentée.");
                            document.location.href = "<?php echo $redirectUrl; ?>";
                        </script>
                        <?php
                    }
                }
                else {
                    showError("sessionError");
                }
            }
            else {
                showError("sessionError");
            }
        }
        else {
            showError("unknownOp");
        }
    } else {
        showError("wtf");
    }
}
else {
    showError("unlogged");
}