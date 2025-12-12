<?php

require_once __DIR__ . '/../Model/Comment.php';
require_once __DIR__ . '/../Model/Post.php';
require_once __DIR__ . '/../Model/User.php';

class CommentController {
    
    public function ajouter() {
        if (!isset($_SESSION['id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Non autorisé.']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $post_id = $_POST['post_id'] ?? null;
            $contenu = trim($_POST['contenu'] ?? '');
            $utilisateur_id = $_SESSION['id'];

            if ($post_id && $contenu) {
                $commentModel = new Comment();
                $result = $commentModel->create($post_id, $utilisateur_id, $contenu);

                if ($result) {
                    $lastId = $commentModel->getLastInsertId(); 
                    $newComment = $commentModel->find($lastId); 
                    
                    $userModel = new User();
                    $user = $userModel->find($utilisateur_id);

                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Commentaire ajouté.', 
                        'comment' => [
                            'id' => $newComment['id'],
                            'contenu' => $newComment['contenu'],
                            'date_commentaire' => $newComment['date_commentaire'],
                            'nom_utilisateur' => $user['nom']
                        ]
                    ]);
                    exit;
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Erreur SQL lors de l\'ajout.']);
                    exit;
                }
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Données manquantes.']);
                exit;
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Méthode non supportée.']);
        exit;
    }

    public function supprimer($id) {
        if (!isset($_SESSION['id'])) {
            $_SESSION['message'] = ['danger' => 'Veuillez vous connecter.'];
            header('Location: ?c=User&a=connexion');
            exit;
        }

        if (!$id) {
            $_SESSION['message'] = ['danger' => 'ID du commentaire manquant.'];
            header('Location: ?c=home');
            exit;
        }

        $commentModel = new Comment();
        $comment = $commentModel->find($id);

        if (!$comment) {
            $_SESSION['message'] = ['danger' => 'Commentaire non trouvé.'];
            header('Location: ?c=home');
            exit;
        }

        if ($comment['utilisateur_id'] != $_SESSION['id']) {
            $_SESSION['message'] = ['danger' => 'Vous n\'êtes pas autorisé à supprimer ce commentaire.'];
            header('Location: ?c=home');
            exit;
        }

        if ($commentModel->delete($id)) {
            $_SESSION['message'] = ['success' => 'Commentaire supprimé avec succès.'];
        } else {
            $_SESSION['message'] = ['danger' => 'Erreur lors de la suppression du commentaire.'];
        }

        header('Location: ?c=home');
        exit;
    }
}