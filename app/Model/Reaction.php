<?php

require_once __DIR__ . '/Database.php';

class Reaction {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function countByPostId($post_id) {
        $query = "SELECT COUNT(*) FROM reactions WHERE post_id = :post_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    public function hasReacted($utilisateur_id, $post_id) {
        $query = "SELECT COUNT(*) FROM reactions WHERE utilisateur_id = :utilisateur_id AND post_id = :post_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':utilisateur_id', $utilisateur_id, PDO::PARAM_INT);
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function add($utilisateur_id, $post_id) {
        $query = "INSERT INTO reactions (utilisateur_id, post_id, date_reaction)
                  VALUES (:utilisateur_id, :post_id, NOW())";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':utilisateur_id', $utilisateur_id, PDO::PARAM_INT);
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function delete($utilisateur_id, $post_id) {
        $query = "DELETE FROM reactions WHERE utilisateur_id = :utilisateur_id AND post_id = :post_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':utilisateur_id', $utilisateur_id, PDO::PARAM_INT);
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}