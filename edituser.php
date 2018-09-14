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
    <title>Éditer votre profil</title>
    <?php
    include('includes/bootstrapcss.html');
    ?>
</head>
<body>
<div class="container">
<?php
if(isset($_SESSION['username']) && isset($_SESSION['iduser']) && isset($_SESSION['email'])) {
    $notIndex = true;
    $editAccount = true;
    ?>
    <div class="row d-flex justify-content-center">
        <header class="center page-header col-lg-12">
            <h1>Modifier votre profil</h1>
        </header>
    </div>
    <?php
    include('navbar.php');
    $reqUser = $bdd->prepare('SELECT * FROM users WHERE iduser = :iduser');
    $reqUser->execute(array("iduser" => $_SESSION['iduser']));
    if($userInfo = $reqUser->fetch()) {
        $desc = $userInfo['description'];
        ?>
    <div class="row">
        <form action="exedit.php" method="post" class="col-lg-12 mt-4" novalidate>
            <div class="form-group">
                <label for="username">Nom d'utilisateur :</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text fas fa-user"></span>
                    </div>
                    <input class="form-control" type="text" name="username" id="username" maxlength="255" placeholder="Nom d'utilisateur" value="<?php echo $_SESSION['username']; ?>" autofocus>
                    <small class="form-text text-muted droite col-lg-12" id="usernameText">Votre nom d'utilisateur doit comporter 3 à 255 caractères.</small>
                </div>
            </div>
            <div class="form-group">
                <label for="email">Adresse mail :</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text fas fa-at"></span>
                    </div>
                    <input type="email" name="email" id="email" class="form-control" maxlength="255" placeholder="Adresse mail" value="<?php echo $_SESSION['email']; ?>">
                    <small class="form-text text-muted droite col-lg-12" id="emailText">Votre adresse mail doit être valide.</small>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="newpassword">Nouveau mot de passe (seulement si vous voulez modifier) :</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text fas fa-key"></span>
                        </div>
                        <input type="password" name="newpassword" class="form-control" id="newpassword" maxlength="255" placeholder="Nouveau mot de passe">
                        <small class="col-lg-12 form-text text-muted" id="newPasswordText">Votre mot de passe doit comporter moins de 255 caractères.</small>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label for="confirmation">Confirmation du nouveau mot de passe : </label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text fas fa-key"></span>
                        </div>
                        <input type="password" name="confirmation" class="form-control" id="confirmation" maxlength="255" placeholder="Confirmation du nouveau mot de passe">
                        <small id="confirmationText" class="form-text text-muted droite col-lg-12">Les deux mots de passe doivent correspondre.</small>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="description">Description de l'utilisateur :</label>
                <div class="input-group">
                    <textarea name="description" id="description" class="form-control" placeholder="Quelques informations sur vous ?" rows="12"><?php echo $desc; ?></textarea>
                    <small class="col-lg-12 form-text text-muted droite">La description est facultative.</small>
                </div>
            </div>
            <div class="form-group alert alert-secondary">
                <label for="password">Mot de passe actuel (nécessaire pour modifier) :</label>
                <div class="input-group">
                    <div class="input-group-prepend"><span class="fas fa-key input-group-text"></span></div>
                    <input type="password" name="password" id="password" class="form-control" maxlength="255" placeholder="Mot de passe actuel">
                    <small class="col-lg-12 form-text text-muted droite" id="passwordText">Vous devez spécifier un mot de passe pour éviter l'usurpation de compte.</small>
                </div>
            </div>
            <div class="alert alert-info">
                Vous pouvez modifier votre avatar avec le service <a href="https://www.gravatar.com">Gravatar</a>.<br>
            </div>
            <div class="row form-group justify-content-center d-flex">
                <div class="col-lg-6 col-md-12">
                    <div class="row justify-content-center d-flex">
                        <div class="btn-group col-lg-12">
                            <button type="submit" class="col-lg-6 col-md-12 btn btn-success"><span class="fas fa-edit"></span> Modifier mon profil</button><button type="reset" class="col-lg-6 btn btn-danger d-none d-lg-block"><span class="fas fa-undo"></span> Réinitialiser</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
        <?php
        include('includes/scripts.html');
        ?>
<script src="scripts/editUserVerif.js"></script>
<?php
        $_SESSION['descErreur'] = NULL;
    }
    else {
        showError("sessionError");
    }
}
else {
    showError("unlogged");
}
?>
</body>
</html>
