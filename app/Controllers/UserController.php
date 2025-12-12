<?php

require_once __DIR__ . '/../Model/User.php';

class UserController {

    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function inscription() {
        require_once __DIR__ . '/../Views/User/inscription.php';
    }

    public function enregistrer() {
        $nom = $_POST['nom'];
        $email = $_POST['email'];
        $password = $_POST['pwd'];

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $this->userModel->add($nom, $email, $hashedPassword);

        require_once __DIR__ . '/../Views/User/enregistrement.php';
    }

    public function connexion() {
        require_once __DIR__ . '/../Views/User/connexion.php';
    }

    public function verifieConnexion(){

        $nom = $_POST['nom'];
        $password = $_POST['pwd'];

        $users = $this->userModel->findBy(['nom' => $nom]);
        $user = $users ? $users[0] : null;

        if ($user && password_verify($password, $user['password'])){
            $_SESSION['id'] = $user['id'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['email'] = $user['email'];


            header('Location: ?c=home');
        }
        else{
            $_SESSION['message']['danger'] = "Identifiant ou mot de passe incorrect.";
            header('Location: ?c=User&a=connexion');
        }
    }

    public function deconnexion() {
        session_destroy();
        header('Location: ?c=home');
    }

    public function profil() {
        if (!isset($_SESSION['id'])) {
            echo "Vous devez être connecté.";
            return;
        }

        $user = $this->userModel->find($_SESSION['id']);
        require_once __DIR__ . '/../Views/User/profil.php';
    }
    
    public function updateProfilAjax() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['id'])) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié.']);
            return;
        }

        $id = $_POST['id'] ?? null;
        if ($id != $_SESSION['id']) {
            echo json_encode(['success' => false, 'message' => 'Accès refusé.']);
            return;
        }

        $nom = $_POST['nom'] ?? null;
        $email = $_POST['email'] ?? null;
        
        if (!$nom || !$email) {
            echo json_encode(['success' => false, 'message' => 'Le nom et l\'email sont requis.']);
            return;
        }
        
        $user = $this->userModel->find($id);
        $passwordHash = $user['password'];

        if ($this->userModel->update($id, $nom, $email, $passwordHash)) {
            $_SESSION['nom'] = $nom;
            $_SESSION['email'] = $email;
            
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Échec de la mise à jour de la base de données.']);
        }
    }
}