<?php
$page = (isset($page)) ? $page : "users";
// DEBUG
//include('../includes/database.php');
?>
<div class="col-lg-12">
    <nav class="navbar navbar-dark d-print navbar-expand-md sticky-top" style="background-color: rebeccapurple;">
        <div class="navbar-header d-block d-md-none">
            <button class="navbar-toggler navbar-toggler-right" data-toggle="collapse" data-target="#navigation">Panneau d'administration <span class="navbar-toggler-icon"></span></button>
        </div>
        <div class="navbar-header d-none d-md-block">
            <a class="navbar-brand" href="index.php">
                Panneau d'administration
            </a>
        </div>
        <div class="collapse navbar-collapse" id="navigation">
            <ul class="navbar-nav mr-auto">
                <?php
                $verifPending = $bdd->query('SELECT COUNT(iduser) AS nbPending FROM users WHERE active = 0');
                $pendingUsers = $verifPending->fetch()['nbPending'];
                ?>
                <li class="nav-item<?php if($page == 'users' || $page == 'delete' || $page == 'promote') { echo ' active'; }?>"><a href="index.php" class="nav-link"><span class="fas fa-user"></span> Utilisateurs
                    <?php
                    if($pendingUsers) {
                        echo "<span class='badge badge-danger'>".$pendingUsers."</span>";
                    }
                    ?>
                    </a></li>
                <li class="nav-item<?php if($page == 'op' || $page == 'addop' || $page == 'delop' || $page == 'editop') { echo ' active'; }?> dropdown">
                    <a href="#" class="nav-link dropdown-toggle" id="dropdownOpLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="fas fa-credit-card"></span> Opérations
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownOpLink">
                        <a class="dropdown-item<?php if($page == 'op') { echo ' active'; }?>" href="op.php"><span class="fas fa-list"></span> Liste des opérations</a>
                        <a class="dropdown-item<?php if($page == 'addop') { echo ' active'; }?>" href="opedit.php"><span class="fas fa-plus"></span> Ajouter une opération</a>
                    </div>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item"><a data-toggle="modal" data-target="#about" href="#" class="nav-link"><span class="fas fa-info-circle"></span> À propos</a></li>
                <li class="nav-item"><a href="../index.php" class="nav-link"><span class="fas fa-home"></span> Retour au site</a></li>
            </ul>
        </div>
    </nav>
</div>

<!-- BOÎTE DE DIALOGUE À PROPOS -->
<div class="modal fade" id="about" tabindex="-1" role="dialog" aria-labelledby="titreAbout" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titreAbout"><span class="fas fa-info-circle"></span> À propos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php include('about.php'); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>