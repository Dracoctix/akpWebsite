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
    <title>Détails de l'opération</title>
    <?php
    include('includes/bootstrapcss.html');
    ?>
</head>
<body>
<div class="container-fluid">
<?php
if(isset($_SESSION['username']) && isset($_SESSION['iduser']) && isset($_SESSION['email'])) {
    if(isset($_GET['idop']) && $_GET['idop']) {
//        On récupère les informations sur l'opération depuis la BDD.
        $reqOp = $bdd->prepare('SELECT *, DATE_FORMAT(creationdate,
        \'%d/%m/%Y %H:%i\') AS creationdate FROM op LEFT JOIN users ON op.idcreateur = users.iduser WHERE idop = :idop AND (status = 1 OR
        status = 2)');

        $reqOp->execute(array("idop" => $_GET['idop']));
        if($opInfos = $reqOp->fetch()) {
            $notIndex = true;?>
        <div class="row">
            <header class="col-lg-12 page-header">
                <h1>Détails de l'opération "<?php echo $opInfos['titre']; ?>" <?php if($_SESSION['rank']) {?>
                        <button type="button" class="btn btn-primary" onclick="document.location.href='adm/opedit.php?idop=<?php echo $_GET['idop']; ?>'"><span class="fas fa-edit"></span> Modifier</button>
                    <?php } ?></h1>
            </header>
        </div>
            <?php
                include('navbar.php');
                $createur = "Créateur inconnu";
                if($opInfos['idcreateur']) {
                    $createur = $opInfos['username'];
                }
                $totalCartes = 0;
                $totalChef = 0;
                // On récupère les participations
                $reqParticipants = $bdd->prepare(
                    'SELECT *, DATE_FORMAT(creation, \'%d/%m/%Y %H:%i\') AS creation,
                              DATE_FORMAT(datemaj, \'%d/%m/%Y %H:%i\') AS datemaj FROM participation JOIN users ON
                              participation.iduser = users.iduser WHERE  idop = :idop ORDER BY exemplaires DESC');
                $reqParticipants->execute(array("idop" => $_GET['idop']));

                $reqInfos = $bdd->prepare('SELECT COUNT(*) AS nombre, SUM(exemplaires) AS totalCartes, SUM(exemplaireschef) AS totalChef FROM participation WHERE idop = :idop');
                $reqInfos->execute(array("idop" => $_GET['idop']));
                $compteurs = $reqInfos->fetch();

                $totalParticipants = $compteurs["nombre"];
                $totalCartes = $compteurs['totalCartes'];
                $totalChef = $compteurs['totalChef'];

                // On vérifie si des utilisateurs n'ont pas participé
                $reqNonParticipant = $bdd->prepare('SELECT COUNT(users.iduser) AS nbNon FROM participation JOIN users ON participation.iduser = users.iduser WHERE users.iduser NOT IN
                                                                                                      (SELECT users.iduser FROM participation JOIN users ON participation.iduser = users.iduser WHERE idop = :idop)');
                $reqNonParticipant->execute(array("idop" => $_GET['idop']));
                $nombreNonParticipant = $reqNonParticipant->fetch();

                // On vérifie si l'utilisateur a déjà participé.
                $reqDejaParticipant = $bdd->prepare('SELECT COUNT(*) AS nombre FROM participation WHERE idop = :idop AND iduser = :iduser');
                $reqDejaParticipant->execute(array(
                    "idop" => $_GET['idop'],
                    "iduser" => $_SESSION['iduser']
                ));
                $dejaParticipant = $reqDejaParticipant->fetch()['nombre'];

                $reqNbComments = $bdd->prepare('SELECT COUNT(*) AS nombre FROM comments WHERE idop = :idop');
                $reqNbComments->execute(array("idop" => $_GET['idop']));
                $totalComments = $reqNbComments->fetch()['nombre'];

            $pourcentage = 0;
            if($opInfos['objectif']) {
                $pourcentage = round(($totalCartes/$opInfos['objectif'])*100);
                if($totalCartes < $opInfos['objectif'] && $pourcentage == 100) {
                    $pourcentage = 99;
                }
            }
            ?>
    <div class="row mt-4">
        <div class="col-lg-10">
            <div class="card">
            <div class="card-header bg-info text-white">
                <h3 id="details">Informations</h3>
            </div>
                <ul class="list-group">
                    <li class="list-group-item"><a class="list-group-item-action d-flex justify-content-between align-items-center" href="https://www.urban-rivals.com/characters/?id_perso=<?php echo $opInfos['idcarte']; ?>">Carte ciblée
                        <span class="badge badge-pill badge-primary"><?php echo $opInfos['carte']; ?></span></a></li>
                    <li class="list-group-item d-flex justify-content-between align-items-center"><?php echo ($opInfos['status'] == 1) ? 'Date de lancement' : 'Date de fin';  ?><span class="badge badge-pill badge-primary"><?php echo $opInfos['creationdate']; ?></span></li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">Objectif visé
                    <span class="badge badge-pill <?php echo ($opInfos['objectif']) ? 'badge-primary' : 'badge-secondary;'?>"><?php echo ($opInfos['objectif']) ? $opInfos['objectif'].' exemplaires' : 'Aucun objectif'; ?></span></li>
                    <li class="list-group-item d-flex justify-content-between align-items-center"><?php echo ($opInfos['phase']) ? 'Prix visé' : "Prix d'achat maximal"; ?>
                    <span class="badge badge-pill badge-primary"><?php echo $opInfos['prix']; ?> clintz/tête</span></li>
                    <li class="list-group-item"><a class="list-group-item-action d-flex justify-content-between align-items-center" href="<?php echo ($opInfos['idcreateur']) ? "viewuser.php?iduser=".$opInfos['idcreateur'] : '#' ?>">Créateur
                        <span class="badge badge-pill badge-<?php echo ($opInfos['idcreateur']) ? 'primary' : 'secondary'?>"><?php echo $createur; ?></span></a></li>
                    <li class="list-group-item d-flex justify-content-between align-items-center"><?php echo ($opInfos['status'] == 2) ? 'Statut' : 'Phase' ?>
                            <?php
                            if($opInfos['status'] == 1) {
                                ?>
                                <span class="badge badge-pill badge-primary"><?php echo ($opInfos['phase']) ? 'Hausse des prix' : 'Stockage'; ?></span>
                                <?php
                            } elseif($opInfos['status'] == 2) {
                                ?>
                                <span class="badge badge-pill badge-secondary">Terminée</span>
                                <?php
                            }
                            ?>
                    </li>
                    <?php if($totalParticipants) {?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Stock total <span class="badge badge-pill badge-primary"><?php echo $totalCartes.' exemplaires'?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Cartes chez le chef <span class="badge badge-pill badge-success"><?php echo $totalChef.' chez le chef';?></span>
                        </li>
                    <?php }
                    else { ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">Participants
                        <span class="badge badge-pill badge-secondary">Aucun participant</span></li>
                    <?php } ?>

                    <?php if($opInfos['description']) { ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center"><?php echo $opInfos['description'];?></li>
                    <?php } ?>

                </ul>
        </div>
        </div>
        <aside class="col-lg-2 d-none d-lg-block ">
            <div class="card">
                <h3 class="card-header">Opération</h3>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><a class="list-group-item-action d-flex justify-content-between align-items-center" href="#details">Détails
                        <?php
                        if($opInfos['status'] == 1) {
                            ?>
                            <span class="badge badge-pill badge-success">En cours</span>
                            <?php
                        } elseif($opInfos['status'] == 2) {
                            ?>
                            <span class="badge badge-pill badge-secondary">Terminée</span>
                            <?php
                        }
                        ?></a></li>
                    <li class="list-group-item"><a class="list-group-item-action d-flex justify-content-between align-items-center" href="#participations">Participations
                        <span class="badge badge-pill <?php
                            echo ($opInfos['status'] == 2) ? 'badge-secondary' : (($opInfos['status'] == 1 && !$dejaParticipant) ? 'badge-success' : 'badge-primary');
                        ?>"><?php echo $totalParticipants; ?></span></a></li>
                    <li class="list-group-item"><a class="list-group-item-action d-flex justify-content-between align-items-center" href="#commentaires">Commentaires
                        <span class="badge badge-pill <?php echo (($opInfos['status'] == 1) && $totalComments > 0) ? 'badge-primary' : 'badge-secondary' ?>"><?php echo $totalComments; ?></span></a></li>
                    <?php if($opInfos['status'] == 1 || $_SESSION['rank']) { ?><li class="list-group-item"><a class="list-group-item-action d-flex justify-content-between align-items-center" href="#postNewComment" >Poster un commentaire</a></li><?php }?>
                </ul>
            </div>
        </aside>
    </div>
    <div class="row d-none d-md-block">
        <div class="col-lg-12">
            <div class="progress my-4" style="height: 25px;">
                <div class="progress-bar progress-bar-striped <?php echo ($pourcentage >= 100) ? 'bg-success' : 'bg-primary' ; ?>" style="width:<?php echo ($pourcentage > 100) ? 100 : $pourcentage; ?>%;" role="progressbar" aria-valuenow="<?php echo ($pourcentage > 100) ? 100 : $pourcentage; ?>" aria-valuemin="0" aria-valuemax="100">
                    <strong><?php echo $pourcentage.'%';?></strong>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-4">
                <h2 id="participations" class="card-header bg-info text-white">
                    <?php
                    if($totalParticipants) {
                    ?> Liste des participations
                    <?php }
                    else { ?>
                    Aucune participation actuellement<?php }?>
                <?php if(($opInfos['status'] == 1 && !$dejaParticipant) || $_SESSION['rank'] && $nombreNonParticipant['nbNon'] > 0) {
                    ?>
                    <button type="button" onclick="document.location.href='partupdate.php?idop=<?php echo $_GET['idop']; ?>'" class="btn btn-success"><span class="fas fa-plus"></span> Participer</button>
                    <?php
                }

                ?>
                </h2>
                <div class="card-content">
                    <?php
                    if($totalParticipants) {
                        ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover mb-0">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">Joueur</th>
                                    <th scope="col">Exemplaires</th>
                                    <th scope="col">Exemplaires chez le meneur</th>
                                    <th scope="col">Date de participation</th>
                                    <th scope="col">Date de dernière mise à jour</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                        <?php
                        $reqParticipants = $bdd->prepare('SELECT *, DATE_FORMAT(creation,
            \'%d/%m/%Y %H:%i\') AS creation, DATE_FORMAT(datemaj, \'%d/%m/%Y %H:%i\') AS datemaj FROM participation
            JOIN users ON participation.iduser = users.iduser WHERE idop = :idop ORDER BY exemplaires DESC');
                        $reqParticipants->execute(array("idop" => $_GET['idop']));
                        while($participantsInfos = $reqParticipants->fetch()) {
                            ?>
                            <tr>
                                <td><a href="viewuser.php?iduser=<?php echo $participantsInfos['iduser']; ?>"><?php
                                    echo $participantsInfos['username']; ?></td></a>
                                <td><?php echo $participantsInfos['exemplaires']; ?> exemplaires</td>
                                <td><?php echo $participantsInfos['exemplaireschef']; ?> exemplaires chez le chef</td>
                                <td><?php echo $participantsInfos['creation']; ?></td>
                                <td><?php echo $participantsInfos['datemaj']; ?></td>
                                <td class="center"><?php
                                    if($_SESSION['iduser'] == $participantsInfos['iduser'] || $_SESSION['rank']) {
                                        ?><a class="btn btn-warning" href="partupdate.php?idpart=<?php echo $participantsInfos['idpart'];
                                        ?>"><span class="fas fa-edit"></span> Modifier</a> <a class="btn btn-danger" href="deletepart.php?idpart=<?php
                                        echo $participantsInfos['idpart'];?>"><span class="fas fa-trash"></span> Supprimer</a><?php
                                    }
                                    ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                        </table>
                    </div>
                            <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-2">
                <h2 id="commentaires" class="card-header text-white bg-info"><?php if($totalComments) {
                        echo $totalComments;
                    }
                    else {
                        echo "Aucun";
                    }
                    ?> commentaire<?php if($totalComments > 1){ echo 's'; }?></h2>
                <div class="list-group">
                    <?php
                    if($totalComments) {
                        ?>
                                <div id="sectionCommentaire">
                                    <?php
                                    $reqListcomments = $bdd->prepare('SELECT *, DATE_FORMAT(date, \'Le <strong>%d/%m/%Y</strong> à <strong> %H:%i</strong>\') AS moment FROM comments NATURAL LEFT JOIN users WHERE idop = :idop ORDER BY date ASC;');
                                    $reqListcomments->execute(array("idop" => $opInfos['idop']));
                                    $commentCount = 0;
                                    while($commentInfos = $reqListcomments->fetch()) {
                                        $commentCount++;
                                        $nombreTotal = $commentCount;
                                        $urlAvatar = "https://akp.dracoctix.fr/noname.jpg";
                                        $size = 125;
                                        $color = NULL;
                                        $urlAvatar = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($commentInfos['email']))) . "?d=" . urlencode( $urlAvatar ) . "&s=" . $size;
                                        ?>
                                        <div class="list-group-item">
                                            <div class="row">
                                                <div class="row col-lg-12">
                                                    <div class="d-none d-lg-block">
                                                        <img src="<?php echo $urlAvatar;?>" alt="avatar" class="img-responsive">
                                                    </div>
                                                    <div class="col-lg-11" style="padding-left: 50px;">
                                                        <div class="row d-flex justify-content-between">
                                                            <div>
                                                                <div class="row">
                                                                    <div>
                                                                        <strong>
                                                                            <?php echo ($commentInfos['iduser']) ? '<a href="viewuser.php?iduser='.$commentInfos['iduser'].'">'.$commentInfos['username'].'</a>' : '<em>Compte supprimé</em>'?>
                                                                        </strong>
                                                                        <?php
                                                                        if($commentInfos['rank']) {
                                                                            ?>
                                                                            &nbsp;<span class="fas fa-user-circle" title="Administrateur"></span>
                                                                            <?php
                                                                        }
                                                                        if($commentInfos['iduser'] == $opInfos['idcreateur']) {
                                                                            ?>
                                                                            &nbsp;<span class="fas fa-user-secret" title="Créateur de l'opération"></span>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <em><?php echo $commentInfos['moment']; ?></em>
                                                                </div>
                                                            </div>
                                                            <div>
                                                            <?php
                                                            if($_SESSION['iduser'] == $commentInfos['iduser'] || $_SESSION['rank'] == 1) {
                                                                ?>
                                                                <div class="actions">
                                                                    <button class="btn btn-warning" onclick="document.location.href='editcomment.php?idcomment=<?php echo $commentInfos['idcomment'];?>'"><span class="fas fa-edit"></span> Modifier</button>
                                                                    <button class="btn btn-danger" onclick="document.location.href='deletecomment.php?idcomment=<?php echo $commentInfos['idcomment'];?>'"><span class="fas fa-trash"></span> Supprimer</button>
                                                                </div>
                                                                <?php
                                                            }
                                                            ?>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <?php echo $commentInfos['text']; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
        //                            }
                                    ?>
                                        <?php
                            }?>
                                    </div>
                        <div class="list-group-item justify-content-center d-flex" id="commentsNav">
                        </div>
                        <?php
                    }
                    if($opInfos['status'] == 1 || $_SESSION['rank']) {
                        ?>
                        <div class="list-group-item">
                            <div class="row mb-2">
                                <h3 class="col-lg-12" id="postNewComment">Nouveau commentaire</h3>
                            </div>
                            <div class="row">
                                <form class="col-lg-12 form-horizontal" action="postcomment.php" method="post">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group input-group">
                                                <textarea name="commentContent" class="form-control col-lg-12" id="commentContent" placeholder="Votre commentaire..." rows="10"></textarea><br>
                                                <small id="commentContentText" class="form-text text-muted droite col-lg-12">Les commentaires doivent comporter plus de 3 caractères.</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row d-flex justify-content-center">
                                        <input name="idop" type="hidden" value="<?php echo $_GET['idop'];?>" id="idop">
                                        <div class="form-group col-lg-12 center">
                                            <div class="btn-group col-lg-6 col-md-12">
                                                <button type="submit" class="col-lg-6 col-md-12 btn btn-success"><span class="fas fa-comment"></span> Poster</button><button type="reset" class="d-none d-lg-block col-lg-6 btn btn-danger"><span class="fas fa-undo"></span> Réinitialiser le formulaire</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
            <?php
            include('includes/scripts.html');
            ?>
<script src="scripts/commentsNav.js"></script>
<script src="scripts/commentVerif.js"></script>
<?php
        }
        else {
            showError('unknownOp');
        }
    }
    else {
            ?>
        <script>
            document.location.href = "index.php";
        </script>
        <?php
    }
}
else {
    showError("unlogged");
}
?>
</body>
</html>