<?php
/**
 * @param string $errorCode Code d'erreur lié à l'erreur survenue.
 * @param string $redirect Page vers laquelle l'utilisateur doit être redirigé après l'affichage de l'erreur (facultatif).
 */
function showError($errorCode, $redirect = 'index.php') {
    switch ($errorCode) {
        case "wtf":
            ?>
            <script>
                alert("Vous n'êtes pas censé aboutir sur cette page de cette façon.");
                document.location.href="<?php echo $redirect; ?>";
            </script>
            <?php
            break;
        case "saisieVide":
            ?>
            <script>
                alert("Merci de remplir tous les champs du formulaire.");
                document.location.href="<?php echo $redirect; ?>";
            </script>
            <?php
            break;
        case "loginInvalide":
            ?>
            <script>
                alert("Vos identifiants sont invalides.");
                document.location.href="<?php echo $redirect; ?>";
            </script>
            <?php
            break;
        case "inactive":
            ?>
            <script>
                alert("Ce compte n'est pas activé. Veuillez attendre le passage d'un administrateur.");
                document.location.href="<?php echo $redirect; ?>";
            </script>
            <?php
            break;
        case "unlogged":
            ?>
            <script>
                alert("Vous devez être connecté pour effectuer cette action.");
                document.location.href="<?php echo $redirect; ?>";
            </script>
            <?php
            break;
        case "notAdmin":
            ?>
            <script>
                alert("Vous devez être administrateur pour effectuer cette action.");
                document.location.href="<?php echo $redirect; ?>";
            </script>
            <?php
            $_SESSION['rank'] = 0;
            break;
        case "noIdForUser":
            ?>
            <script>
                alert("Aucun identifiant n'a été spécifié pour effectuer cette action.");
                document.location.href="<?php echo $redirect; ?>";
            </script>
            <?php
            break;
        case "unknownUser":
            ?>
            <script>
                alert("L'utilisateur n'existe pas.");
                document.location.href="<?php echo $redirect; ?>";
            </script>
            <?php
            break;
        case "sessionError":
            session_destroy();
            ?>
            <script>
                alert("Une erreur de session est survenue. Merci de vous reconnecter.");
                document.location.href="<?php echo $redirect; ?>";
            </script>
            <?php
            break;
        case "unknownOp":
            ?>
            <script>
                alert("L'opération demandée est iconnue.");
                document.location.href="<?php echo $redirect; ?>";
            </script>
            <?php
            break;
        case "unknownPart":
            ?>
            <script>
                alert("La participation n'existe pas.");
                document.location.href="<?php echo $redirect; ?>";
            </script>
            <?php
            break;
        case "unknownComment":
            ?>
            <script>
                alert("Le commentaire n'existe pas.");
                document.location.href="<?php echo $redirect; ?>";
            </script>
            <?php
            break;
        case "unknownLot":
            ?>
            <script>
                alert("Le lot n'existe pas.");
                document.location.href="<?php echo $redirect; ?>";
            </script>
            <?php
            break;
        case "unauthorizedAction":
            ?>
            <script>
                alert("Vous n'avez pas le droit d'effectuer cette action.");
                document.location.href="<?php echo $redirect; ?>";
            </script>
            <?php
            break;
        default:
            echo "<strong>Une erreur s'est produite, mais il est impossible de savoir ce dont il s'agit.";
            break;
    }
}
?>