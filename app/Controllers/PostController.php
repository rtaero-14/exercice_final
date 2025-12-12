<?php

require_once __DIR__ . '/../Model/Post.php';

class PostController {

    private $postModel; // Renommé en postModel pour plus de clarté

    public function __construct() {
        $this->postModel = new Post();
    }

    public function lister(){
        $posts = $this->postModel->findAll();
        require_once __DIR__ . '/../Views/Post/lister.php';
    }

    // CORRECTION 4: Ajout de la méthode creer() pour afficher le formulaire
    public function creer() {
        if (!isset($_SESSION['id'])) {
            $_SESSION['message']['danger'] = "Vous devez être connecté pour créer un post.";
            header('Location: ?c=User&a=connexion');
            return;
        }
        require_once __DIR__ . '/../Views/Post/creer_post.php';
    }

    // CORRECTION 4: Ajout de la méthode enregistrer() pour traiter le formulaire
    public function enregistrer() {
        if (!isset($_SESSION['id'])) {
            $_SESSION['message']['danger'] = "Vous devez être connecté pour enregistrer un post.";
            header('Location: ?c=User&a=connexion');
            return;
        }

        $titre = $_POST['titre'] ?? '';
        $contenu = $_POST['contenu'] ?? '';
        $utilisateur_id = $_SESSION['id'];

        if (empty($titre) || empty($contenu)) {
             $_SESSION['message']['danger'] = "Le titre et le contenu sont obligatoires.";
             header('Location: ?c=Post&a=creer');
             return;
        }

        $this->postModel->add($titre, $contenu, $utilisateur_id);
        $_SESSION['message']['success'] = "Le post a été publié avec succès.";
        header('Location: ?c=Post&a=lister');
        return;
    }

    public function modifier($id) { 
        if (!isset($_SESSION['id'])) {
            echo "Vous devez être connecté.";
            return;
        }

        $post = $this->postModel->find($id);
        if ($post['utilisateur_id'] != $_SESSION['id']) {
            $_SESSION['message']['danger'] = "Vous ne pouvez pas modifier le post d'un autre utilisateur.";
            header('Location: ?c=Post&a=lister');
            return;
        }

        require_once __DIR__ . '/../Views/Post/modifier.php';        
    }

    public function supprimer($id) {
        if (!isset($_SESSION['id'])) {
            echo "Vous devez être connecté.";
            return;
        }
        
        $post = $this->postModel->find($id);
        if ($post['utilisateur_id'] != $_SESSION['id']) {
            $_SESSION['message']['danger'] = "Vous ne pouvez pas supprimer le post d'un autre utilisateur.";
            header('Location: ?c=Post&a=lister');
            return;
        }

        $this->postModel->delete($id);
        $_SESSION['message']['success'] = "Le post a été supprimé avec succès.";
        header('Location: ?c=Post&a=lister');        
    }
}