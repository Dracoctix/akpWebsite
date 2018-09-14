<?php
// Page permettant le traîtement de l'ajout ou la modification d'un lot personnalisé.
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
    // On vérifie ensuite que l'utilisateur soit bien passé par le formulaire d'ajout.
    if(isset($_POST['exemplaires']) && isset($_POST['objectif']) && isset($_POST['cardName']) && isset($_POST['cardId'])) {
        // Pour éviter tout bug, on vérifie si l'utilisateur existe encore dans la BDD, et s'il a le droit d'être connecté.
        $reqVerifUser = $bdd->prepare('SELECT iduser FROM users WHERE iduser = :iduser AND active = 1 AND banned = 0');
        $reqVerifUser->execute(array("iduser" => $_SESSION['iduser']));
        if($infosUser = $reqVerifUser->fetch()) {
            // Cette variable permet de savoir si on met à jour. On lui met true si le bloc suivant passe les tests.
            $updateMode = false;
            // Variable stockant l'adresse à laquelle renvoyer l'utilisateur en cas d'erreur (doit être modifiée dans le bloc suivant, en cas d'édit)
            $returnUrl = "editlot.php";
            // Ce bloc se déclenche si on met à jour.
            if(isset($_POST['idlot'])) {
                // On vérifie si le lot existe.
                $reqExistingLot = $bdd->prepare('SELECT idlot, fini FROM lots WHERE idlot = :idlot AND iduser = :iduser');
                $reqExistingLot->execute(array(
                   "idlot" => $_POST['idlot'],
                   "iduser" => $_SESSION['iduser']
                ));

                if($infosLot = $reqExistingLot->fetch()) {
                    $updateMode = true;
                    $returnUrl = "editlot.php?idlot=".$_POST['idlot'];
                } else {
                    showError("unknownLot");
                }
            }

            // On met les informations rentrées par l'utilisateur en session, pour pouvoir lui éviter de tout retaper en cas d'erreur.
            $_SESSION['erroredCardName'] = $_POST['cardName'];
            $_SESSION['erroredCardId'] = $_POST['cardId'];
            $_SESSION['erroredExemplaires'] = $_POST['exemplaires'];
            $_SESSION['erroredObjectif'] = $_POST['objectif'];
            $_SESSION['erroredFini'] = (isset($_POST['fini'])) ? true : false;
            $_SESSION['erroredVisible'] = (isset($_POST['visible'])) ? true : false;

            // On vérifie si l'utilisateur a bien renseigné tous les champs.
            if($_POST['exemplaires'] && $_POST['objectif'] && $_POST['cardName'] && $_POST['cardId']) {
                $cardName = htmlspecialchars($_POST['cardName']);
                // On vérifie que l'utilisateur a bien rentré un nom de carte suffisamment long.
                if(strlen($cardName) <= 255) {
                    // On vérifie que l'utilisateur a bien mis un nombre pour l'identifiant de la carte.
                    if(filter_var($_POST['cardId'], FILTER_VALIDATE_INT)) {
                        if($_POST['cardId'] <= 9999 && $_POST['cardId'] >= 0) {
                            if(filter_var($_POST['exemplaires'], FILTER_VALIDATE_INT)) {
                                if($_POST['exemplaires'] < 9999999 && $_POST['exemplaires'] >= 0) {
                                    if(filter_var($_POST['objectif'], FILTER_VALIDATE_INT)) {
                                        if($_POST['objectif'] < 9999999 && $_POST['objectif'] >= 0) {
                                            // Les vérifications étant faites, on peut entamer les modifications.
                                            $fini = (isset($_POST['fini'])) ? 1 : 0;
                                            $visible = (isset($_POST['visible'])) ? 1 : 0;
                                            // On vide les variables déclarées précédemment en session, pour éviter des bugs d'affichage.
                                            $_SESSION['erroredVisible'] = $_SESSION['erroredObjectif'] = $_SESSION['erroredFini'] = $_SESSION['erroredCardId'] = $_SESSION['erroredCardName'] = $_SESSION['erroredExemplaires'] = NULL;
                                            // On vérifie si on est en mise à jour pour modifier l'entrée en conséquence.
                                            if($updateMode) {
                                                $reqUpdate = $bdd->prepare('UPDATE lots SET carte = :carte, idcarte = :idcarte, exemplaires = :exemplaires, objectif = :objectif, visible = :visible, fini = :fini WHERE idlot = :idlot');
                                                $reqUpdate->execute(array(
                                                    "carte" => $cardName,
                                                    "idcarte" => $_POST['cardId'],
                                                    "exemplaires" => $_POST['exemplaires'],
                                                    "objectif" => $_POST['objectif'],
                                                    "visible" => $visible,
                                                    "fini" => $fini,
                                                    "idlot" => $_POST['idlot']
                                                ));
                                                ?>
                                                <script>
                                                    alert("Votre lot a bien été modifié.");
                                                    <?php if(isset($_POST['fini'])) { ?>
                                                    document.location.href="viewoldlot.php?iduser=<?php echo $_SESSION['iduser']; ?>";
                                                    <?php } else { ?>
                                                    document.location.href="viewuser.php?iduser=<?php echo $_SESSION['iduser']; ?>";
                                                    <?php } ?>
                                                </script>
                                                <?php
                                            } else {
                                                // On ajoute le lot, puis on redirige l'utilisateur vers sa page.
                                                $reqAjout = $bdd->prepare('INSERT INTO lots(carte, idcarte, exemplaires, objectif, visible, fini, iduser) VALUES(:carte, :idcarte, :exemplaires, :objectif, :visible, :fini, :iduser)');
                                                $reqAjout->execute(array(
                                                    "carte" => $cardName,
                                                    "idcarte" => $_POST['cardId'],
                                                    "exemplaires" => $_POST['exemplaires'],
                                                    "objectif" => $_POST['objectif'],
                                                    "visible" => $visible,
                                                    "fini" => $fini,
                                                    "iduser" => $_SESSION['iduser']
                                                ));
                                                ?>
                                                <script>
                                                    alert("Votre lot a bien été ajouté.");
                                                    <?php if(isset($_POST['fini'])) { ?>
                                                    document.location.href="viewoldlot.php?iduser=<?php echo $_SESSION['iduser']; ?>";
                                                    <?php } else {
                                                        ?>
                                                    document.location.href="viewuser.php?iduser=<?php echo $_SESSION['iduser']; ?>";
                                                    <?php
                                                    } ?>
                                                </script>
                                                <?php
                                            }
                                        } else {
                                            ?>
                                            <script>
                                                alert("L'objectif doit être positif et inférieur à 9 999 999.");
                                                document.location.href="<?php echo $returnUrl; ?>";
                                            </script>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <script>
                                        alert("L'objectif doit être un nombre.");
                                        document.location.href="<?php echo $returnUrl; ?>";
                                        </script>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <script>
                                        alert("Le nombre d'exemplaires doit être positif et inférieur à 9 999 999.");
                                        document.location.href="<?php echo $returnUrl; ?>";
                                    </script>
                                    <?php
                                }
                            }
                            else {
                                ?>
                                <script>
                                alert("Le nombre d'exemplaires doit être un nombre entier.");
                                document.location.href="<?php echo $returnUrl; ?>";
                                </script>
                                <?php
                            }
                        }   else {
                            ?>
                            <script>
                                alert("L'identifiant de la carte doit être positif et inférieur à 9999.");
                                document.location.href="<?php echo $returnUrl; ?>";
                            </script>
                        <?php
                        }
                    } else {
                        ?>
                        <script>
                            alert("L'identifiant de la carte doit être un nombre entier.");
                            document.location.href="<?php echo $returnUrl; ?>";
                        </script>
                        <?php
                    }
                } else {
                    ?>
                    <script>
                        alert("Le nom de la carte doit comporter moins de 255 caractères.");
                        document.location.href="<?php echo $returnUrl; ?>";
                    </script>
                    <?php
                }
            }
            else {
                showError("saisieVide", $returnUrl);
            }
        }
        else {
            showError("sessionError");
        }
    }
    else {
        showError("wtf");
    }
} else {
    showError("unlogged");
}