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
<!--        <link rel="stylesheet" href="../style/adm.css">-->
<!--        <link rel="stylesheet" href="../style/index.css">-->
<!--        <link rel="icon" href="../favicon.ico">-->
    </head>
<?php
if(isset($_SESSION['iduser'])) { // Vérifions que l'utilisateur est bien loggé
    $verifAdmin = $bdd->prepare('SELECT * FROM users WHERE iduser = :iduser');
    $verifAdmin->execute(array("iduser" => $_SESSION['iduser']));
    if($infosAdmin = $verifAdmin->fetch()) { // Ici, on s'assure que l'utilisateur existe bien, au cas-où
        if ($infosAdmin['rank']) { // On vérifie que l'utilisateur soit toujours admin
            $modif = false;
            $opInfos = NULL;
            if(isset($_POST['titre']) && isset($_POST['carte']) && isset($_POST['idcarte']) && isset($_POST['objectif'])
            && isset($_POST['phase']) && isset($_POST['statut']) && isset($_POST['prix']) && isset($_POST['description'])) {
                $_SESSION['titreErreur'] = $_POST['titre'];
                $_SESSION['carteErreur'] = $_POST['carte'];
                $_SESSION['idcarteErreur'] = $_POST['idcarte'];
                $_SESSION['objectifErreur'] = $_POST['objectif'];
                $_SESSION['phaseErreur'] = $_POST['phase'];
                $_SESSION['prixErreur'] = $_POST['prix'];
                $_SESSION['statutErreur'] = $_POST['statut'];
                $_SESSION['descErreur'] =$_POST['description'];

                if(isset($_POST['newcreateur'])) {
                    $_SESSION['remplacement'] = $_POST['newCreateur'];
                }
                else {
                    $_SESSION['remplacement'] = 0;
                }

                if(isset($_POST['idop']) && $_POST['idop'] != NULL) {
                    $retour = "opedit.php?idop=".$_POST['idop'];
                }
                else {
                    $retour = "opedit.php";
                }

                if($_POST['titre'] != NULL && $_POST['carte'] != NULL && $_POST['idcarte'] != NULL &&
                $_POST['objectif'] != NULL && $_POST['phase'] != NULL && $_POST['statut'] != NULL && $_POST['prix'] !=
                NULL) {
                    $description = htmlspecialchars($_POST['description']);
                    $titre = htmlspecialchars($_POST['titre']);
                    $carte = htmlspecialchars($_POST['carte']);
                    if(strlen($titre) >= 3 && strlen($titre) <= 255) {
                        if(strlen($carte) >= 2 && strlen($carte) <= 255) {
                            if(($_POST['statut'] >= 1 || $_POST['statut'] <= 3) && ($_POST['phase'] == 1 || $_POST['phase'] == 0)) {
                                $testPositif = array(
                                    'options' => array(
                                            'min_range' =>0
                                    )
                                );
                                if(filter_var($_POST['idcarte'], FILTER_VALIDATE_INT, $testPositif) !== false) {
                                    if(filter_var($_POST['objectif'], FILTER_VALIDATE_INT, $testPositif) !== false) {
                                        if(filter_var($_POST['prix'], FILTER_VALIDATE_INT, $testPositif) !== false) {
                                            // On a vérifié que l'utilisateur n'a pas rentré de conneries, on peut donc modifier.
                                            if (isset($_POST['idop']) && $_POST['idop']) {
                                                $recupOp = $bdd->prepare('SELECT * FROM op WHERE idop = :idop');
                                                $recupOp->execute(array("idop" => $_POST['idop']));
                                                if ($opInfos = $recupOp->fetch()) {
                                                    $idcreateur = $opInfos['idcreateur'];
                                                    $date = $opInfos['creationdate'];
                                                    if(isset($_POST['newCreateur'])) {
                                                        $idcreateur = $_SESSION['iduser'];
                                                    }
                                                    if($_POST['statut'] != $opInfos['status']) {
                                                        $date = date("Y-m-d H:i:s");
                                                    }
                                                    $modification = $bdd->prepare('UPDATE op SET titre = :titre,
                                                    carte = :carte, idcarte = :idcarte, objectif = :objectif, phase = :phase,
                                                    prix = :prix, status = :status, idcreateur = :idcreateur, creationdate = :creationdate, description = :description WHERE idop = :idop');
                                                    $modification->execute(array(
                                                        "titre" => $titre,
                                                        "carte" => $carte,
                                                        "idcarte" => $_POST['idcarte'],
                                                        "objectif" => $_POST['objectif'],
                                                        "phase" => $_POST['phase'],
                                                        "prix" => $_POST['prix'],
                                                        "status" => $_POST['statut'],
                                                        "idcreateur" => $idcreateur,
                                                        "creationdate" => $date,
                                                        "description" => $description,
                                                        "idop" => $_POST['idop']
                                                    ));
                                                    ?>
                                                    <script>
                                                        alert("L'opération <?php echo $opInfos['titre']; ?> a correctement été modifiée.")
                                                        document.location.href="op.php";
                                                    </script>
                                                    <?php
                                                } else {
                                                    showError("unknownOp", "op.php");
                                                }
                                            }
                                            else {
                                                $ajout = $bdd->prepare('INSERT INTO op (titre, carte, idcarte, objectif, phase, prix, status, description, idcreateur)
                                                VALUES (:titre, :carte, :idcarte, :objectif, :phase, :prix, :status, :description, :idcreateur)');
                                                $ajout->execute(array(
                                                    "titre" => $titre,
                                                    "carte" => $carte,
                                                    "idcarte" => $_POST['idcarte'],
                                                    "objectif" => $_POST['objectif'],
                                                    "phase" => $_POST['phase'],
                                                    "prix" => $_POST['prix'],
                                                    "status" => $_POST['statut'],
                                                    "description" => $description,
                                                    "idcreateur" => $_SESSION['iduser']
                                                ));
                                                    ?>
                                                    <script>
                                                        alert("L'opération a été correctement ajoutée.");
                                                        document.location.href="op.php"
                                                    </script>
                                                <?php
                                            }
                                            $_SESSION['titreErreur'] = $_SESSION['statutErreur'] =
                                            $_SESSION['carteErreur'] = $_SESSION['idcarteErreur'] =
                                            $_SESSION['remplacement'] = $_SESSION['phaseErreur'] =
                                            $_SESSION['objectifErreur'] = NULL;
                                        } else {
                                            ?>
                                            <script>
                                                alert("Le prix de la carte n'est pas un nombre.");
                                                document.location.href="<?php echo $retour; ?>";
                                            </script>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <script>
                                            alert("L'objectif de l'opération n'est pas un nombre.");
                                            document.location.href="<?php echo $retour; ?>";
                                            </script>
                                    <?php
                                    }
                                } else {
                                    ?>
                                    <script>
                                        alert("L'identifiant de la carte n'est pas un nombre.");
                                        document.location.href="<?php echo $retour; ?>";
                                    </script>
                                    <?php
                                }
                            } else {
                                ?>
                                <script>
                                    alert("Les données envoyées par le formulaire sont incorrectes.");
                                    document.location.href="<?php echo $retour; ?>";
                                </script>
                                <?php
                            }
                        }
                        else {
                            ?>
                            <script>
                                alert("Le nom du personnage doit comprendre entre 2 et 255 caractères.");
                                document.location.href="<?php echo $retour; ?>";
                            </script>
                            <?php
                        }
                    }
                    else {
                        ?>
                        <script>
                            alert("Le titre doit comprendre entre 3 et 255 caractères.");
                            document.location.href="<?php echo $retour; ?>";
                        </script>
                        <?php
                    }
                }
                else {
                    showError("saisieVide", $retour);
                }
            }
            else {
                showError("wtf", "../index.php");
            }
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