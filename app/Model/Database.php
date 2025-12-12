<?php

Class Database {

    private $host = "localhost";
    private $db_name = "exercice_reseau_social";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection(){
        $this->conn = null;
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn-> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception){
            echo "Erreur de connexion : " . $exception->getMessage();
        }

        return $this->conn;
    }
}