<?php
session_start();
session_destroy();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Déconnexion</title>
    <meta name="robots" content="noindex,nofollow">
    <script>
        alert("Vous êtes bien déconnecté. Vous allez être redirigé.");
        document.location.href="index.php";
    </script>
</head>
</html>