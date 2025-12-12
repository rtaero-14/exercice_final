<?php

require_once __DIR__ . '/../Model/Search.php';

class SearchController {
    private $searchModel;

    public function __construct() {
        $this->searchModel = new Search();
    }

    public function ajaxSearch() {
        header('Content-Type: application/json');

        $term = trim($_GET['q'] ?? '');

        if (empty($term)) {
            echo json_encode(['results' => [], 'message' => 'Veuillez entrer un terme de recherche.']);
            return;
        }

        $results = $this->searchModel->executeSearch($term);

        echo json_encode(['results' => $results, 'term' => $term]);
    }
}