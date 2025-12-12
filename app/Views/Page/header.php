<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Réseau Social Trop Cool</title>
<!-- Bootstrap CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet" integrity="sha384-
QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

<!-- menu de navigation -->
<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" href='?c=home'>Accueil</a>
        </li>
        <?php if (isset($_SESSION['nom'])) { ?>
            <li class="nav-item">
                <a class="nav-link" href="?c=Recettes&a=ajouter">Nouveau Post</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="?c=User&a=profil">Profil de <?php echo $_SESSION['nom']?></a>
            </li>
        <?php } ?>
    </ul>

    <ul class="navbar-nav">
        <?php if (isset($_SESSION['nom'])) { ?>
            <li class="nav-item">
                <a class="btn btn-outline-dark" href="?c=User&a=deconnexion">Déconnexion</a>
            </li>
        <?php } else { ?>
            <li class="nav-item">
                <a class="btn btn-outline-dark" href="?c=User&a=inscription">Inscription</a>
            </li>
            <li class="nav-item">
                <a class="btn btn-outline-dark" href="?c=User&a=connexion">Connexion</a>
            </li>
        <?php } ?>
    </ul>
</nav>

<?php if(isset($_SESSION['message'])) : ?>
<?php foreach ($_SESSION['message'] as $type => $message) { ?>
    <div class="alert alert-<?php echo $type; ?>">
        <?php echo $message; ?>
    </div>
<?php } endif; unset($_SESSION['message']); ?>

<!-- corps de la page -->
<div class="container w-75 m-auto">