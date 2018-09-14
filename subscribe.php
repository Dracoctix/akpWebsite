<?php
session_start();
include('includes/errors.php');
include('includes/database.php');
if(!isset($_SESSION['iduser']) || $_SESSION['iduser'] == NULL) {
    ?>
    <!doctype html>
    <html>
    <head>
        <title>Inscription sur le site</title>
        <meta charset="utf-8">
        <meta name="robots" content="noindex,nofollow"/>
        <link rel="stylesheet" href="style/index.css">
        <link rel="icon" href="favicon.ico">
        <?php
        include('includes/bootstrapcss.html');
        ?>
    </head>
    <body>
    <div class="container">
        <header>
            <div class="row">
                <h1 class="col-lg-12 page-header">Inscription sur l'AKP Business Team</h1>
            </div>
            <div class="row">
                <div class="col-lg-12 alert alert-info">
                    Pour des raisons de sécurité, il est nécessaire d'avoir un compte sur le site, pour consulter les
                    informations qui y sont présentes. En outre, il devra être validé par un administrateur, pour éviter toute
                    usurpation.
                </div>
            </div>
        </header>
        <div class="row">
            <form method="post" action="exesubscribe.php" class="col-lg-12" novalidate>
                <div class="form-group">
                    <label for="username">Nom d'utilisateur :</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text fas fa-user"></span>
                        </div>
                        <input class="form-control" type="text" name="username" id="username" placeholder="Nom d'utilisateur" maxlength="255"
                        value="<?php if(isset($_SESSION['errorUsername'])) { echo $_SESSION['errorUsername']; } ?>" autofocus>
                        <small class="form-text text-muted droite col-lg-12" id="usernameText">Votre nom d'utilisateur doit comporter 3 à 255 caractères.</small>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">Adresse mail :</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text fas fa-at"></span>
                        </div>
                        <input class="form-control" type="email" name="email" id="email" placeholder="adresse@domaine.tld" maxlength="255"
                        value="<?php if(isset($_SESSION['errorEmail'])) { echo $_SESSION['errorEmail']; } ?>">
                        <small class="form-text text-muted droite col-lg-12" id="emailText">Votre adresse mail doit être valide.</small>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-lg-6 col-md-6">
                        <label for="password">Mot de passe :</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text fas fa-key"></span>
                            </div>
                            <input type="password" name="password" id="password" placeholder="Mot de passe" maxlength="255" class="form-control">
                            <small class="col-lg-12 form-text text-muted" id="passwordText">Votre mot de passe doit comporter moins de 255 caractères.</small>
                        </div>
                    </div>
                    <div class="form-group col-lg-6 col-md-6">
                        <label for="confirmation">Mot de passe (confirmation) :</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text fas fa-key"></span>
                            </div>
                            <input type="password" name="confirmation" id="confirmation" placeholder="Confirmation" maxlength="255" class="form-control">
                            <small id="confirmationText" class="form-text text-muted droite col-lg-12">Les deux mots de passe doivent correspondre.</small>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="description">Description :</label><textarea class="form-control" name="description" id="description"
                       placeholder="Quelques informations sur vous ?" rows="12"><?php if(isset($_SESSION['errorDesc'])){ echo $_SESSION['errorDesc']; }?></textarea>
                    <small class="form-text text-muted droite">La description est facultative.</small>
                </div>
                <div class="row form-group justify-content-center d-flex">
                    <div class="col-lg-6 col-md-12">
                        <div class="row justify-content-center d-flex">
                            <div class="btn-group col-lg-12">
                                <button type="submit" class="col-lg-6 col-md-12 btn btn-success"><span class="fas fa-user-plus"></span> S'inscrire</button><button type="reset" class="col-lg-6 btn btn-danger d-none d-lg-block"><span class="fas fa-undo"></span> Réinitialiser</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row form-group justify-content-center d-flex">
                    <div class="col-lg-6 col-md-12 text-center">
                        <a href="index.php" class="btn btn-warning col-lg-9 col-md-12"><span class="fas fa-sign-out-alt"></span> Retour à la page d'accueil</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php
    include('includes/scripts.html');
    ?>
    <script src="scripts/subformvalid.js">
    </script>
    </body>
    </html>
    <?php
    $_SESSION['errorUsername'] = $_SESSION['errorEmail'] = $_SESSION['errorDesc'] = NULL;
}
else {
    ?>
    <script>
        document.location.href="index.php";
    </script>
    <?php
}
?>