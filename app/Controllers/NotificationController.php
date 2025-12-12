<?php

require_once __DIR__ . '/../Model/Post.php';
require_once __DIR__ . '/../Model/Comment.php';
require_once __DIR__ . '/../Model/Reaction.php';

class NotificationController {

    private $postModel;
    private $commentModel;
    private $reactionModel;

    public function __construct() {
        $this->postModel = new Post();
        $this->commentModel = new Comment();
        $this->reactionModel = new Reaction();
    }

    public function checkNewActivities() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['id'])) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié.']);
            return;
        }

        $latestPost = $this->postModel->findAll()[0]['id'] ?? 0;
        
        echo json_encode([
            'success' => true,
            'latest_post_id' => $latestPost,
            'message' => 'Nouvelle activité vérifiée.',
        ]);
    }
}