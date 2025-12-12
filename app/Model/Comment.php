<?php

require_once __DIR__ . '/Database.php';

class Comment {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function add($contenu, $utilisateur_id, $post_id) {
        $query = "INSERT INTO comments (contenu, utilisateur_id, post_id, date_commentaire)
                  VALUES (:contenu, :utilisateur_id, :post_id, NOW())";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':contenu', $contenu);
        $stmt->bindParam(':utilisateur_id', $utilisateur_id);
        $stmt->bindParam(':post_id', $post_id);

        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    public function findByPostId($post_id) {
        $query = "SELECT c.*, u.nom as nom_utilisateur 
                  FROM comments c
                  JOIN users u ON c.utilisateur_id = u.id
                  WHERE c.post_id = :post_id
                  ORDER BY c.date_commentaire ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $query = "SELECT * FROM comments WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        $query = "DELETE FROM comments WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}