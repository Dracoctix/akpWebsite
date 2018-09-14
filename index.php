<?php
session_start();
include('includes/database.php');
?>
<!doctype html>
<html lang="fr">
<?php
if(isset($_SESSION['iduser']) && $_SESSION['iduser'] != NULL) { //Si l'utilisateur est bien loggé, on lui affiche l'accueil, sinon, on lui affiche la page de connnexion.
    ?>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="style/index.css">
        <meta name="robots" content="noindex,nofollow">
        <title>AKP Business Team</title>
        <link rel="icon" href="favicon.ico">
        <?php
        include('includes/bootstrapcss.html');
        ?>
    </head>
    <body style="/*margin-left: 0; margin-right: 0;*/">
    <div class="container-fluid">
        <header class="page-header center">
            <div class="row justify-content-center">
                <img src="logo.png" alt="AKP" class="logo img-responsive">
            </div>
            <div class="row justify-content-center">
                <h1 class="col-lg-12">AKP Business Team</h1>
            </div>
        </header>
        <div class="row">
            <div class="col-lg-12">
            <?php
            $index = true;
            include 'navbar.php';
            ?>
            </div>
        </div>
        <div class="row mt-4">
            <?php
            // Opérations actuelles auxquelles l'utilisateur a participé.
            $recupNowOpPart = $bdd->prepare("SELECT op.*, DATE_FORMAT(creationdate, '%d/%m/%Y %H:%i') AS dateCreation, SUM(exemplaires) AS nbEx, COUNT(idpart) AS nbPart FROM op JOIN participation ON op.idop = participation.idop WHERE op.idop IN
            (SELECT idop FROM participation WHERE iduser = :iduser)
            GROUP BY op.idop
            HAVING status = 1
            ORDER BY creationdate DESC"); // On récupère toutes les opérations dans la BDD, qui sont censées être accessibles au public (donc pas celles futures.
            $recupNowOpPart->execute(array("iduser" => $_SESSION['iduser']));

            // On récupère le nombre d'opérations auxquelles l'utilisateur a participé.
            $recupNbOpPart = $bdd->prepare('SELECT COUNT(DISTINCT idop) nbPart FROM op NATURAL JOIN participation WHERE iduser = :iduser AND status = 1');
            $recupNbOpPart->execute(array("iduser" => $_SESSION['iduser']));
            $nbOpPart = $recupNbOpPart->fetch()['nbPart'];

            // Opérations actuelles auxquelles l'utilisateur n'a pas participé.
            $recupNowOpFree = $bdd->prepare("SELECT op.*, DATE_FORMAT(creationdate, '%d/%m/%Y %H:%i') AS dateCreation, SUM(exemplaires) AS nbEx, COUNT(idpart) AS nbPart FROM op NATURAL LEFT JOIN participation WHERE status = 1 AND op.idop NOT IN
            (SELECT idop FROM participation WHERE iduser = :iduser)
            GROUP BY op.idop ORDER BY  creationdate DESC");
            $recupNowOpFree->execute(array("iduser" => $_SESSION['iduser']));

            // On récupère le nombre d'opérations disponibles.
            $recupNbOpDisp = $bdd->prepare('SELECT COUNT(DISTINCT idop) nbPart FROM op WHERE status = 1 AND idop NOT IN
            (SELECT idop FROM participation WHERE iduser = :iduser)');
            $recupNbOpDisp->execute(array("iduser" => $_SESSION['iduser']));
            $nbOpDispo = $recupNbOpDisp->fetch()['nbPart'];

            // Anciennes opérations auxquelles l'utilisateur n'a pas participé.
            $recupOldOpPart = $bdd->prepare("SELECT op.*, DATE_FORMAT(creationdate, '%d/%m/%Y %H:%i') AS dateCreation FROM op WHERE status = 2 AND idop IN
            (SELECT idop FROM participation WHERE iduser = :iduser) ORDER BY creationdate DESC");
            $recupOldOpPart->execute(array("iduser" => $_SESSION['iduser']));

            // On récupère le nombre d'anciennes opérations
            $recupnbOldOp = $bdd->prepare('SELECT COUNT(DISTINCT idop) nbPart FROM op NATURAL JOIN participation WHERE status = 2 AND iduser = :iduser');
            $recupnbOldOp->execute(array("iduser" => $_SESSION['iduser']));
            $nbOldOp = $recupnbOldOp->fetch()['nbPart'];
            ?>
            <div class="col-lg-10 col-md-12">
                <div class="row">
                    <h2 class="col-lg-12 mb-4" id="actuelParticipant"><?php
                        if($nbOpPart > 0) {
                            echo $nbOpPart.' ';
                        }
                        ?>Opérations en cours auxquelles vous participez</h2>
                </div>
                <div class="row">
                    <section class="col-lg-12 table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">Titre</th>
                                    <th scope="col">Carte</th>
                                    <th scope="col">Date de lancement</th>
                                    <th scope="col">Participants</th>
                                    <th scope="col">Nombre de cartes</th>
                                    <th scope="col">Objectif</th>
                                    <th scope="col">Phase</th>
                                    <th scope="col">Prix</th>
                                    <th scope="col">Détails</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Pour savoir si on est passé dans la boucle d'affichage.
                                $actOpPartExist = false;
                                while ($actualOpInfo = $recupNowOpPart->fetch()) {
                                    $actOpPartExist = true;
                                    $phase = ($actualOpInfo['phase']) ? "Montée des prix" : "Stockage";
                                    $objectif = ($actualOpInfo['objectif']) ? $actualOpInfo['objectif']." exemplaires" : "Aucun objectif";
                                    $totalParticipants = $actualOpInfo['nbPart'];
                                    $totalCartes = $actualOpInfo['nbEx'];
                                    $totalParticipants = ($totalParticipants) ? $totalParticipants : "Aucun";
                                    $totalCartes = ($totalCartes) ? $totalCartes : "Aucun";
                                    ?>
                                    <tr>
                                        <td><?php echo $actualOpInfo['titre']; ?></td>
                                        <td><a href="https://www.urban-rivals.com/characters/?id_perso=<?php echo $actualOpInfo['idcarte']; ?>"><?php echo $actualOpInfo['carte']; ?></a></td>
                                        <td><?php echo $actualOpInfo['dateCreation']; ?></td>
                                        <td><?php echo $totalParticipants; ?> participants</td>
                                        <td><?php echo $totalCartes; ?> exemplaires</td>
                                        <td><?php echo $objectif; ?></td>
                                        <td><?php echo $phase; ?></td>
                                        <td><?php echo $actualOpInfo['prix']; ?> clintz/tête</td>
                                        <td class="center"><a class="btn btn-primary" href="viewop.php?idop=<?php echo $actualOpInfo['idop']; ?>"><span class="fas fa-search"></span> Détails</a></td>
                                    </tr>
                                    <?php
                                }
                                if(!$actOpPartExist) {
                                    ?>
                                    <tr>
                                        <td colspan="9" class="noOp">Vous ne participez à aucune opération en cours.</td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </section>
                </div>
                <div class="row">
                    <h2 class="col-lg-12 my-4" id="actuelDisponible"><?php
                            if($nbOpDispo >= 1) {
                                echo $nbOpDispo.' ';
                            }
                        ?>Opérations en cours auxquelles vous ne participez pas</h2>
                </div>
                <div class="row">
                    <section class="col-lg-12 table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">Titre</th>
                                    <th scope="col">Carte</th>
                                    <th scope="col">Date de lancement</th>
                                    <th scope="col">Participants</th>
                                    <th scope="col">Nombre de cartes</th>
                                    <th scope="col">Objectif</th>
                                    <th scope="col">Phase</th>
                                    <th scope="col">Prix</th>
                                    <th scope="col">Détails</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Savoir si une opération existe, vaut true dès le premier passage dans la boucle
                                $actOpFreeExist = false;
                                while ($actualOpInfo = $recupNowOpFree->fetch()) {
                                    $actOpFreeExist = true;
                                    $phase = ($actualOpInfo['phase']) ? "Montée des prix" : "Stockage";
                                    $objectif = ($actualOpInfo['objectif']) ? $actualOpInfo['objectif']." exemplaires" : "Aucun objectif";
                                    $totalParticipants = $actualOpInfo['nbPart'];
                                    $totalCartes = $actualOpInfo['nbEx'];
                                    $totalParticipants = ($totalParticipants) ? $totalParticipants : "Aucun";
                                    $totalCartes = ($totalCartes) ? $totalCartes : "Aucun";
                                    ?>
                                    <tr>
                                        <td><?php echo $actualOpInfo['titre']; ?></td>
                                        <td><a href="https://www.urban-rivals.com/characters/?id_perso=<?php echo $actualOpInfo['idcarte']; ?>"><?php echo $actualOpInfo['carte']; ?></a></td>
                                        <td><?php echo $actualOpInfo['creationdate']; ?></td>
                                        <td><?php echo $totalParticipants; ?> participants</td>
                                        <td><?php echo $totalCartes; ?> exemplaires</td>
                                        <td><?php echo $objectif; ?> </td>
                                        <td><?php echo $phase; ?></td>
                                        <td><?php echo $actualOpInfo['prix']; ?> clintz/tête</td>
                                        <td class="center"><a class="btn btn-primary" href="viewop.php?idop=<?php echo $actualOpInfo['idop']; ?>"><span class="fas fa-search"></span> Détails</a></td>
                                    </tr>
                                    <?php
                                }
                                if(!$actOpFreeExist) {
                                    ?>
                                    <tr>
                                        <td colspan="9" class="noOp">Vous ne pouvez prendre part à aucune opération.</td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </section>
                </div>
                <div class="row">
                    <h2 class="col-lg-12 my-4" id="oldParticipant"><?php
                        if($nbOldOp > 0) {
                            echo $nbOldOp.' ';
                        }
                        ?>Anciennes opérations auxquelles vous avez participé</h2>
                </div>
                <div class="row">
                    <section class="col-lg-12 table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">Titre</th>
                                    <th scope="col">Carte</th>
                                    <th scope="col">Date de fin</th>
                                    <th scope="col">Objectif</th>
                                    <th scope="col">Prix</th>
                                    <th scope="col">Détails</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Vérifier que la boucle d'affichage s'est bien exécutée une fois.
                                $oldOpExist = false;
                                while ($actualOpInfo = $recupOldOpPart->fetch()) {
                                    $oldOpExist = true;
                                    $objectif = ($actualOpInfo['objectif']) ? $actualOpInfo['objectif']." exemplaires" : "Aucun objectif";
                                    $phase = ($actualOpInfo['phase']) ? "Montée des prix" : "Stockage";
                                    ?>
                                    <tr>
                                        <td><?php echo $actualOpInfo['titre']; ?></td>
                                        <td><a href="https://www.urban-rivals.com/characters/?id_perso=<?php echo $actualOpInfo['idcarte']; ?>"><?php echo $actualOpInfo['carte']; ?></a></td>
                                        <td><?php echo $actualOpInfo['creationdate']; ?></td>
                                        <td><?php echo $objectif; ?></td>
                                        <td><?php echo $actualOpInfo['prix']; ?> clintz/tête</td>
                                        <td class="center"><a class="btn btn-primary" href="viewop.php?idop=<?php echo $actualOpInfo['idop']; ?>"><span class="fas fa-search"></span> Détails</a></td>
                                    </tr>
                                    <?php
                                }
                                if(!$oldOpExist) {
                                    ?>
                                    <tr>
                                        <td colspan="7" class="noOp">Vous n'avez participé à aucune opération terminée.</td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </section>
                </div>
            </div>
            <aside class="col-lg-2 d-none d-lg-block">
                <div class="card">
                    <h3 class="card-header">Opérations</h3>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><a class="list-group-item-action d-flex justify-content-between align-items-center" href="#actuelParticipant">En cours
                            <span class="badge badge-pill <?php echo ($nbOpPart >= 1) ? 'badge-success' : 'badge-secondary'; ?> "><?php echo $nbOpPart; ?></span></a></li>
                        <li class="list-group-item"><a class="list-group-item-action d-flex justify-content-between align-items-center" href="#actuelDisponible">Disponibles
                            <span class="badge badge-pill <?php echo ($nbOpDispo >= 1) ? 'badge-primary' : 'badge-secondary'; ?> "><?php echo $nbOpDispo; ?></span></a></li>
                        <li class="list-group-item"><a class="list-group-item-action d-flex justify-content-between align-items-center" href="#oldParticipant">Terminées
                            <span class="badge badge-secondary badge-pill"><?php echo $nbOldOp; ?></span></a></li>
                    </ul>
                </div>
            </aside>
        </div>
        <div class="row justify-content-center my-4">
            <?php
            $reqOpPassees = $bdd->query('SELECT * FROM op WHERE status = 2');
            if($reqOpPassees->fetch()) {
                ?>
                <div class="col-lg-8 col-md-12 center"><button class="btn btn-secondary col-lg-12" type="button" onclick="document.location.href='archiveop.php'"><span class="fas fa-archive"></span> Voir toutes les anciennes opérations</button></div>
                <?php
            }
            ?>
        </div>
    </div>

    <?php
    include('includes/scripts.html');
    ?>
    </body>
    <?php
}
else {
    ?>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="style/index.css">
        <meta name="robots" content="noindex,nofollow">
        <title>Connexion.</title>
        <?php
        include('includes/bootstrapcss.html');
        ?>
        <link rel="icon" href="favicon.ico">
    </head>
    <body>
    <div class="container-fluid">
        <header class="row">
            <h1 class="col-lg-12">Bienvenue sur l'AKP Business Team</h1>
        </header>
        <div class="row">
            <div class="authform col-lg-6">
                <div class="row justify-content-center">
                    <form action="login.php" method="POST" id="formulaire" class="col-lg-11 form-horizontal">
                        <div class="row justify-content-center">
                            <p class="alert alert-info col-lg-11 warnlogin">Pour accéder à la liste des lots ou les mettre à jour, vous devez vous authentifier. <a href="subscribe.php">Créer un compte</a>
                            </p>
                        </div>
                        <div class="row form-group">
                            <label for="username" class="d-none d-md-block col-lg-3 col-md-3 logLabel">Nom d'utilisateur : </label>
                            <div class="input-group col-md-9 col-lg-9">
                                <div class="input-group-prepend">
                                    <span class="input-group-text fas fa-user"></span>
                                </div>
                                <input class="form-control" type="text" id="username" name="username"
                                                                 placeholder="Nom d'utilisateur" maxlength="255" value="<?php if(isset($_SESSION['errorUsername'])) {
                                                                     echo $_SESSION['errorUsername'];
                                                                 }
                                                                 ?>" autofocus>
                                <div class="invalid-feedback droite">Votre nom d'utilisateur comporte plus de trois caractères.</div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label for="password" class=" d-none d-md-block hidden-xs-down col-lg-3 col-md-3 logLabel">Mot de passe : </label>

                            <div class="input-group col-md-9 col-lg-9">
                                <div class="input-group-prepend">
                                    <span class="input-group-text fas fa-key"></span>
                                </div>
                                <input class="form-control" type="password" id="password" name="password" placeholder="Mot de passe" maxlength="255">
                                <div class="invalid-feedback droite">Vous devez remplir cette case.</div>
                            </div>
                        </div>
                        <div class="row form-group justify-content-center">
                            <button type="submit" class="col-lg-4 col-md-12 btn btn-primary"><span class="fas fa-sign-in-alt"></span> Se connecter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
    include('includes/scripts.html');
    ?>
    </body>
    <script>
        // $(function(){
            $("#formulaire").on("submit",function() {
                var valide = true;
                if($('#username').val().length < 3) {
                    $('#username').addClass("is-invalid");
                    valide = false;
                }
                else {
                    $('#username').removeClass("is-invalid");
                }
                if($('#password').val().length < 1) {
                    $('#password').addClass("is-invalid");
                    valide = false;
                }
                else {
                    $('#username').removeClass("is-invalid");
                }

                if(!valide) {
                    $('#avertissement').show("slow").delay(400).hide("slow");
                }

                return valide;
            });
        // });
    </script>
    <?php
    $_SESSION['errorUsername'] = NULL;
}
?>
</html>
