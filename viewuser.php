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
        \'%d/%m/%Y %H:%i\') AS date, COUNT(idpart) AS nbPart FROM users LEFT JOIN participation ON users.iduser = participation.iduser WHERE users.iduser = :iduser AND active = 1 AND banned = 0 GROUP BY users.iduser');

        $reqUser->execute(array("iduser" => $_GET['iduser']));
        if($userInfos = $reqUser->fetch()) {
            $notIndex = true;
            $profil = true;
            if($_SESSION['iduser'] == $_GET['iduser']) {
                $reqNbLots = $bdd->prepare('SELECT COUNT(idlot) nbLots FROM lots WHERE iduser = :iduser AND fini = 0');
            }
            else {
                $reqNbLots = $bdd->prepare('SELECT COUNT(idlot) nbLots FROM lots WHERE iduser = :iduser AND fini = 0 AND visible = 1;');
            }
            $reqNbLots->execute(array("iduser" => $_GET['iduser']));
            $nbLots = $reqNbLots->fetch()['nbLots'];

            // On récupère les participations actives pour générer un badge différent.
            $reqNbActualPart = $bdd->prepare('SELECT COUNT(DISTINCT idPart) nbActualPart FROM participation NATURAL JOIN op WHERE status = 1 AND iduser = :iduser');
            $reqNbActualPart->execute(array("iduser" => $_GET['iduser']));
            $nbActualPart = $reqNbActualPart->fetch()['nbActualPart'];

            ?>
                <title>Profil de <?php echo $userInfos['username']; ?></title>
                </head>
                <body>
                <div class="container-fluid">
                <div class="row">
                    <header class="page-header col-lg-12">
                        <h1>Profil de <?php echo $userInfos['username']; ?></h1>
                    </header>
                </div>
                <?php
                include('navbar.php');
                $nbParts = $userInfos['nbPart'];
                // ICI
                $defaultAvatar="https://akp.dracoctix.fr/noname.jpg";
                $size = 80;
                $gravURL = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($userInfos['email']))) . "?d=" . urlencode( $defaultAvatar ) . "&s=" . $size;
                ?>
                    <div class="row my-4">
                        <div class="col-lg-10 col-md-12">
                            <div class="card">
                                <h3 id="infos" class="card-header bg-info text-white d-flex justify-content-between align-items-center"><?php echo $userInfos['username']; ?>
                                    <img src="<?php echo $gravURL?>" alt="<?php echo $userInfos['username']; ?>" class="img-fluid rounded-circle">
                                </h3>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Statut
                                        <span class="badge badge-pill badge-secondary">À venir</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Date d'inscription
                                        <span class="badge badge-pill badge-primary"><?php echo $userInfos['subdate']; ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Participations aux opérations
                                        <span class="badge badge-pill badge-<?php echo ($nbActualPart) ? 'primary' : 'secondary'; ?>">
                                            <?php echo ($nbParts) ? $nbParts : 'Aucune'; ?>
                                        </span>
                                    </li>
                                    <?php
                                    if($userInfos['description']) {
                                        ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <?php
                                            echo $userInfos['description'];
                                            ?>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-2 d-none d-lg-block">
                            <div class="card">
                                <h3 class="card-header">Utilisateur</h3>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <a href="#infos" class="list-group-item-action d-flex justify-content-between align-items-center">Informations</a>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?php
                                        if($nbParts) {
                                            ?>
                                            <a href="#participations" class="list-group-item-action d-flex justify-content-between align-items-center">
                                            <?php
                                        }
                                        ?>
                                        Participations aux opérations AKP
                                        <span class="badge badge-pill badge-<?php echo ($nbActualPart) ? 'primary' : 'secondary'; ?>">
                                            <?php echo ($nbParts) ? $nbParts : 'Aucune';?>
                                        </span>
                                        <?php
                                        if($nbParts) {
                                            echo '</a>';
                                        }
                                        ?>
                                    </li>
                                    <li class="list-group-item">
                                        <a href="#lots" class="list-group-item-action d-flex justify-content-between align-items-center">
                                            Lots personnels
                                            <?php
                                            if($nbLots) {
                                                ?>
                                                <span class="badge badge-pill badge-primary"><?php echo $nbLots; ?></span>
                                                <?php
                                            } else {
                                                ?>
                                                <span class="badge badge-pill badge-secondary">Aucun</span>
                                                <?php
                                            }
                                            ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                        <?php if($userInfos['nbPart'] > 0) {
                            $reqParts = $bdd->prepare('SELECT *, DATE_FORMAT(creation, \'%d/%m/%Y %H:%i\') creation, DATE_FORMAT(datemaj, \'%d/%m/%Y %H:%i\') dateMaj FROM participation
                                        NATURAL JOIN op WHERE iduser = :iduser AND status = 1');
                            $reqParts->execute(array(
                                "iduser" => $_GET['iduser']
                            ));
                            ?>
                    <div class="row my-4">
                        <div class="col-lg-12">
                            <div class="card">
                                <h3 class="card-header bg-info text-white" id="participations">Participations aux opérations AKP</h3>
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
                                                    <td><?php echo $partInfo['exemplaires']; ?> exemplaires, dont <?php echo $partInfo['exemplaireschef'];?> chez le chef.</td>
                                                    <td><?php echo $partInfo['creation']; ?></td>
                                                    <td><?php echo $partInfo['dateMaj']; ?></td>
                                                    <td class="center"><a class="btn btn-primary" href="viewop.php?idop=<?php echo $partInfo['idop']; ?>#participations"><span class="fas fa-search"></span> Voir l'opération</a>
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
                                                    <td class="center" colspan="6">L'utilisateur n'a participé à aucune opération en cours.</td>
                                                </tr>
                                                <?php
                                            }
                                            $reqOldPart = $bdd->prepare('SELECT COUNT(idpart) nbLot FROM participation NATURAL JOIN op WHERE iduser = :iduser AND status = 2');
                                            $reqOldPart->execute(array(
                                                "iduser" => $_GET['iduser']
                                            ));
                                            ?>
                                        </tbody>
                                    </table>
                                    <?php
                                    if($reqOldPart->fetch()['nbLot']) {
                                        ?>
                                        <div class="center my-2"><button class="btn btn-secondary" type="button" onclick="document.location.href='viewoldlot.php?iduser=<?php echo $_GET['iduser']; ?>#participations'"><span class="fas fa-archive"></span> Voir les participations du joueur à d'anciennes opérations</button></div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php }?>
                    <div class="row my-4">
                        <div class="col-lg-12">
                            <div class="card">
                                <h3 class="card-header bg-info text-white d-flex align-items-center justify-content-between" id="lots">Lots personnels de <?php echo $userInfos['username']; ?><?php if($_GET['iduser'] == $_SESSION['iduser']) { ?>
                                        <a href="editlot.php" class="btn btn-success"><span class="fas fa-plus"></span> Créer un nouveau lot</a><?php } ?></h3>
                                <div class="card-content">
                                    <table class="table table-striped table-hover table-bordered mb-0">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th scope="col">Carte</th>
                                                <th scope="col">Exemplaires</th>
                                                <th scope="col">Objectif</th>
                                                <th scope="col" class="d-none d-lg-table-cell">Progression</th>
                                                <th scope="col">Date de création</th>
                                                <th scope="col">Date d'édition</th>
                                                <?php if($_SESSION['iduser'] == $_GET['iduser']) { ?>
                                                    <th scope="col">Statut</th>
                                                    <th scope="col">Actions</th>
                                                <?php } ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if($_SESSION['iduser'] = $_GET['iduser']) {
                                                $queryLotsPersos = $bdd->prepare('SELECT *, DATE_FORMAT(dateCreation, \'%d/%m/%Y %H:%i\') AS dateCreation, DATE_FORMAT(dateEdit, \'%d/%m/%Y %H:%i\') AS dateEdit FROM lots WHERE iduser = :iduser AND fini = 0 ORDER BY exemplaires DESC, lots.dateEdit DESC');
                                            }
                                            else {
                                                $queryLotsPersos = $bdd->prepare('SELECT *, DATE_FORMAT(dateCreation, \'%d/%m/%Y %H:%i\') AS dateCreation, DATE_FORMAT(dateEdit, \'%d/%m/%Y %H:%i\') AS dateEdit FROM lots WHERE iduser = :iduser AND fini = 0 AND visible = 1 ORDER BY exemplaires DESC, lots.dateEdit DESC');
                                            }
                                            $queryLotsPersos->execute(array("iduser" => $_SESSION['iduser']));
                                            // Variable pour savoir si un lot personnel existe.
                                            $compteurLots = false;
                                            while($infosLotsPersos = $queryLotsPersos->fetch()) {
                                                $pourcentageTmp = ($infosLotsPersos['exemplaires']/$infosLotsPersos['objectif'])*100;
                                                $pourcentage = ($pourcentageTmp >= 99 && $pourcentageTmp < 100) ? 99 : ceil($pourcentageTmp);
                                                $pourcentage100 = ($pourcentage > 100) ? 100 : $pourcentage;
                                                $compteurLots = true;
                                                ?>
                                                <tr>
                                                    <td><a href="https://www.urban-rivals.com/fr/characters/?id_perso=<?php echo $infosLotsPersos['idcarte']; ?>"><?php echo $infosLotsPersos['carte']; ?></a></td>
                                                    <td><?php echo $infosLotsPersos['exemplaires']; ?> exemplaires</td>
                                                    <td><?php echo $infosLotsPersos['objectif']; ?> exemplaires</td>
                                                    <td  class="d-none d-lg-table-cell">
                                                        <div class="progress">
                                                            <div class="progress-bar bg-<?php echo ($pourcentage100 == 100) ? 'success' : 'primary'; ?>" role="progressbar" aria-valuenow="<?php echo $pourcentage100; ?>" aria-valuemax="100" aria-valuemin="0" style="width:<?php echo $pourcentage100; ?>%">
                                                                <?php echo $pourcentage?>%
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?php echo $infosLotsPersos['dateCreation']; ?></td>
                                                    <td><?php echo $infosLotsPersos['dateEdit']; ?></td>
                                                    <?php if($_SESSION['iduser'] == $_GET['iduser']) { ?>
                                                        <td class="center"><?php
                                                            if(!$infosLotsPersos['visible']) echo '<span class="fas fa-eye-slash" title="Lot privé"></span>';
                                                            else echo '<span class="fas fa-eye" title="Lot public"></span>';
                                                            ?></td>
                                                        <td class="center"><a class="btn btn-warning" href="editlot.php?idlot=<?php echo $infosLotsPersos['idlot']; ?>"><span class="fas fa-edit"></span> Modifier</a>
                                                            <a class="btn btn-danger" href="deletelot.php?idlot=<?php echo $infosLotsPersos['idlot']; ?>"><span class="fas fa-trash"></span> Supprimer</a></td>
                                                    <?php } ?>
                                                </tr>
                                                <?php
                                            }
                                            // Savoir combien de colonnes doit occuper la case dédiée à l'affichage d'un message d'absence d'opérations.
                                            if(!$compteurLots) {
                                                $colspan = ($_GET['iduser'] == $_SESSION['iduser']) ? 8 : 6;
                                                ?>
                                                <tr>
                                                    <td colspan="<?php echo $colspan?>" class="center">Aucun lot n'est disponible.</td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                    <?php
                                    // Requêtes servant à vérifier si des anciens lots sont visibles.
                                    if($_GET['iduser'] == $_SESSION['iduser']) {
                                        $reqOldLot = $bdd->prepare('SELECT COUNT(idlot) AS nbLot FROM lots WHERE iduser = :iduser AND fini = 1');
                                    }
                                    else {
                                        $reqOldLot = $bdd->prepare('SELECT COUNT(idlot) AS nbLot FROM lots WHERE iduser = :iduser AND fini = 1 AND visible = 1');
                                    }
                                    $reqOldLot->execute(array("iduser" => $_GET['iduser']));
                                    if($reqOldLot->fetch()['nbLot'] >= 1) {
                                        ?>
                                        <div class="center my-2"><a class="btn btn-secondary" href="viewoldlot.php?iduser=<?php echo $_GET['iduser']; ?>#lots"><span class="fas fa-archive"></span> Voir les anciens lots du joueur</a></div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                include('includes/scripts.html')
                ?>
                </body>
            <?php
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