<?php
// Page permettant l'ajout ou la modification d'un lot personnalisé.
session_start();
include('includes/errors.php');
include('includes/database.php');
?>
    <!doctype html>
    <html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="style/index.css">
        <meta name="robots" content="noindex,nofollow">
<?php
// On vérifie d'abord si l'utilisateur est connecté.
if(isset($_SESSION['iduser']) && isset($_SESSION['username'])) {
    // On vérifie si l'utilisateur est bien autorisé à être connecté.
    $reqUser = $bdd->prepare('SELECT iduser FROM users WHERE iduser = :iduser');
    $reqUser->execute(array("iduser" => $_SESSION['iduser']));

    if($reqUser->fetch()) {
        if(isset($_GET['idlot'])) {
            // On vérifie si le lot existe.
            $reqLot = $bdd->prepare('SELECT * FROM lots WHERE idlot = :idlot AND iduser = :iduser');
            $reqLot->execute(array(
                "idlot" => $_GET['idlot'],
                "iduser" => $_SESSION['iduser']
            ));

            if($infosLot = $reqLot->fetch()) {
                $reqDelete = $bdd->prepare('DELETE FROM lots WHERE idlot = :idlot');
                $reqDelete->execute(array("idlot" => $_GET['idlot']));
                ?>
                <script>
                    alert("Le lot a bien été supprimé.");
                    <?php if($infosLot['fini']) { ?>
                    document.location.href="viewoldlot.php?iduser=<?php echo $_SESSION['iduser']?>";
                    <?php } else { ?>
                    document.location.href="viewuser.php?iduser=<?php echo $_SESSION['iduser']?>";
                    <?php } ?>
                </script>
                <?php
            } else {
                showError("unknownOp", "viewuser.php?iduser=".$_SESSION['iduser']);
            }
        } else {
        showError("wtf");
        }
    }
    else {
        showError("sessionError");
    }
} else {
    showError("unlogged");
}