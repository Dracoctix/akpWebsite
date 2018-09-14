<?php
session_start();
include('includes/database.php');
include('includes/errors.php');
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style/index.css">
    <link rel="icon" href="favicon.ico">
    <title>AKP Business Team : Anciennes opérations</title>
    <meta name="robots" content="noindex,nofollow">
    <?php
    include('includes/bootstrapcss.html');
    ?>
</head>
<?php
if(isset($_SESSION['rank']) && isset($_SESSION['iduser']) && $_SESSION['iduser']) {
    $reqTestOldOp = $bdd->query('SELECT * FROM op WHERE status = 2');
    if($reqTestOldOp->fetch()) {
        ?>
        <body>
        <div class="container-fluid">
            <header class="row">
                <h1 class="col-lg-12 page-header">Anciennes opérations</h1>
            </header>
            <?php
            $notIndex = true;
            include('navbar.php');?>
            <div class="row">
                <div class="col-lg-12">
                <table class="table table-hover table-striped table-bordered">
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
                    $listeOldOp = $bdd->query('SELECT *, DATE_FORMAT(creationdate, \'%d/%m/%Y %H:%i\') AS creationdate FROM op WHERE status = 2');
                    while($infosOp = $listeOldOp->fetch()) {
                        $objectif = ($infosOp['objectif']) ? $infosOp['objectif']." exemplaires" : "Aucun objectif";
                        ?>
                        <tr>
                            <td><?php echo $infosOp['titre']; ?></td>
                            <td><a href="https://www.urban-rivals.com/characters/?id_perso=<?php echo $infosOp['idcarte'];?>"><?php echo $infosOp['carte']; ?></a></td>
                            <td><?php echo $infosOp['creationdate']; ?></td>
                            <td><?php echo $objectif; ?></td>
                            <td><?php echo $infosOp['prix']; ?> clintz</td>
                            <td class="center"><a class="btn btn-primary" href="viewop.php?idop=<?php echo $infosOp['idop'];?>"><span class="fas fa-search"></span> Détails</a></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
                </div>
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
        <script>
            alert("Aucune opération n'est terminée. Il n'y a donc rien à afficher.");
            document.location.href="index.php";
        </script>
        <?php
    }
} else {
    showError("unlogged");
}
?>
</html>
