<?php

session_start();

require_once __DIR__ . '/../app/Controllers/UserController.php';
require_once __DIR__ . '/../app/Controllers/PostController.php';
require_once __DIR__ . '/../app/Controllers/CommentController.php';
require_once __DIR__ . '/../app/Model/Post.php';
require_once __DIR__ . '/../app/Model/User.php';

if(!isset($_GET['x']))
require_once __DIR__ . '/../app/Views/Page/header.php';

if(!isset($_GET['c'])){
    $_GET['c'] = 'home';
}

$controller = isset($_GET['c']) ? $_GET['c'] : 'home';
$action = isset($_GET['a']) ? $_GET['a'] : 'index';
$id = isset($_GET['id']) ? $_GET['id'] : NULL;

switch ($controller) {
    case 'home':
        $postModel = new Post();
        $userModel = new User();
        $posts = $postModel->findAll();

        foreach ($posts as $key => $post) {
            $user = $userModel->find($post['utilisateur_id']);
            $posts[$key]['nom_utilisateur'] = $user['nom'] ?? 'Utilisateur Inconnu';
        }
        
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
        
    case 'Post':
        $postController = new PostController();
        switch ($action) {
            case 'lister':
            case 'index':
                $postController->lister();
                break;
            case 'creer':
                $postController->creer();
                break;
            case 'enregistrer':
                $postController->enregistrer();
                break;
            case 'modifier':
                $postController->modifier($id);
                break;
            case 'update':
                $postController->update();
                break;
            case 'supprimer':
                $postController->supprimer($id);
                break;
            default:
                $postController->lister();
                break;
        }
        break;
    
    case 'Commentaires':
        $commentaireController = new CommentController();
        switch ($action) {
            case 'ajouter':
                $commentaireController->ajouter();
                break;

            case 'supprimer':
                $commentaireController->supprimer($id);
                break;
        }
        break;
    
    default:
        echo "Page non trouvée";
        break;
}

if(!isset($_GET['x']))
require_once __DIR__ . '/../app/Views/Page/footer.php';