<?php
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
    if(isset($_SESSION['username']) && isset($_SESSION['iduser']) && isset($_SESSION['email'])) {
        if(isset($_GET['iduser']) && $_GET['iduser']) {
            $reqUser = $bdd->prepare('SELECT *, DATE_FORMAT(subdate,
                \'%d/%m/%Y %H:%i\') AS date FROM users WHERE iduser = :iduser AND active = 1 AND banned = 0');

            $reqUser->execute(array("iduser" => $_GET['iduser']));
            if($userInfos = $reqUser->fetch()) {
                $redirectLink = 'viewuser.php?iduser=' . $_GET['iduser'];
                if($_SESSION['iduser'] == $_GET['iduser']) {
                    $reqNbOldLot = $bdd->prepare('SELECT COUNT(idlot) AS nbOldLot FROM lots WHERE iduser = :iduser AND fini = 1');
                }
                else {
                    $reqNbOldLot = $bdd->prepare('SELECT COUNT(idlot) AS nbOldLot FROM lots WHERE iduser = :iduser AND fini = 1 AND visible = 1');
                }
                $reqNbOldLot->execute(array("iduser" => $_GET['iduser']));
                $nbOldLot = $reqNbOldLot->fetch()['nbOldLot'];

                $reqNbOldPart = $bdd->prepare('SELECT COUNT(idpart) AS nbOldPart FROM participation NATURAL JOIN op WHERE status = 2 AND iduser = :iduser');
                $reqNbOldPart->execute(array("iduser" => $_GET['iduser']));
                $nbOldPart = $reqNbOldPart->fetch()['nbOldPart'];

                if($nbOldLot + $nbOldPart >= 1) {
                    $notIndex = true;
                    $profil = true; ?>
                        <title>Anciens lots de <?php echo $userInfos['username']; ?></title>
                    </head>
                    <body>
                        <div class="container-fluid">
                            <div class="row">
                                <header class="col-lg-12 page-header center">
                                    <h1>Ancienne activité de <?php echo $userInfos['username']; ?></h1>
                                </header>
                            </div>
                            <div class="row">
                                <?php include('navbar.php'); ?>
                            </div>
                            <div class="row">
                                <div class="col-lg-10 col-md-12">
                                    <?php
                                    if($nbOldPart >= 1) {
                                        $reqParts = $bdd->prepare('SELECT *, DATE_FORMAT(creation, \'%d/%m/%Y %H:%i\') creation, DATE_FORMAT(datemaj, \'%d/%m/%Y %H:%i\') dateMaj FROM participation
                                                    NATURAL JOIN op WHERE iduser = :iduser AND status = 2');
                                        $reqParts->execute(array(
                                            "iduser" => $_GET['iduser']
                                        ));
                                    ?>
                                    <div class="row my-4">
                                        <div class="col-lg-12">
                                            <div class="card">
                                                <h3 class="card-header bg-info text-white justify-content-between d-flex align-items-center" id="participations">Anciennes opérations <span class="badge badge-pill badge-secondary"><?php echo $nbOldPart;?></span></h3>
                                                <div class="card-content table-responsive">
                                                    <table class="table table-bordered table-hover table-striped mb-0">
                                                        <thead class="thead-dark">
                                                            <tr>
                                                                <th scope="col">Nom de l'opération</th>
                                                                <th scope="col">Carte</th>
                                                                <th scope="col">Exemplaires</th>
                                                                <th scope="col">Date de participation</th>
                                                                <th scope="col">Date d'édition</th>
                                                                <th scope="col">Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $compteurLots = false;
                                                            while($partInfo = $reqParts->fetch()) {
                                                                $compteurLots = true;
                                                                ?>
                                                                <tr>
                                                                    <td><?php echo $partInfo['titre']; ?></td>
                                                                    <td><a href="https://www.urban-rivals.com/fr/characters/?id_perso=<?php echo $partInfo['idcarte']; ?>"><?php echo $partInfo['carte']; ?></a></td>
                                                                    <td><?php echo $partInfo['exemplaires']; ?></td>
                                                                    <td><?php echo $partInfo['creation']; ?></td>
                                                                    <td><?php echo $partInfo['dateMaj']; ?></td>
                                                                    <td class="center"><a href="viewop.php?idop=<?php echo $partInfo['idop']; ?>#participations" class="btn btn-primary"><span class="fas fa-eye"></span> Voir l'opération</a>
                                                                        <?php if($_GET['iduser'] == $_SESSION['iduser'] || $_SESSION['rank']) {?>
                                                                        <a href="partupdate.php?idpart=<?php echo $partInfo['idpart']; ?>" class="btn btn-warning"><span class="fas fa-edit"></span> Modifier</a>
                                                                        <a href="deletepart.php?idpart=<?php echo $partInfo['idpart']; ?>" class="btn btn-danger"><span class="fas fa-trash"></span> Supprimer</a>
                                                                    </td>
                                                                    <?php } ?>
                                                                </tr>
                                                                <?php
                                                            }
                                                            if(!$compteurLots) {
                                                                ?>
                                                                <tr>
                                                                    <td colspan="6" class="center">L'utilisateur n'a participé à aucune ancienne opération.</td>
                                                                </tr>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    if($nbOldLot >= 1) {
                                    ?>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="card">
                                                <h3 class="card-header bg-info justify-content-between d-flex align-items-center text-white" id="lots">Anciens lot <span class="badge badge-pill badge-secondary"><?php echo $nbOldLot;?></span></h3>
                                                <div class="card-content table-responsive">
                                                    <table class="table table-striped table-hover table-bordered mb-0">
                                                        <thead class="thead-dark">
                                                            <tr>
                                                                <th scope="col">Carte</th>
                                                                <th scope="col">Exemplaires</th>
                                                                <th scope="col">Objectif</th>
                                                                <th scope="col" class="d-none d-lg-table-cell">Progression</th>
                                                                <th scope="col">Date de création</th>
                                                                <th scope="col">Date de modification</th>
                                                                <?php if($_GET['iduser'] == $_SESSION['iduser']) { ?>
                                                                    <th scope="col">Statut</th>
                                                                    <th scope="col">Actions</th>
                                                                <?php } ?>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            if($_GET['iduser'] == $_SESSION['iduser']) {
                                                                $reqOldLot = $bdd->prepare('SELECT *, DATE_FORMAT(dateCreation, \'%d/%m/%Y %H:%i\') AS dateCreation, DATE_FORMAT(dateEdit, \'%d/%m/%Y %H:%i\') AS dateEdit FROM lots WHERE iduser = :iduser AND fini = 1 ORDER BY exemplaires DESC, lots.dateEdit DESC');
                                                            } else {
                                                                $reqOldLot = $bdd->prepare('SELECT *, DATE_FORMAT(dateCreation, \'%d/%m/%Y %H:%i\') AS dateCreation, DATE_FORMAT(dateEdit, \'%d/%m/%Y %H:%i\') AS dateEdit FROM lots WHERE iduser = :iduser AND fini = 0 AND visible = 1 ORDER BY exemplaires DESC, lots.dateEdit DESC');
                                                            }
                                                            $reqOldLot->execute(array("iduser" => $_GET['iduser']));
                                                            $compteurLots = false;

                                                            while($infosLotsPersos = $reqOldLot->fetch()) {
                                                                $pourcentageTmp = ($infosLotsPersos['exemplaires']/$infosLotsPersos['objectif'])*100;
                                                                $pourcentage = ($pourcentageTmp >= 99 && $pourcentageTmp < 100) ? 99 : ceil($pourcentageTmp);
                                                                $pourcentage100 = ($pourcentage > 100) ? 100 : $pourcentage;
                                                                $compteurLots = true;
                                                                ?>
                                                                <tr>
                                                                    <td><a href="https://www.urban-rivals.com/fr/characters/?id_perso=<?php echo $infosLotsPersos['idcarte']; ?>"><?php echo $infosLotsPersos['carte'];?></a></td>
                                                                    <td><?php echo $infosLotsPersos['exemplaires'];?> exemplaires</td>
                                                                    <td><?php echo $infosLotsPersos['objectif']; ?> exemplaires</td>
                                                                    <td  class="d-none d-lg-table-cell">
                                                                        <div class="progress">
                                                                            <div class="progress-bar bg-secondary" role="progressbar" aria-valuenow="<?php echo $pourcentage100; ?>" aria-valuemax="100" aria-valuemin="0" style="width:<?php echo $pourcentage100; ?>%">
                                                                                <?php echo $pourcentage?>%
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td><?php echo $infosLotsPersos['dateCreation']; ?></td>
                                                                    <td><?php echo $infosLotsPersos['dateEdit']; ?></td>
                                                                    <?php if($_GET['iduser'] == $_SESSION['iduser']) { ?>
                                                                        <td class="center"><?php
                                                                            if(!$infosLotsPersos['visible']) echo '<span class="fas fa-eye-slash" title="Lot privé"></span>';
                                                                            else echo '<span class="fas fa-eye" title="Lot public"></span>';
                                                                            ?>
                                                                        </td>
                                                                        <td class="center">
                                                                            <a class="btn btn-warning" href="editlot.php?idlot=<?php echo $infosLotsPersos['idlot']; ?>"><span class="fas fa-edit"></span> Modifier</a>
                                                                            <a class="btn btn-danger" href="deletelot.php?idlot=<?php echo $infosLotsPersos['idlot']; ?>"><span class="fas fa-trash"></span> Supprimer</a>
                                                                        </td>
                                                                    <?php } ?>
                                                                </tr>
                                                                <?php
                                                            }
                                                            if(!$compteurLots) {
                                                                $colspan = ($_GET['iduser'] == $_SESSION['iduser']) ? 8 : 6;
                                                                ?>
                                                                <tr>
                                                                    <td colspan="<?php echo $colspan?>">Aucun lot n'est disponible.</td>
                                                                </tr>
                                                                <?php
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                    </div>
                                        </div>
                                    </div>
                                </div>
                                <aside class="col-lg-2 d-none d-lg-block mt-4">
                                    <div class="card">
                                        <h3 class="card-header"><?php echo $userInfos['username']; ?></h3>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item"><a href="viewuser.php?iduser=<?php echo $_GET['iduser']; ?>" class="list-group-item-action d-flex justify-content-between align-items-center"><span><span class="fas fa-arrow-left"></span> Retour à la page de <?php echo $userInfos['username']; ?></span></a></li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center"><?php
                                                    if($nbOldPart >= 1) {
                                                        ?>
                                                        <a href="#participations" class="list-group-item-action d-flex justify-content-between align-items-center">
                                                        <?php
                                                    }
                                                ?>
                                                Anciennes participations aux opérations
                                                <span class="badge badge-pill badge-<?php echo ($nbOldPart >= 1) ? 'primary' : 'secondary'; ?>"><?php echo $nbOldPart; ?></span>
                                                <?php
                                                    if($nbOldPart >= 1) {
                                                        ?>
                                                        </a>
                                                        <?php
                                                    }
                                                ?>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <?php
                                                if($nbOldLot >= 1) {
                                                    ?>
                                                    <a href="#lots" class="list-group-item-action d-flex justify-content-between align-items-center">
                                                    <?php
                                                }
                                                ?>
                                                Anciens lots personnels
                                                <span class="badge badge-pill badge-<?php echo ($nbOldLot >= 1) ? 'primary' : 'secondary'; ?>"><?php echo $nbOldLot; ?></span>
                                            </li>
                                        </ul>
                                    </div>
                                </aside>
                            </div>
                        </div>
                    <?php
                    include('includes/scripts.html');
                }
                else {
                    ?>
                    <script>
                        alert("L'utilisateur en cours n'a pas d'ancien lot.");
                        document.location.href="<?php echo $redirectLink; ?>";
                    </script>
                    <?php
                }
            }
            else {
                showError("unknownUser");
            }
        }
        else {
            showError("noIdForUser");
        }
    }
    else {
        showError("unlogged");
    }
    ?></html>