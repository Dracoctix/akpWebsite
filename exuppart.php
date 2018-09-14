<?php
session_start();
include('includes/database.php');
include('includes/errors.php');
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Participer à une opération</title>
    <link rel="icon" href="favicon.ico">
    <meta name="robots" content="noindex,nofollow">
</head>
<?php
$retour = "index.php";
$limiteNeg = array(
    'options' => array(
            'min_range' => 0
    )
);
if(isset($_SESSION['iduser']) && $_SESSION['iduser'] && isset($_SESSION['rank'])) {
    if(isset($_POST['exemplaires']) && isset($_POST['exemplaireschef']) && ((isset($_POST['idpart']) && !(isset($_POST['idop']))) || (isset($_POST['idop']) && !(isset($_POST['idpart']))))) {
        if(isset($_POST['idop']) && $_POST['idop']) {
            $retour = "partupdate.php?idop=".$_POST['idop'];
            $verifOp = $bdd->prepare('SELECT * FROM op WHERE idop = :idop');
            $verifOp->execute(array("idop" => $_POST['idop']));
            if($opInfos = $verifOp->fetch()) {
                if((filter_var($_POST['exemplaireschef'], FILTER_VALIDATE_INT, $limiteNeg) !== false || !$_POST['exemplaireschef']) && (filter_var($_POST['exemplaires'], FILTER_VALIDATE_INT, $limiteNeg) !== false || !$_POST['exemplaires'])) {
                    if($_POST['exemplaireschef'] <= $_POST['exemplaires']) {
                        $verifUser = $bdd->prepare('SELECT * FROM users WHERE iduser = :iduser AND active = 1 AND banned = 0');
                        $verifUser->execute(array("iduser" => $_SESSION['iduser']));
                        if($infosUser = $verifUser->fetch()) {
                            $iduser = $_SESSION['iduser'];
                            if ($infosUser['rank']) {
                                $iduser = (isset($_POST['iduser']) && $_POST['iduser']) ? $_POST['iduser'] : $_SESSION['iduser'];
                                if(isset($_POST['iduser']) && !filter_var($_POST['iduser'], FILTER_VALIDATE_INT, $limiteNeg) !== false) {
                                    $_SESSION['exemplairesErreur'] = $_POST['exemplaires'];
                                    $_SESSION['exemplairesChefErreur'] = $_POST['exemplaireschef'];
                                    $_SESSION['idUserErreur'] = $_POST['iduser'];
                                    ?>
                                    <script>
                                        alert("Toutes les valeurs saisies doivent être des nombres.");
                                        document.location.href="<?php echo $retour; ?>";
                                    </script>
                                    <?php
                                }
                            } elseif ($infosUser['rank'] != $_SESSION['rank']) {
                                showError("sessionError");
                            }
                            $verifPart = $bdd->prepare('SELECT iduser FROM participation WHERE idop = :idop');
                            $verifPart->execute(array("idop" => $_POST['idop']));
                            $listeIds = array();
                            while ($partInfo = $verifPart->fetchColumn()) {
                                $listeIds[] = $partInfo;
                            }
                            if(!in_array($iduser, $listeIds)) {
                                $participation = $bdd->prepare('INSERT INTO participation (idop, iduser, exemplaires, exemplaireschef)
                                VALUES (:idop, :iduser, :exemplaires, :exemplaireschef)');
                                $exemplaires = ($_POST['exemplaires']) ? $_POST['exemplaires'] : 0;
                                $exemplaireschef = ($_POST['exemplaireschef']) ? $_POST['exemplaireschef'] : 0;
                                $participation->execute(array(
                                    "idop" => $_POST['idop'],
                                    "iduser" => $iduser,
                                    "exemplaires" => $exemplaires,
                                    "exemplaireschef" => $exemplaireschef
                                ));
                                ?>
                                <script>
                                    alert("Votre participation a bien été enregistrée");
                                    document.location.href="viewop.php?idop=<?php echo $_POST['idop'];?>"
                                </script>
                                <?php
                            }
                            else {
                                if($_SESSION['rank']) {
                                $_SESSION['exemplairesErreur'] = $_POST['exemplaires'];
                                $_SESSION['exemplairesChefErreur'] = $_POST['exemplaireschef'];
                                $_SESSION['idUserErreur'] = $iduser;
                                ?>
                                    <script>
                                        alert("L'utilisateur a déjà participé.");
                                        document.location.href = "partupdate.php?idop=<?php echo $_POST['idop'];?>";
                                    </script>
                                <?php
                                }
                                else {
                                    ?>
                                    <script>
                                        alert("Vous ne pouvez pas participer deux fois.");
                                        document.location.href="viewop.php?idop=<?php echo $_POST['idop'];?>";
                                    </script>
                                    <?php
                                }
                            }
                        }
                        else {
                            showError("sessionError");
                        }
                    }
                    else {
                        $_SESSION['exemplairesErreur'] = $_POST['exemplaires'];
                        $_SESSION['exemplairesChefErreur'] = $_POST['exemplaireschef'];
                        $_SESSION['idUserErreur'] = (isset($_POST['iduser']) && $_POST['iduser']) ? $_POST['iduser'] : $_SESSION['iduser'];
                        ?>
                        <script>
                            alert("Le nombre d'exemplaires chez le chef doit être inférieur au nombre total");
                            document.location.href="<?php echo $retour; ?>";
                        </script>
                        <?php
                    }
                }
                else {
                    $_SESSION['exemplairesErreur'] = $_POST['exemplaires'];
                    $_SESSION['exemplairesChefErreur'] = $_POST['exemplaireschef'];
                    ?>
                <script>
                    alert("Toutes les valeurs saisies doivent être des nombres.");
                    document.location.href="<?php echo $retour; ?>";
                </script>
                <?php
                }
            }
            else {
                showError("unknownOp");
            }
        }
        elseif(isset($_POST['idpart']) && $_POST['idpart']) {
            $retour = "partupdate.php?idpart=".$_POST['idpart'];
            $verifPart = $bdd->prepare('SELECT * FROM participation WHERE idpart = :idpart');
            $verifPart->execute(array("idpart" => $_POST['idpart']));
            if($partInfo = $verifPart->fetch()) {
                if((filter_var($_POST['exemplaireschef'], FILTER_VALIDATE_INT, $limiteNeg) !== false || !$_POST['exemplaireschef']) && (filter_var($_POST['exemplaires'], FILTER_VALIDATE_INT, $limiteNeg) !== false || !$_POST['exemplaires'])) {
                    if ($_POST['exemplaireschef'] <= $_POST['exemplaires']) {
                        $verifUser = $bdd->prepare('SELECT * FROM users WHERE iduser = :iduser AND active = 1 AND banned = 0');
                        $verifUser->execute(array("iduser" => $_SESSION['iduser']));
                        if ($infosUser = $verifUser->fetch()) {
                            if ($partInfo['iduser'] == $_SESSION['iduser'] || $infosUser['rank']) {
                                $editParticipation = $bdd->prepare('UPDATE participation SET exemplaires = :exemplaires, exemplaireschef = :exemplaireschef WHERE idpart = :idpart');
                                $editParticipation->execute(array(
                                    "exemplaires" => $_POST['exemplaires'],
                                    "exemplaireschef" => $_POST['exemplaireschef'],
                                    "idpart" => $_POST['idpart']
                                ));
                            ?>
                            <script>
                                alert("La participation a bien été mise à jour.");
                                document.location.href = "viewop.php?idop=<?php echo $partInfo['idop']; ?>";
                            </script>
                            <?php
                            }
                            else {
                                if ($infosUser['rank'] != $_SESSION['rank']) {
                                    showError("sessionError");
                                }
                                showError("unauthorizedAction");
                            }
                        } else {
                            showError("sessionError");
                        }
                    }
                    else {
                        ?>
                        <script>
                            alert("Le total de cartes chez le chef doit être inférieur au total de cartes de votre lot.");
                            document.location.href='<?php echo $retour;?>';
                        </script>
                        <?php
                    }
                }
                else {
                $_SESSION['exemplairesErreur'] = $_POST['exemplaires'];
                $_SESSION['exemplairesChefErreur'] = $_POST['exemplaireschef'];
                ?>
                <script>
                    alert("Toutes les valeurs saisies doivent être des nombres.");
                    document.location.href="<?php echo $retour; ?>";
                </script>
                <?php
                }
            }
            else {
                showError("unknownPart");
            }
        }
        else {
            showError("wtf");
        }
    }
    else {
        showError("wtf");
    }
} else {
    showError("unlogged");
}
?>