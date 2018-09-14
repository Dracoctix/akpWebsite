<?php
define("VERSION", "1.2.1");
define("DATABASE_PASSWORD", ""); // Mot de passe de la base de données.
define("DATABASE_USER", ""); // Utilisateur de la base de données
define("DATABASE_HOST", "localhost"); // Serveur de base de données
define("DATABASE_NAME", ""); // Nom de la base de données
try {
//    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
    $bdd = new PDO('mysql:host='.DATABASE_HOST.';dbname='.DATABASE_NAME.';charset=utf8', DATABASE_USER, DATABASE_PASSWORD);
}
catch (Exception $e) {
    die("Une erreur est survenue lors de la connexion à la base de données : " . $e->getMessage());
}
?>
