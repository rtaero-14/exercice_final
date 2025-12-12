<?php

session_start();

require_once __DIR__ . '/../app/Controllers/UserController.php';
require_once __DIR__ . '/../app/Controllers/PostController.php';
require_once __DIR__ . '/../app/Controllers/CommentController.php';

if(!isset($_GET['x']))
require_once __DIR__ . '/../app/Views/Page/header.php';

if(!isset($_GET['c'])){
    $_GET['c'] = 'home';
}

// mise en place de la route actuelle
$controller = isset($_GET['c']) ? $_GET['c'] : 'home';
$action = isset($_GET['a']) ? $_GET['a'] : 'index';
$id = isset($_GET['id']) ? $_GET['id'] : NULL;

// définition des routes disponibles
switch ($controller) {
    case 'home':
        require_once __DIR__ . '/../app/Views/Page/home.php';
        break;
    
    case 'User':
        $userController = new UserController();
        switch ($action) {
            case 'inscription':
                $userController->inscription();
                break;

            case 'enregistrer':
                $userController->enregistrer();
                break;

            case 'connexion':
                $userController->connexion();
                break;

            case 'connecter':
                $userController->verifieConnexion();
                break;

            case 'deconnexion':
                $userController->deconnexion();
                break;

            case 'profil':
                $userController->profil();
                break;
        }
        break;
    
        // case 'Commentaires':
        //     $commentaireController = new CommentaireController();
        //     switch ($action) {
        //         case 'ajouter':
        //             $commentaireController->ajouter($id);
        //             break;

        //         case 'supprimer':
        //             $commentaireController->supprimer($id);
        //             break;

        //         case 'listerTous':
        //             $commentaireController->listerCommentaires();
        //             break;
        //     }
        //     break;
    
    default:
        echo "Page non trouvée";
        break;
}

if(!isset($_GET['x']))
require_once __DIR__ . '/../app/Views/Page/footer.php';