<?php

require_once __DIR__ . '/Database.php';

class Comment {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create($post_id, $utilisateur_id, $contenu) {
        $sql = "INSERT INTO comments (post_id, utilisateur_id, contenu) VALUES (:post_id, :utilisateur_id, :contenu)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->bindParam(':utilisateur_id', $utilisateur_id);
        $stmt->bindParam(':contenu', $contenu);
        return $stmt->execute();
    }
    
    public function getLastInsertId() {
        return $this->conn->lastInsertId();
    }

    public function findByPostId($post_id) {
        $sql = "SELECT c.*, u.nom as nom_utilisateur FROM comments c JOIN users u ON c.utilisateur_id = u.id WHERE c.post_id = :post_id ORDER BY c.date_commentaire ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function find($id) {
        $sql = "SELECT * FROM comments WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        $sql = "DELETE FROM comments WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}