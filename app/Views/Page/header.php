<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Réseau Social Trop Cool</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet" integrity="sha384-
QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
crossorigin="anonymous"></script>

<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
            <a class="nav-link" href='?c=home'>Accueil</a>
        </li>
        <?php if (isset($_SESSION['nom'])) { ?>
            <li class="nav-item">
                <a class="nav-link" href="?c=Post&a=creer">Nouveau Post</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="?c=User&a=profil">Profil de <?php echo $_SESSION['nom']?></a>
            </li>
        <?php } ?>
    </ul>

    <div class="d-flex me-3 position-relative">
        <input class="form-control me-2" type="search" placeholder="Recherche en direct" id="search-input" aria-label="Search">
        <div id="search-results-container" class="position-absolute bg-white border rounded shadow-lg p-2" style="z-index: 1000; top: 100%; width: 300px; max-height: 400px; overflow-y: auto; display: none;">
        </div>
    </div>
    
    <ul class="navbar-nav me-3">
        <?php if (isset($_SESSION['nom'])) { ?>
            <li class="nav-item">
                 <button class="btn btn-sm position-relative" id="notification-button">
                    <i class="bi bi-bell fs-5"></i>
                    <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle d-none" id="notification-badge">
                        0
                    </span>
                 </button>
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

<div class="container w-75 m-auto">