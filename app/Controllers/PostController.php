<?php

require_once __DIR__ . '/../Model/Post.php';
require_once __DIR__ . '/../Model/User.php';
require_once __DIR__ . '/../Model/Comment.php';

class PostController {

    private $postModel;
    private $userModel;
    private $commentModel;
    
    public function __construct() {
        $this->postModel = new Post();
        $this->userModel = new User();
        $this->commentModel = new Comment();
    }

    public function lister(){
        $posts = $this->postModel->findAll();
        
        foreach ($posts as $key => $post) {
            $user = $this->userModel->find($post['utilisateur_id']);
            $posts[$key]['nom_utilisateur'] = $user['nom'] ?? 'Utilisateur Inconnu';
            
            $posts[$key]['commentaires'] = $this->commentModel->findByPostId($post['id']);
        }

        require_once __DIR__ . '/../Views/Post/lister.php';
    }

    public function creer() {
        if (!isset($_SESSION['id'])) {
            $_SESSION['message']['danger'] = "Vous devez être connecté pour créer un post.";
            header('Location: ?c=User&a=connexion');
            return;
        }
        require_once __DIR__ . '/../Views/Post/creer_post.php';
    }

    public function enregistrer() {
        if (!isset($_SESSION['id'])) {
            $_SESSION['message']['danger'] = "Vous devez être connecté pour enregistrer un post.";
            header('Location: ?c=User&a=connexion');
            return;
        }

        $titre = trim($_POST['titre'] ?? '');
        $contenu = trim($_POST['contenu'] ?? '');
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
            $_SESSION['message']['danger'] = "Vous devez être connecté pour modifier un post.";
            header('Location: ?c=User&a=connexion');
            return;
        }

        $post = $this->postModel->find($id);
        if (!$post || $post['utilisateur_id'] != $_SESSION['id']) {
            $_SESSION['message']['danger'] = "Accès refusé ou post non trouvé.";
            header('Location: ?c=Post&a=lister');
            return;
        }

        require_once __DIR__ . '/../Views/Post/modifier.php';        
    }
    
    public function update() {
        if (!isset($_SESSION['id'])) {
            $_SESSION['message']['danger'] = "Vous devez être connecté pour modifier un post.";
            header('Location: ?c=User&a=connexion');
            return;
        }
        
        $id = $_POST['id'] ?? null;
        $titre = trim($_POST['titre'] ?? '');
        $contenu = trim($_POST['contenu'] ?? '');
        $utilisateur_id = $_SESSION['id'];

        if (empty($id)) {
             $_SESSION['message']['danger'] = "Erreur de soumission : ID de post manquant.";
             header('Location: ?c=Post&a=lister');
             return;
        }

        if (empty($titre) || empty($contenu)) {
             $_SESSION['message']['danger'] = "Tous les champs sont obligatoires.";
             header('Location: ?c=Post&a=modifier&id=' . $id);
             return;
        }
        
        $post = $this->postModel->find($id);
        if (!$post || $post['utilisateur_id'] != $utilisateur_id) {
            $_SESSION['message']['danger'] = "Tentative de modification non autorisée.";
            header('Location: ?c=Post&a=lister');
            return;
        }

        if ($this->postModel->update($id, $titre, $contenu, $utilisateur_id)) {
            $_SESSION['message']['success'] = "Le post a été modifié avec succès.";
        } else {
            $_SESSION['message']['danger'] = "Erreur lors de la modification du post.";
        }
        
        header('Location: ?c=Post&a=lister');
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