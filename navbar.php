<nav class="navbar navbar-dark d-print navbar-expand-md sticky-top col-lg-12" style="background-color: rebeccapurple;">
    <div class=" navbar-header d-block d-md-none">
        <button class="navbar-toggler navbar-toggler-right" data-toggle="collapse" data-target="#navigation">Afficher le menu <span class="navbar-toggler-icon"></span></button>
    </div>
    <div class="collapse navbar-collapse" id="navigation">
        <ul class="navbar-nav mr-auto">
        <?php
        $index = (isset($index)) ? $index : false;
        $editAccount = (isset($editAccount)) ? $editAccount : false;
        $profil = (isset($profil)) ? $profil : false;
        if(isset($_SESSION['iduser']) && $_SESSION['iduser']) {
            ?>
            <li class="nav-item<?php if($index) { echo ' active'; }?>"><a href="index.php" class="nav-link"><span class="fas fa-home"></span> Accueil</a></li>
            <li class="nav-item<?php if($profil) { echo ' active'; }?>"><a class="nav-link" href="viewuser.php?iduser=<?php echo $_SESSION['iduser'];?>"><span class="fas fa-eye"></span> Voir votre profil</a></li>
            <?php
            if(isset($_SESSION['rank']) && $_SESSION['rank']) {
                $verifPending = $bdd->query('SELECT COUNT(iduser) AS nbPending FROM users WHERE active = 0');
                $pendingUsers = $verifPending->fetch()['nbPending'];
                ?>
                <li class="nav-item"><a href="adm/index.php" class="nav-link"><span class="fas fa-key"></span> Panneau d'administration <?php
                    if($pendingUsers) {
                        echo "<span class='badge badge-danger'>".$pendingUsers."</span>";
                    }
                        ?></a></li>
                <?php
            }
        }
        ?>
        </ul>
        <ul class="navbar-nav">
            <li class="nav-item<?php if($editAccount) { echo ' active'; }?>"><a href="edituser.php" class="nav-link"><span class="fas fa-edit"></span> Éditer votre compte</a></li>
            <li class="nav-item"><a href="logout.php" class="nav-link"><span class="fas fa-sign-out-alt"></span> Déconnexion</a></li>
        </ul>
    </div>
</nav>