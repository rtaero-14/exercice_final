<?php

require_once __DIR__ . '/../Model/Post.php';

class PostController {

    private $userModel;

    public function __construct() {
        $this->userModel = new Post();
    }

    public function lister(){
        $posts = $this->userModel->findAll();
        require_once __DIR__ . '/../Views/Post/lister.php';
    }

    public function creer_post() {
        require_once __DIR__ . '/../Views/Post/creer_post.php';
    }

    public function modifier() {
        if (!isset($_SESSION['id'])) {
            echo "Vous devez être connecté.";
            return;
        }

        $post = $this->userModel->find($_GET['id']);
        require_once __DIR__ . '/../Views/Post/modifier.php';        
    }

    public function supprimer() {
        if (!isset($_SESSION['id'])) {
            echo "Vous devez être connecté.";
            return;
        }

        $this->userModel->delete($_GET['id']);
        header('Location: ?c=post&action=lister');        
    }
}