<?php
session_start();
include('includes/database.php');
include('includes/errors.php');
?>
<!doctype html>
<html>
<head>
    <title>Suppression d'un commentaire</title>
    <meta charset="utf-8">
    <meta name="robots" content="noindex,nofollow">
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
                    $suppression = $bdd->prepare('DELETE FROM comments WHERE idcomment = :idcomment');
                    $suppression->execute(array("idcomment" => $_GET['idcomment']));
                    ?>
                    <script>
                        alert("Le commentaire a bien été supprimé.");
                        document.location.href="<?php echo $redirectPage; ?>";
                    </script>
                    <?php
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