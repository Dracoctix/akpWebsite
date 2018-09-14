<?php
session_start();
include('../includes/database.php');
include('../includes/errors.php');
?>
<!doctype html>
<html>
<head>
    <link rel="stylesheet" href="../style/index.css">
    <link rel="icon" href="../favicon.ico">
    <link rel="stylesheet" href="../style/adm.css">
    <meta charset="utf-8">
    <title>Panneau d'administration : Opérations</title>
    <meta name="robots" content="noindex,nofollow">
    <?php
    include('../includes/bootstrapcss.html');
    ?>
</head>
<?php
if(isset($_SESSION['rank'])) {
    if($_SESSION['rank']) {
        $reqNbActualOp = $bdd->query('SELECT COUNT(idop) nb FROM op WHERE status = 1;');
        $nbActualOp = $reqNbActualOp->fetch()['nb'];
        $reqNbFutureOp = $bdd->query('SELECT COUNT(idop) nb FROM op WHERE status = 0;');
        $nbFutureOp = $reqNbFutureOp->fetch()['nb'];
        $reqNbOldOp = $bdd->query('SELECT COUNT(idop) nb FROM op WHERE status = 2');
        $nbOldOp = $reqNbOldOp->fetch()['nb'];
        ?>
        <body>
        <div class="container-fluid">
            <div class="row">
                <header class="col-lg-12 center">
                    <h1 class="page-header">Panneau d'administration de l'AKP Business Team : Opérations <a class="btn btn-success" href="opedit.php"><span class="fas fa-plus"></span> Ajouter</a></h1>
                </header>
            </div>
            <div class="row">
                <?php
                $page = "op";
                include('menu.php');
                ?>
            </div>
            <div class="row my-4">
                <div class="col-lg-10">
                    <div class="row mb-4">
                        <div class="col-lg-12">
                            <div class="card">
                                <h3 id="actual" class="card-header bg-info text-white align-items-center d-flex justify-content-between">Opérations actuelles
                                <span class="badge badge-pill badge-<?php echo ($nbActualOp) ? 'primary' : 'secondary'; ?>"><?php echo $nbActualOp; ?></span></h3>
                                <div class="card-content table-responsive">
                                    <table class="table table-bordered table-hover table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="d-md-none d-lg-table-cell">Identifiant</th>
                                                <th scope="col">Titre</th>
                                                <th scope="col">Carte</th>
                                                <th scope="col">Date de lancement</th>
                                                <th scope="col">Objectif</th>
                                                <th scope="col">Phase</th>
                                                <th scope="col">Taille du lot AKP</th>
                                                <th scope="col">Prix</th>
                                                <th scope="col">Nombre de participants</th>
                                                <th scope="col">Créateur</th>
                                                <th scope="col">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // On récupère toutes les opérations actuelles.
                                            $actualOp = $bdd->query('SELECT op.*, users.*, COUNT(participation.idpart) AS nbPart, SUM(exemplaires) AS nbEx FROM users RIGHT JOIN op ON op.idcreateur = users.iduser NATURAL LEFT JOIN participation WHERE status = 1 GROUP BY op.idop'); // Requête utilisée pour récupérer les opérations en cours.
                                            while($actualOpInfo = $actualOp->fetch()) {
                                                $createur = NULL;
                                                $objectif = ($actualOpInfo['objectif']) ? $actualOpInfo['objectif']." exemplaires" : "Aucun objectif";
                                                $phase = ($actualOpInfo['phase']) ? "Montée des prix" : "Stockage";
                                                if($actualOpInfo['idcreateur'] == 0) {
                                                    $createur = "Inconnu";
                                                }
                                                else {
                                                    $createur = $actualOpInfo['username'];
                                                }

                                                echo "<tr>
                                                        <td class='d-md-none d-lg-table-cell'>".$actualOpInfo['idop']."</td>
                                                        <td>".$actualOpInfo['titre']."</td>
                                                        <td><a href='https://www.urban-rivals.com/characters/?id_perso=".$actualOpInfo['idcarte']."'>".
                                                        $actualOpInfo['carte']."</a></td>
                                                        <td>".$actualOpInfo['creationdate']."</td>
                                                        <td>".$objectif."</td>
                                                        <td>".$phase."</td>
                                                        <td>".$actualOpInfo['nbEx']." exemplaires</td>
                                                        <td>".$actualOpInfo['prix']." clintz/tête</td>
                                                        <td>".$actualOpInfo['nbPart']." participants</td>
                                                        <td>".$createur."</td>
                                                        <td class='center'><a href='opedit.php?idop=".$actualOpInfo['idop']."' class='btn btn-warning'><span class='fas fa-edit'></span> Modifier</a>
                                                        <a href='deleteop.php?idop=".$actualOpInfo['idop']."' class='btn btn-danger'><span class='fas fa-trash'></span> Supprimer</a>
                                                        <a href='../viewop.php?idop=".$actualOpInfo['idop']."' class='btn btn-primary'><span class='fas fa-search'></span> Détails</a></td>
                                                      </tr>";
                                            }
                                            if(!$nbActualOp) {
                                                echo "<tr><td class='center' colspan='11'>Aucune opération n'est en cours.</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-lg-12">
                            <div class="card">
                                <h3 id="future" class="card-header bg-info text-white align-items-center d-flex justify-content-between">Opérations futures
                                <span class="badge badge-pill badge-<?php echo $nbFutureOp ? 'primary' : 'secondary'; ?>"><?php echo $nbFutureOp; ?></span></h3>
                                <div class="card-content table-responsive">
                                    <table class="table table-hover table-striped table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th class="d-md-none d-lg-table-cell" scope="col">Identifiant</th>
                                                <th scope="col">Titre</th>
                                                <th scope="col">Carte</th>
                                                <th scope="col">Date de création</th>
                                                <th scope="col">Objectif</th>
                                                <th scope="col">Prix</th>
                                                <th scope="col">Créateur</th>
                                                <th scope="col">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $futureOp = $bdd->query('SELECT * FROM op JOIN users ON op.idcreateur = users.iduser WHERE status = 0');
                                            while($futureOpInfo = $futureOp->fetch()) {
                                                $createur = NULL;
                                                $objectif = ($futureOpInfo['objectif']) ? $futureOpInfo['objectif']." exemplaires" : "Aucun objectif";
                                                if($futureOpInfo['idcreateur'] == 0) {
                                                    $createur = "Inconnu";
                                                }
                                                else {
                                                    $createur = $futureOpInfo['username'];
                                                }
                                                echo "<tr>
                                                        <td class='d-md-none d-lg-table-cell'>".$futureOpInfo['idop']."</td>
                                                        <td>".$futureOpInfo['titre']."</td>
                                                        <td><a href='https://www.urban-rivals.com/characters/?id_perso=".$futureOpInfo['idcarte']."'>".
                                                    $futureOpInfo['carte']."</a></td>
                                                        <td>".$futureOpInfo['creationdate']."</td>
                                                        <td>".$objectif."</td>
                                                        <td>".$futureOpInfo['prix']." clintz/tête</td>
                                                        <td>".$createur."</td>
                                                        <td class='center'><a href='opedit.php?idop=".$futureOpInfo['idop']."' class='btn btn-warning'><span class='fas fa-edit'></span> Modifier</a>
                                                        <a href='deleteop.php?idop=".$futureOpInfo['idop']."' class='btn btn-danger'><span class='fas fa-trash'></span> Supprimer</a></td>
                                                      </tr>";
                                            }
                                            if(!$nbFutureOp) {
                                                echo "<tr><td colspan='8' class='center'>Aucune opération n'est en préparation.</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <h3 class="card-header bg-info text-white d-flex align-items-center justify-content-between" id="old">Anciennes opérations
                                <span class="badge badge-pill badge-secondary"><?php echo $nbOldOp; ?></span></h3>
                                <div class="card-content table-responsive">
                                    <table class="table table-hover table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="col-lg-table-cell col-md-none">Identifiant</th>
                                                <th scope="col">Titre</th>
                                                <th scope="col">Carte</th>
                                                <th scope="col">Date de fin</th>
                                                <th scope="col">Objectif</th>
                                                <th scope="col">Prix</th>
                                                <th scope="col">Créateur</th>
                                                <th scope="col">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $oldOp = $bdd->query('SELECT * FROM op JOIN users ON op.idcreateur = users.iduser WHERE status = 2');
                                            while($oldOpInfo = $oldOp->fetch()) {
                                                $createur = NULL;
                                                $objectif = ($oldOpInfo['objectif']) ? $oldOpInfo['objectif']." exemplaires" : "Aucun objectif";
                                                if($oldOpInfo['idcreateur'] == 0) {
                                                    $createur = "Inconnu";
                                                }
                                                else {
                                                    $createur = $oldOpInfo['username'];
                                                }
                                                echo "<tr>
                                                        <td class='col-lg-table-cell col-md-none'>".$oldOpInfo['idop']."</td>
                                                        <td>".$oldOpInfo['titre']."</td>
                                                        <td><a href='https://www.urban-rivals.com/characters/?id_perso=".$oldOpInfo['idcarte']."'>".
                                                    $oldOpInfo['carte']."</a></td>
                                                        <td>".$oldOpInfo['creationdate']."</td>
                                                        <td>".$objectif."</td>
                                                        <td>".$oldOpInfo['prix']." clintz/tête</td>
                                                        <td>".$createur."</td>
                                                        <td class='center'><a href='opedit.php?idop=".$oldOpInfo['idop']."' class='btn btn-warning'><span class='fas fa-edit'></span> Modifier</a>
                                                        <a href='deleteop.php?idop=".$oldOpInfo['idop']."' class='btn btn-danger'><span class='fas fa-trash'></span> Supprimer</a>
                                                        <a href='../viewop.php?idop=".$oldOpInfo['idop']."' class='btn btn-primary'><span class='fas fa-search'></span> Détails</a></td>
                                                      </tr>";
                                            }
                                            if(!$nbOldOp) {
                                                echo "<tr><td colspan='8' class='center'>Aucune opération n'est achevée.</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="card">
                        <h3 class="card-header">Accès rapide</h3>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><a class="align-items-center list-group-item-action d-flex justify-content-between" href="#actual">Opérations actuelles
                                <span class="badge badge-pill badge-<?php echo ($nbActualOp) ? 'primary' : 'secondary'; ?>"><?php echo $nbActualOp; ?></span></a></li>
                            <li class="list-group-item"><a class="align-items-center list-group-item-action d-flex justify-content-between" href="#future">Opérations futures
                                <span class="badge badge-pill badge-<?php echo ($nbFutureOp) ? 'primary' : 'secondary'; ?>"><?php echo $nbFutureOp; ?></span></a></li>
                            <li class="list-group-item"><a class="align-items-center list-group-item-action d-flex justify-content-between" href="#old">Anciennes opérations
                                <span class="badge badge-pill badge-secondary"><?php echo $nbOldOp; ?></span></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?php
        include '../includes/scripts.html';
        ?>
        </body>
        <?php
    }
    else {
        showError("notAdmin", "../index.php");
    }
}
else {
    showError("unlogged", "../index.php");
}
?>
</html>
