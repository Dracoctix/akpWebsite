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
        include('includes/bootstrapcss.html');
    ?>
<?php
// On vérifie d'abord si l'utilisateur est connecté.
if(isset($_SESSION['iduser']) && isset($_SESSION['username'])) {
    // On déclare les variables de vérification.
    $updateMode = false;
    $authorized = false;
    $cardName = $cardId = $exemplaires = $objectif = $fini = $visible = NULL;
    // On effectue d'abord les vérifications, pour afficher (ou non) le formulaire, si l'utilisateur est autorisé.
    if(!isset($_GET['idlot']) || !$_GET['idlot']) {
        // Dans ce cas, on part du principe que l'utilisateur cherche à créer un lot. On n'a donc pas besoin de vérification annexe.
        $authorized = true;
        $cardName = (isset($_SESSION['erroredCardName'])) ? $_SESSION['erroredCardName'] : NULL;
        $cardId = (isset($_SESSION['erroredCardId'])) ? $_SESSION['erroredCardId'] : NULL;
        $exemplaires = (isset($_SESSION['erroredExemplaires'])) ? $_SESSION['erroredExemplaires'] : NULL;
        $objectif = (isset($_SESSION['erroredObjectif'])) ? $_SESSION['erroredObjectif'] : NULL;
        $fini = (isset($_SESSION['erroredFini'])) ? $_SESSION['erroredFini'] : false;
        $visible = (isset($_SESSION['erroredVisible'])) ? $_SESSION['erroredVisible'] : true;
    } else {
        // On vérifie si le lot demandé existe.
        $reqLot = $bdd->prepare('SELECT * FROM lots WHERE idlot = :idlot AND iduser = :iduser');
        $reqLot->execute(array(
            "idlot" => $_GET['idlot'],
            "iduser" => $_SESSION['iduser']
        ));
        if($infosLot = $reqLot->fetch()) {
            $authorized = true;
            $updateMode = true;
            // On met les valeurs au formulaire pour éviter de tout taper à nouveau.
            $cardName = (isset($_SESSION['erroredCardName'])) ? $_SESSION['erroredCardName'] : $infosLot['carte'];
            $cardId = (isset($_SESSION['erroredCardId'])) ? $_SESSION['erroredCardId'] : $infosLot['idcarte'];
            $exemplaires = (isset($_SESSION['erroredExemplaires'])) ? $_SESSION['erroredExemplaires'] : $infosLot['exemplaires'];
            $objectif = (isset($_SESSION['erroredObjectif'])) ? $_SESSION['erroredObjectif'] : $infosLot['objectif'];
            $fini = (isset($_SESSION['erroredFini'])) ? $_SESSION['erroredFini'] : (($infosLot['fini']) ? 1 : 0);
            $visible = (isset($_SESSION['erroredVisible'])) ? $_SESSION['erroredVisible'] : (($infosLot['visible']) ? 1 : 0);
        } else {
            showError("unknownLot", "viewuser.php?iduser=".$_SESSION['iduser']);
        }
    }

    // Maintenant, on affiche le formulaire si l'utilisateur est autorisé.
    if($authorized) {
        $notIndex = true;
        ?>
            <title><?php echo ($updateMode) ? "Mise à jour d'un lot" : "Ajout d'un lot"; ?></title>
        </head>
        <body>
        <div class="container">
            <div class="row d-flex justify-content-center">
               <header class="page-header col-lg-12 center">
                   <h1><?php
                       if($updateMode) {
                           ?>Modifier un lot<?php
                       } else {
                           ?>Ajouter un nouveau lot<?php
                       }
                       ?></h1>
               </header>
            </div>
            <div class="row">
                <?php include 'navbar.php';?>
            </div>
            <div class="row mt-4 justify-content-center">
                <form method="post" class="col-lg-12" action="exeditlot.php">
                    <div class="row form-group">
                        <div class="col-md-6">
                            <label for="cardName">Nom de la carte stockée :</label>
                            <div class="input-group">
                                <input class="form-control" type="text" name="cardName" id="cardName" placeholder="Nom de la carte" value="<?php echo $cardName; ?>" maxlength="255" autofocus>
                                <div class="invalid-feedback droite">Vous devez indiquer un nom de carte de moins de 255 caractères.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="cardId" title="Apparaît dans l'URL du perso">Identifiant de la carte :</label>
                            <div class="input-group">
                                <input min="0" type="number" name="cardId" id="cardId" placeholder="ID" max="9999" value="<?php echo $cardId;?>" class="form-control">
                                <div class="invalid-feedback droite">Vous devez indiquer un identifiant de 4 chiffres maximum.</div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-6">
                            <label for="exemplaires">Stock :</label>
                            <div class="input-group">
                                <input min="0" type="number" name="exemplaires" id="exemplaires" placeholder="Nombre d'exemplaires" value="<?php echo $exemplaires; ?>" class="form-control">
                                <div class="input-group-append">
                                    <span class="input-group-text">cartes</span>
                                </div>
                                <div class="invalid-feedback">Vous devez indiquer un nombre allant jusqu'à 7 chiffres.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="objectif">Objectif :</label>
                            <div class="input-group">
                                <input min="0" type="number" name="objectif" id="objectif" placeholder="Objectif" value="<?php echo $objectif;?>" class="form-control">
                                <div class="input-group-append">
                                    <span class="input-group-text">cartes</span>
                                </div>
                                <div class="invalid-feedback droite">Vous devez indiquer un nombre allant jusqu'à 7 chiffres.</div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-6 checkbox">
                            <input type="checkbox" name="fini" id="fini" value="true" <?php if($fini){ echo 'checked'; } ?>><label for="fini" class="label-coche">Ce lot est un ancien lot.</label>
                        </div>
                        <div class="col-md-6 checkbox">
                            <input type="checkbox" name="visible" id="visible" value="true" <?php if($visible) { echo 'checked'; } ?>><label for="visible" class="label-coche">Ce lot peut être vu par les autres membres</label><br>
                        </div>
                    </div>
                    <?php if($updateMode) { ?>
                    <input type="hidden" name="idlot" id="idlot" value="<?php echo $_GET['idlot']; ?>">
                    <?php } ?>
                    <div class="row form-group justify-content-center d-flex">
                        <div class="col-lg-6 col-md-12">
                            <div class="row justify-content-center d-flex">
                                <div class="btn-group col-lg-12">
                                    <button type="submit" class="col-lg-6 col-md-12 btn btn-success"><span class="fas fa-<?php echo ($updateMode) ? 'edit' : 'plus' ?>"></span> <?php
                                        if($updateMode) {
                                            echo 'Modifier le lot';
                                        } else {
                                            echo 'Créer le lot';
                                        }
                                        ?></button><button type="reset" class="col-lg-6 btn btn-danger d-none d-lg-block"><span class="fas fa-undo"></span> Réinitialiser</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php
        include('includes/scripts.html');
        ?>
        <script src="scripts/verifLot.js"></script>
        </body>
        <?php
        $_SESSION['erroredExemplaires'] = $_SESSION['erroredCardName'] = $_SESSION['erroredCardId'] = $_SESSION['erroredFini'] = $_SESSION['erroredObjectif'] = $_SESSION['erroredVisible'] = NULL;
    }
}
else {
    showError("unlogged");
}
?>
