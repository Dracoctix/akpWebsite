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
        <link rel="stylesheet" href="../style/adm.css">
        <link rel="stylesheet" href="../style/index.css">
        <link rel="icon" href="../favicon.ico">
        <?php
        include('../includes/bootstrapcss.html');
        ?>
    </head>
    <body>
    <div class="container">
    <?php
    if(isset($_SESSION['iduser'])) { // Vérifions que l'utilisateur est bien loggé
        $verifAdmin = $bdd->prepare('SELECT * FROM users WHERE iduser = :iduser');
        $verifAdmin->execute(array("iduser" => $_SESSION['iduser']));
        if($infosAdmin = $verifAdmin->fetch()) { // Ici, on s'assure que l'utilisateur existe bien, au cas-où
            if ($infosAdmin['rank']) { // On vérifie que l'utilisateur soit toujours admin
                $modif = false;
                $opInfos = NULL;
                $titre = $carte = $idcarte = $objectif = $monteePrix = $prix = $statut = $idcreateur = $createurPseudo =
                $remplacement = $description = NULL;
                if (isset($_GET['idop']) && $_GET['idop']) {
                    $recupOp = $bdd->prepare('SELECT * FROM op WHERE idop = :idop');
                    $recupOp->execute(array("idop" => $_GET['idop']));
                    if ($opInfos = $recupOp->fetch()) {
                        $modif = true;
                    } else {
                        showError("unknownOp", "op.php");
                    }
                }
                ?>
                <div class="row">
                    <header class="col-lg-12 center">
                        <?php
                        if($modif) {
                            echo '<h1 class="page-header">Modifier l\'opération "'.$opInfos['titre'].'"</h1>';
                            $idcreateur = $opInfos['idcreateur'];
                            $createurPseudo = "Créateur Inconnu";
                        }
                        else {
                            echo '<h1 class="page-header">Ajouter une nouvelle opération</h1>';
                        }

                        ?>
                    </header>
                </div>
                <?php
                if(isset($_SESSION['titreErreur']) && isset($_SESSION['carteErreur']) && isset($_SESSION['idcarteErreur'])
                    && isset($_SESSION['objectifErreur']) && isset($_SESSION['phaseErreur']) && isset($_SESSION['prixErreur']) && isset($_SESSION['descErreur'])
                    && isset($_SESSION['statutErreur'])) {
                    $titre = $_SESSION['titreErreur'];
                    $carte = $_SESSION['carteErreur'];
                    $idcarte = $_SESSION['idcarteErreur'];
                    $objectif = $_SESSION['objectifErreur'];
                    $monteePrix = $_SESSION['phaseErreur'];
                    $prix = $_SESSION['prixErreur'];
                    $statut = $_SESSION['statutErreur'];
                    $remplacement = $_SESSION['remplacement'];
                }
                elseif ($modif) {
                    $titre = $opInfos['titre'];
                    $carte = $opInfos['carte'];
                    $idcarte = $opInfos['idcarte'];
                    $objectif = $opInfos['objectif'];
                    $monteePrix = $opInfos['phase'];
                    $prix = $opInfos['prix'];
                    $statut = $opInfos['status'];
                    $description = $opInfos['description'];
                }
                $page = ($modif) ? 'editop' : 'addop';
                ?>
                <div class="row">
                    <?php
                    include 'menu.php';
                    ?>
                </div>
                <div class="row my-4">
                    <div class="col-lg-12">
                        <form action="exeditop.php" method="post" novalidate>
                            <div class="form-group">
                                <label for="titre">Titre :</label>
                                <div class="input-group">
                                    <input type="text" maxlength="255" name="titre" id="titre" placeholder="Titre" value="<?php echo $titre; ?>" class="form-control" autofocus>
                                    <small class="form-text text-muted droite col-lg-12" id="titreTexte">Vous devez spécifier un titre.</small>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="carte">Carte :</label>
                                    <div class="input-group">
                                        <input type="text" maxlength="255" name="carte" id="carte" placeholder="Carte" value="<?php echo $carte; ?>" class="form-control">
                                        <small class="form-text text-muted col-lg-12 droite" id="carteTexte">Vous devez spécifier le nom de la carte ciblée.</small>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="idcarte" class="d-flex justify-content-between">Identifiant de la carte :<span data-toggle="tooltip" data-placement="top" data-html="true" title="L'identifiant apparaît dans l'URL de la page du personnage." class="fas fa-info-circle droite"></span></label>
                                    <div class="input-group">
                                        <input min="0" type="number" name="idcarte" id="idcarte" placeholder="Identifiant" value="<?php echo $idcarte; ?>" class="form-control" max="9999">
                                        <small class="form-text text-muted col-lg-12 droite" id="idTexte">Vous devez spécifier un identifiant valide pour la carte ciblée.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-lg-6">
                                    <label for="objectif" title="Nombre d'exemplaires visé (si inconnu, mettre 0).">Objectif :</label>
                                    <div class="input-group">
                                        <input type="number" min="0" maxlength="255" name="objectif" id="objectif" placeholder="Objectif" value="<?php echo $objectif;?>" class="form-control">
                                        <div class="input-group-append">
                                            <span class="input-group-text">exemplaires</span>
                                        </div>
                                        <small class="form-text text-muted col-lg-12 droite">L'objectif est facultatif.</small>
                                    </div>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="prix">Prix :</label>
                                    <div class="input-group">
                                        <input type="number" min="50" maxlength="9" placeholder="Prix" name="prix" id="prix" value="<?php echo $prix;?>" class="form-control">
                                        <div class="input-group-append">
                                            <span class="input-group-text">clintz/tête</span>
                                        </div>
                                        <small class="form-text text-muted col-lg-12 droite" id="prixTexte">Vous devez spécifier un prix d'achat.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="description">Description de l'opération :</label>
                                <div class="input-group">
                                    <textarea placeholder="Description..." name="description" id="description" rows = "13" cols="100" class="form-control"><?php echo $description; ?></textarea>
                                    <small class="form-text text-muted col-lg-12 droite">La description est facultative.</small>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <div class="row">
                                        <span class="radioLabel col-lg-12">Phase :</span>
                                    </div>
                                    <div class="row justify-content-center d-flex">
                                        <div class="col-lg-12">
                                            <div class="row justify-content-center d-flex">
                                                <div class="btn-group-toggle btn-group col-lg-12" data-toggle="buttons">
                                                    <label class="col-md-6 col-sm-12 btn btn-primary form-check-label <?php if(!$monteePrix){echo "active";}?>">
                                                        <input name="phase" id="0" value="0" class="form-check-input" type="radio" autocomplete="off" <?php if(!$monteePrix){echo "checked";}?>>Stockage
                                                    </label>
                                                    <label class="col-md-6 col-sm-12 btn btn-primary form-check-label <?php if($monteePrix){echo "active";} ?>">
                                                        <input name="phase" id="1" value="1" class="form-check-input" type="radio" autocomplete="off" <?php if($monteePrix){echo "checked";} ?>>Montée des prix
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <div class="row">
                                        <span class="radioLabel col-lg-12">Statut de l'opération :</span>
                                    </div>
                                    <div class="row justify-content-center d-flex">
                                        <div class="col-lg-12">
                                            <div class="row justify-content-center d-flex">
                                                <div class="btn-group-toggle btn-group col-lg-12" data-toggle="buttons">
                                                    <label class="col-md-4 col-sm-12 btn btn-primary form-check-label <?php if(!$modif || !$statut){echo 'active';} ?>">
                                                        <input name="statut" id="0" value="0" class="form-check-input" type="radio" autocomplete="off" <?php if(!$modif || !$statut){echo 'checked';} ?>>En préparation
                                                    </label>
                                                    <label class="col-md-4 col-sm-12 btn btn-primary form-check-label <?php if($statut == 1) {echo 'active';}?>">
                                                        <input name="statut" id="1" value="1" class="form-check-input" type="radio" autocomplete="off" <?php if($statut == 1) {echo 'checked';}?>>En cours
                                                    </label>
                                                    <label class="col-md-4 col-sm-12 btn btn-primary form-check-label <?php if ($statut == 2) {echo 'active';}?>">
                                                        <input name="statut" id="2" value="2" class="form-check-input" type="radio" autocomplete="off" <?php if ($statut == 2) {echo 'checked';}?>>Terminée
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            if($modif && $idcreateur != $_SESSION['iduser']) {
                                if($idcreateur) {
                                    $verifCreateur = $bdd->prepare('SELECT * FROM users WHERE iduser = :iduser');
                                    $verifCreateur->execute(array("iduser" => $idcreateur));
                                    $infosCreateur = $verifCreateur->fetch();
                                    $createurPseudo = $infosCreateur['username'];
                                }
                                $checkRemplacement = ($remplacement) ? "checked" : NULL;
                                echo '<div class="form-check mb-2"><input type="checkbox" name="newCreateur" id="newCreateur" value="newCreateur" class="form-check-input" '.$remplacement.'>
                                      <label for="newCreateur" class="form-check-label">Devenir créateur (remplacez <strong>'.
                                    $createurPseudo.'</strong>)</label></div>';
                            }
                            if($modif) {
                                ?>
                                <input type="hidden" name="idop" id="idop" value="<?php echo $_GET['idop'];?>">
                                <?php
                            }
                            ?>
                            <div class="row form-group d-flex justify-content-center">
                                <div class="col-lg-6 col-md-12">
                                    <div class="row justify-content-center d-flex">
                                        <div class="btn-group col-lg-12">
                                            <button type="submit" class="col-lg-6 col-md-12 btn btn-success"><span class="fas fa-<?php echo ($modif) ? 'edit' : 'plus'; ?>"></span> <?php if($modif){echo "Modifier";} else{echo "Ajouter";}?></button>
                                            <button type="reset" class="col-lg-6 d-none d-lg-block btn btn-danger"><span class="fas fa-undo"></span> Réinitialiser le formulaire</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <?php
                include('../includes/scripts.html');
                ?>
                <script src="../scripts/verifOp.js"></script>
                <script src="../scripts/tooltips.js"></script>
                <?php
                $_SESSION['titreErreur'] = $_SESSION['statutErreur'] = $_SESSION['carteErreur'] = $_SESSION['idcarteErreur']
                = $_SESSION['remplacement'] = $_SESSION['phaseErreur'] = $_SESSION['objectifErreur']= $_SESSION['descErreur'] = NULL;
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
    ?></div>
        </div></body></html>