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
</head>
<?php
if(isset($_SESSION['iduser']) && $_SESSION['iduser'] && isset($_SESSION['rank'])) {
    if(isset($_POST['idcomment']) && $_POST['idcomment'] && isset($_POST['commentContent'])) {
        $reqComment = $bdd->prepare('SELECT * FROM comments WHERE idcomment = :idcomment');
        $reqComment->execute(array("idcomment" => $_POST['idcomment']));
        if($infosComment = $reqComment->fetch()) {
            $reqUser = $bdd->prepare('SELECT * FROM users WHERE iduser = :iduser AND active = 1 AND banned = 0');
            $reqUser->execute(array("iduser" => $_SESSION['iduser']));
            if($infosUser = $reqUser->fetch()) {
                if($infosComment['iduser'] == $_SESSION['iduser'] || $infosUser['rank']) {
                    $redirectPage = 'editcomment.php?idcomment='.$infosComment['idcomment'];
                    $_SESSION['erroredComment'] = $_POST['commentContent'];
                    if(strlen($_POST['commentContent']) > 3) {
                        $commentaire = htmlspecialchars($_POST['commentContent']);
                        $editComment = $bdd->prepare('UPDATE comments SET text = :text WHERE idcomment = :idcomment');
                        $editComment->execute(array(
                            "text" => $commentaire,
                            "idcomment" => $_POST['idcomment']
                        ));
                        $_SESSION['erroredComment'] = NULL;
                        ?>
                        <script>
                            alert("Le commentaire a bien été édité.");
                            document.location.href="viewop.php?idop=<?php echo $infosComment['idop']; ?>";
                        </script>
                        <?php
                    } else {
                        ?>
                        <script>
                            alert("Le commentaire doit faire trois caractères au minimum.");
                            document.location.href="<?php echo $redirectPage; ?>";
                        </script>
                        <?php
                    }
                }
                else {
                    showError("unauthorizedAction", "viewop.php?idop=".$infosComment['idop']);
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