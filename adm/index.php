<?php
session_start();
include('../includes/database.php');
include('../includes/errors.php');
$page = "users";
?>
<!doctype html>
<html>
<head>
    <link rel="stylesheet" href="../style/index.css">
    <link rel="icon" href="../favicon.ico">
    <link rel="stylesheet" href="../style/adm.css">
    <meta charset="utf-8">
    <title>Panneau d'administration : Utilisateurs</title>
    <meta name="robots" content="noindex,nofollow">
    <?php
    include('../includes/bootstrapcss.html')
    ?>
</head>
<?php
if(isset($_SESSION['rank'])) {
    if($_SESSION['rank']) {
        $reqNbUtilisateursInactifs = $bdd->query('SELECT COUNT(iduser) nb FROM users WHERE active = 0 AND banned = 0');
        $nbUtilisateursInactifs = $reqNbUtilisateursInactifs->fetch()['nb'];
        $reqNbUtilisateursActifs = $bdd->query('SELECT COUNT(iduser) nb FROM users WHERE active = 1 AND banned = 0');
        $nbUtilisateursActifs = $reqNbUtilisateursActifs->fetch()['nb'];
        $reqNbBannis = $bdd->query('SELECT COUNT(iduser) nb FROM users WHERE banned = 1');
        $nbBannis = $reqNbBannis->fetch()['nb'];
        ?>
        <body>
        <div class="container-fluid">
            <div class="row">
                <header class="col-lg-12 center">
                    <h1 class="page-header">Panneau d'administration de l'AKP Business Team : Utilisateurs</h1>
                </header>
            </div>
            <div class="row">
            <?php
            include('menu.php');
            ?>
            </div>
            <div class="row my-4">
                <div class="col-lg-10 col-md-12">
                    <div class="row mb-4">
                        <div class="col-lg-12">
                            <div class="card">
                                <h3 id="pending" class="card-header bg-info text-white d-flex justify-content-between align-items-center">Comptes en attente d'activation
                                <span class="badge badge-pill badge-<?php echo ($nbUtilisateursInactifs) ? 'danger' : 'secondary'; ?>"><?php echo $nbUtilisateursInactifs; ?></span></h3>
                                <div class="card-content table-responsive">
                                    <table class="table table-striped table-hover table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="d-md-none d-lg-table-cell">Identifiant</th>
                                                <th scope="col">Nom d'utilisateur</th>
                                                <th scope="col">Adresse mail</th>
                                                <th scope="col">Date d'inscription</th>
                                                <th scope="col">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $usersInactive = $bdd->query('SELECT * FROM users WHERE active = 0 AND banned = 0');
                                            while($usersInactifsInfo = $usersInactive->fetch()) {
                                                echo("<tr>
                                                        <td class='d-md-none d-lg-table-cell'>".$usersInactifsInfo['iduser']."</td>
                                                        <td>".$usersInactifsInfo['username']."</td>
                                                        <td>".$usersInactifsInfo['email']."</td>
                                                        <td>".$usersInactifsInfo['subdate']."</td>
                                                        <td class='center'><a href='enable.php?iduser=".$usersInactifsInfo['iduser']."' class='btn btn-success'><span class='fas fa-check'></span> Approuver</a> <a href='delete.php?iduser=".$usersInactifsInfo['iduser']."' class='btn btn-danger'><span class='fas fa-trash'></span> Supprimer</a></td>
                                                      </tr>");
                                            }
                                            if(!$nbUtilisateursInactifs) {
                                                echo "<tr><td colspan='5' class='center'>Aucun utilisateur n'attend d'être validé.</td></tr>";
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
                                <h3 class="card-header text-white bg-info d-flex justify-content-between align-items-center" id="active">Comptes actifs
                                <span class="badge badge-pill badge-secondary"><?php echo $nbUtilisateursActifs; ?></span></h3>
                                <div class="card-content table-responsive">
                                    <table class="mb-0 table table-hover table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="d-md-none d-lg-table-cell">Identifiant</th>
                                                <th scope="col">Nom d'utilisateur</th>
                                                <th scope="col">Adresse mail</th>
                                                <th scope="col">Date d'inscription</th>
                                                <th scope="col">Statut</th>
                                                <th scope="col">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $usersActifs = $bdd->query('SELECT * FROM users WHERE active = 1 AND banned = 0');
                                            while($usersActifsInfo = $usersActifs->fetch()) {
                                                $promote = ($usersActifsInfo['rank']) ? '<a href="demote.php?iduser='.$usersActifsInfo['iduser'].'" class="btn btn-warning"><span class="fas fa-arrow-down"></span> Rétrograder</a>' : '<a href="disable.php?iduser='.$usersActifsInfo['iduser'].'" class="btn btn-danger"><span class="fas fa-times"></span> Désactiver</a> <a href="promote.php?iduser='.$usersActifsInfo['iduser'].'" class="btn btn-warning"><span class="fas fa-arrow-up"></span> Promouvoir</a>';
                                                $isAdmin = ($usersActifsInfo['rank']);
                                                echo("<tr>
                                                        <td class='d-md-none d-lg-table-cell'>".$usersActifsInfo['iduser']."</td>
                                                        <td>".$usersActifsInfo['username']."</td>
                                                        <td>".$usersActifsInfo['email']."</td>
                                                        <td>".$usersActifsInfo['subdate']."</td>
                                                        <td class='center'><span class='fas ".(($isAdmin) ? "fa-user-plus" : "fa-user")." ' title='".(($isAdmin) ? "Administrateur" : "Utilisateur")."' ></span></td>
                                                        <td class='center'>".$promote." <a href='../viewuser.php?iduser=".$usersActifsInfo['iduser']."' class='btn btn-primary'><span class='fas fa-search'></span> Détails</a></td>
                                                      </tr>");
                                            }
                                            if(!$nbUtilisateursActifs) {
                                                echo "<tr><td colspan='6' class='center'>Aucun utilisateur validé n'existe.</td></tr>";
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
                                <h3 class="card-header text-white bg-info d-flex justify-content-between align-items-center" id="disabled">Comptes désactivés
                                <span class="badge badge-pill badge-secondary"><?php echo $nbBannis; ?></span></h3>
                                <div class="card-content table-responsive">
                                    <table class="table table-striped table-bordered table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="d-md-none d-lg-table-cell">Identifiant</th>
                                                <th scope="col">Nom d'utilisateur</th>
                                                <th scope="col">Adresse mail</th>
                                                <th scope="col">Date d'inscription</th>
                                                <th scope="col">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $usersBannis = $bdd->query('SELECT * FROM users WHERE banned = 1');
                                            while($usersBannisInfos = $usersBannis->fetch()) {
                                                echo("<tr>
                                                        <td class='d-md-none d-lg-table-cell'>".$usersBannisInfos['iduser']."</td>
                                                        <td>".$usersBannisInfos['username']."</td>
                                                        <td>".$usersBannisInfos['email']."</td>
                                                        <td>".$usersBannisInfos['subdate']."</td>
                                                        <td class='center'><a class='btn btn-success' href='reactive.php?iduser=".$usersBannisInfos['iduser']."'><span class='fas fa-check'></span> Réactiver</a>
                                                        <a class='btn btn-danger' href='delete.php?iduser=".$usersBannisInfos['iduser']."'><span class='fas fa-trash'></span> Supprimer</a></td>
                                                      </tr>");
                                            }
                                            if(!$nbBannis) {
                                                echo "<tr><td colspan='5' class='center'>Aucun utilisateur n'est désactivé.</td></tr> ";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 d-none d-lg-block">
                    <div class="card">
                        <h3 class="card-header">Accès rapide</h3>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><a class="align-items-center list-group-item-action d-flex justify-content-between" href="#pending">Utilisateurs en attente d'activation
                                <span class="badge badge-pill badge-<?php echo ($nbUtilisateursInactifs) ? 'danger' : 'secondary'?>"><?php echo $nbUtilisateursInactifs;?></span></a></li>
                            <li class="list-group-item"><a class="align-items-center list-group-item-action d-flex justify-content-between" href="#active">Utilisateurs activés
                                <span class="badge badge-pill badge-secondary"><?php echo $nbUtilisateursActifs; ?></span></a></li>
                            <li class="list-group-item"><a class="align-items-center list-group-item-action d-flex justify-content-between" href="#disabled">Utilisateurs désactivés
                                <span class="badge badge-pill badge-secondary"><?php echo $nbBannis; ?></span></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        </body>
        <?php
        include('../includes/scripts.html');
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
