<?php

require_once __DIR__ . '/../Model/Comment.php';
require_once __DIR__ . '/../Model/Post.php';

class CommentController {

    private $commentModel;
    private $postModel;
    
    public function __construct() {
        $this->commentModel = new Comment();
        $this->postModel = new Post(); 
    }

    public function ajouter() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['id'])) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié.']);
            return;
        }

        $contenu = trim($_POST['contenu'] ?? '');
        $post_id = $_POST['post_id'] ?? null;
        $utilisateur_id = $_SESSION['id'];

        if (empty($contenu) || empty($post_id)) {
            echo json_encode(['success' => false, 'message' => 'Le contenu du commentaire et l\'ID du post sont requis.']);
            return;
        }

        if (!$this->postModel->find($post_id)) {
            echo json_encode(['success' => false, 'message' => 'Post cible non trouvé.']);
            return;
        }
        
        $new_id = $this->commentModel->add($contenu, $utilisateur_id, $post_id);

        if ($new_id) {
            $newComment = $this->commentModel->find($new_id); 
            $newComment['nom_utilisateur'] = $_SESSION['nom']; 

            echo json_encode(['success' => true, 'comment' => $newComment]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Échec de l\'ajout du commentaire.']);
        }
    }

    public function supprimer($id) {
        if (!isset($_SESSION['id'])) {
            $_SESSION['message']['danger'] = "Vous devez être connecté pour supprimer un commentaire.";
            header('Location: ?c=User&a=connexion');
            return;
        }

        $comment = $this->commentModel->find($id);

        if (!$comment) {
            $_SESSION['message']['danger'] = "Commentaire non trouvé.";
            header('Location: ?c=Post&a=lister');
            return;
        }

        if ($comment['utilisateur_id'] != $_SESSION['id']) {
            $_SESSION['message']['danger'] = "Vous ne pouvez pas supprimer le commentaire d'un autre utilisateur.";
            header('Location: ?c=Post&a=lister');
            return;
        }

        if ($this->commentModel->delete($id)) {
            $_SESSION['message']['success'] = "Le commentaire a été supprimé avec succès.";
        } else {
            $_SESSION['message']['danger'] = "Erreur lors de la suppression du commentaire.";
        }
        
        header('Location: ?c=home');
    }
}