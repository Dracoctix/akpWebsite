<?php
session_start();
include('includes/database.php');
include('includes/errors.php');
?>
    <!doctype html>
    <html>
    <head>
        <meta charset="utf-8">
        <title><?php if(isset($_GET['idpart']) && $_GET['idpart']) { echo "Modifier une participation"; } else { echo "Participer"; } ?></title>
        <link rel="icon" href="favicon.ico">
        <link rel="stylesheet" href="style/index.css">
        <meta name="robots" content="noindex,nofollow">
        <?php include('includes/bootstrapcss.html'); ?>
    </head>
<?php
$authorized = false;
$adminMode = false;
$idop = $updateMode = $exemplaires = $exemplairesChef = $iduser =  NULL;
if(isset($_SESSION['iduser']) && $_SESSION['iduser'] && isset($_SESSION['rank'])) {
    if(isset($_GET['idop']) && $_GET['idop']) {
        $retour = "viewop.php?idop=".$_GET['idop'];
        $verifOp = $bdd->prepare('SELECT * FROM op WHERE idop = :idop');
        $verifOp->execute(array("idop" => $_GET['idop']));
        if($opInfos = $verifOp->fetch()) {
            $verifUser = $bdd->prepare('SELECT * FROM users WHERE iduser = :iduser AND active = 1 AND banned = 0');
            $verifUser->execute(array("iduser" => $_SESSION['iduser']));
            if($infosUser = $verifUser->fetch()) {
                $authorized = false;
                if($infosUser['rank']) {
                    $adminMode = true;
                    $iduser = (isset($_SESSION['idUserErreur'])) ? $_SESSION['idUserErreur'] : $_SESSION['iduser'];
                    $authorized = true;
                }
                else {
                    $testPasParticipe = $bdd->prepare('SELECT COUNT(iduser) AS nbPartAuto FROM users WHERE iduser = :iduser AND banned = 0 AND active = 1 AND iduser NOT IN(
                      SELECT iduser FROM participation WHERE idop = :idop
                    )');
                    $testPasParticipe->execute(array(
                        "iduser" => $_SESSION['iduser'],
                        "idop"   => $_GET['idop']
                    ));
                    if($testPasParticipe->fetch()['nbPartAuto'] >= 1) {
                        $authorized = true;
                    }
                }
                if ($authorized) {
                    $exemplaires = (isset($_SESSION['exemplairesErreur'])) ? $_SESSION['exemplairesErreur'] : NULL;
                    $exemplairesChef = (isset($_SESSION['exemplairesChefErreur'])) ? $_SESSION['exemplairesChefErreur'] : NULL;
                    $idop = $_GET['idop'];
                    $updateMode = false;
                }
                else {
                    if($infosUser['rank'] != $_SESSION['rank']) {
                        showError("sessionError");
                    }
                    showError("unauthorizedAction");
                }
            }
            else {
                showError("sessionError");
            }
        }
        else {
            showError("unknownOp");
        }
    }
    elseif(isset($_GET['idpart']) && $_GET['idpart']) {
        $recupPart = $bdd->prepare('SELECT * FROM participation WHERE idpart = :idpart');
        $recupPart->execute(array("idpart" => $_GET['idpart']));
        if($partInfo = $recupPart->fetch()) {
            $recupUser = $bdd->prepare('SELECT * FROM users WHERE iduser = :iduser AND active = 1 AND banned = 0');
            $recupUser->execute(array("iduser" => $_SESSION['iduser']));
            if($infosUser = $recupUser->fetch()) {
                if($_SESSION['iduser'] == $partInfo['iduser'] || $infosUser['rank']) {
                    if($infosUser['rank']) {
                        $adminMode = true;
                    }
                    $authorized = true;
                    $exemplaires = (isset($_SESSION['exemplairesErreur'])) ? $_SESSION['exemplairesErreur'] : $partInfo['exemplaires'];
                    $exemplairesChef = (isset($_SESSION['exemplairesErreur'])) ? $_SESSION['exemplairesErreur'] : $partInfo['exemplaireschef'];
                    $idop = $partInfo['idop'];
                    $updateMode = true;
                    $reqOp = $bdd->prepare('SELECT * FROM op WHERE idop = :idop');
                    $reqOp->execute(array("idop" => $idop));
                    $opInfos = $reqOp->fetch();
                }
                else {
                    if($infosUser['rank'] != $_SESSION['rank']) {
                        showError("unauthorizedAction");
                    }
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
        showError("noIdForUser");
    }
} else {
    showError("unlogged");
}

if($authorized) {
    ?>
    <body>
    <div class="container">
        <div class="row">
            <header class="col-lg-12 center page-header">
                <h1><?php if($updateMode) {echo "Modifier une participation"; } else {echo "Participer à l'opération \"".$opInfos['titre']."\""; } ?></h1>
            </header>
        </div>
        <div class="row">
            <?php include('navbar.php');?>
        </div>
        <div class="row mt-4 justify-content-center">
            <form method="post" action="exuppart.php" class="col-lg-12" novalidate>
                <div class="row form-group">
                    <div class="col-md-6">
                        <label for="exemplaires">Stock total de <a href="https://www.urban-rivals.com/characters/?id_perso=<?php echo $opInfos['idcarte'];?>" ><?php echo $opInfos['carte']; ?></a> :</label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="exemplaires" id="exemplaires" value="<?php echo $exemplaires; ?>" placeholder="Nombre d'exemplaires" autofocus min="0">
                            <div class="input-group-append">
                                <span class="input-group-text">cartes</span>
                            </div>
                            <div class="invalid-feedback">Vous devez remplir ce champ.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="exemplaireschef" title="Indiquez ici le nombre d'exemplaires que vous avez déjà mis chez le chef, s'il y en a un.">Nombre d'exemplaires chez le chef de l'opération : </label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="exemplaireschef" id="exemplaireschef" value="<?php echo $exemplairesChef; ?>" placeholder="Exemplaires chez le chef" min="0" <?php echo ($updateMode) ? 'max="'.$opInfos['$exemplaires'].'"' : '';?>>
                            <div class="input-group-append">
                                <span class="input-group-text">cartes</span>
                            </div>
                            <small class="droite text-muted form-text col-lg-12" id="exemplaireschefText">Le nombre de cartes chez le chef doit être inférieur au total</small>
                        </div>
                    </div>
                </div>
                <?php if($adminMode && !$updateMode) {
                    $reqNonParticipants = $bdd->prepare('SELECT iduser, username FROM users WHERE active = 1 AND banned = 0 AND iduser NOT IN (SELECT iduser FROM participation WHERE idop = :idop)');
                    $reqNonParticipants->execute(array("idop" => $_GET['idop']));
                    $reqNbNonParticipants = $bdd->prepare('SELECT COUNT(*) nombre FROM users WHERE active = 1 AND banned = 0 AND iduser NOT IN (SELECT iduser FROM participation WHERE idop = :idop)');
                    $reqNbNonParticipants->execute(array("idop" => $_GET['idop']));

                    $nbNonParticipants = $reqNbNonParticipants->fetch()['nombre'];
                    ?>
                    <div class="row">
                        <div class="alert alert-warning col-lg-12">
                            <div class="row">
                                <h3>Participant (option admin)</h3>
                            </div>
                            <div class="row">
                                <label for="iduser" class="label-coche col-lg-12">Choisissez l'utilisateur à qui vous souhaitez attribuer la participation, si ce n'est pas vous. Notez qu'il s'agit d'une option réservée aux administrateurs.</label>
                            </div>
                            <select name="iduser" id="iduser" class="form-control" <?php echo ($nbNonParticipants <= 1) ? 'readonly disabled title="Un seul utilisateur peut participer."' : ''; ?> >
                                <?php
                                $idVise = (isset($_SESSION['idUserErreur'])) ? $_SESSION['idUserErreur'] : $_SESSION['iduser'];
                                while ($infosOldUser = $reqNonParticipants->fetch()) {
                                    ?>
                                    <option value="<?php echo $infosOldUser['iduser'];?>"<?php
                                    if($infosOldUser['iduser'] == $idVise) {
                                        echo "selected";
                                    }
                                    ?>><?php echo $infosOldUser['iduser']; ?> : <?php echo $infosOldUser['username']; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                <?php } ?>
                <?php if($updateMode) {
                    ?>
                    <input type="hidden" name="idpart" id="idpart" value="<?php echo $_GET['idpart']; ?>">
                    <?php
                }
                else {
                    ?>
                    <input type="hidden" name="idop" id="idop" value="<?php echo $_GET['idop']; ?>">
                    <?php
                }
                ?>
                <div class="row form-group justify-content-center d-flex">
                    <div class="col-lg-6 col-md-12">
                        <div class="row justify-content-center d-flex">
                            <div class="btn-group col-lg-12">
                                <button type="submit" class="btn btn-success col-lg-6 col-md-12"><span class="fas fa-<?php echo ($updateMode) ? 'edit' : 'plus'; ?>"></span> <?php if($updateMode) {echo "Modifier";} else {echo "Participer";}?></button>
                                <button type="reset" class="btn btn-danger col-lg-6 d-none d-lg-block"><span class="fas fa-undo"></span> Réinitialiser le formulaire</button>
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
    <script src="scripts/partVerif.js"></script>
    <?php
}

$_SESSION['exemplairesErreur'] = $_SESSION['exemplairesChefErreur'] = $_SESSION['idUserErreur'] = NULL;
?>