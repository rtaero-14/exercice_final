<?php

require_once __DIR__ . '/Database.php';

class Search {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function executeSearch($term) {
        $searchTerm = '%' . $term . '%';
        $results = [];

        $queryPosts = "SELECT p.id, p.titre, p.contenu, p.date_publication, u.nom as nom_utilisateur, 'post' as type 
                       FROM posts p
                       JOIN users u ON p.utilisateur_id = u.id
                       WHERE p.titre LIKE :term1 OR p.contenu LIKE :term2
                       ORDER BY p.date_publication DESC";
        
        $stmtPosts = $this->conn->prepare($queryPosts);
        $stmtPosts->bindValue(':term1', $searchTerm);
        $stmtPosts->bindValue(':term2', $searchTerm);
        $stmtPosts->execute();
        $results['posts'] = $stmtPosts->fetchAll(PDO::FETCH_ASSOC);

        $queryComments = "SELECT c.id, c.contenu, c.date_commentaire, p.titre as titre_post, u.nom as nom_utilisateur, 'comment' as type 
                          FROM comments c
                          JOIN users u ON c.utilisateur_id = u.id
                          JOIN posts p ON c.post_id = p.id
                          WHERE c.contenu LIKE :term3
                          ORDER BY c.date_commentaire DESC";
                          
        $stmtComments = $this->conn->prepare($queryComments);
        $stmtComments->bindValue(':term3', $searchTerm);
        $stmtComments->execute();
        $results['comments'] = $stmtComments->fetchAll(PDO::FETCH_ASSOC);
        
        $queryUsers = "SELECT id, nom, email, 'user' as type 
                       FROM users
                       WHERE nom LIKE :term4
                       ORDER BY nom ASC";
                       
        $stmtUsers = $this->conn->prepare($queryUsers);
        $stmtUsers->bindValue(':term4', $searchTerm);
        $stmtUsers->execute();
        $results['users'] = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

        return $results;
    }
}