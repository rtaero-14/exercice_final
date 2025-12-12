<?php

require_once __DIR__ . '/../Model/Reaction.php';

class ReactionController {

    public function toggleReaction() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['id'])) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié.']);
            return;
        }

        $post_id = $_POST['post_id'] ?? null;
        $utilisateur_id = $_SESSION['id'];

        if (empty($post_id)) {
            echo json_encode(['success' => false, 'message' => 'ID de post manquant.']);
            return;
        }

        $reactionModel = new Reaction();

        if ($reactionModel->hasReacted($utilisateur_id, $post_id)) {
            $success = $reactionModel->delete($utilisateur_id, $post_id);
            $action = 'unliked';
        } else {
            $success = $reactionModel->add($utilisateur_id, $post_id);
            $action = 'liked';
        }

        $new_count = $reactionModel->countByPostId($post_id);

        if ($success) {
            echo json_encode([
                'success' => true,
                'action' => $action,
                'new_count' => $new_count
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Échec de la bascule de réaction.']);
        }
    }
}