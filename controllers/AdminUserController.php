<?php

class AdminUserController extends Controller {
    private $userModel;
    private $authController;

    public function __construct() {
        $this->userModel = new User();
        $this->authController = new AuthController();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Zorg ervoor dat de gebruiker een admin is
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 1) { // Veronderstellend dat '1' admin is
            header('Location: /login');
            exit();
        }
    }

    // Methode om gebruikersgegevens op te halen
    public function edit() {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Ongeldige gebruikers-ID.']);
            exit();
        }

        $userId = intval($_GET['id']);
        $user = $this->userModel->getUserById($userId);

        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'Gebruiker niet gevonden.']);
            exit();
        }

        // Retourneer gebruikersgegevens als JSON
        header('Content-Type: application/json');
        echo json_encode($user);
    }

    // Methode om gebruikersgegevens bij te werken
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Methode niet toegestaan.']);
            exit();
        }

        // Valideer en verzamel de gegevens
        $userId = $_POST['user_id'] ?? null;
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $age = $_POST['age'] ?? null;
        $gender = $_POST['gender'] ?? null;

        $errors = [];

        if (empty($fullName)) {
            $errors[] = "Naam is verplicht.";
        }

        if (empty($email)) {
            $errors[] = "E-mail is verplicht.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Ongeldig e-mail formaat.";
        }

        if ($userId === null || !is_numeric($userId)) {
            $errors[] = "Ongeldige gebruikers-ID.";
        }

        // Controleer of het e-mailadres al bestaat (anders dan de huidige gebruiker)
        $existingUser = $this->userModel->getUserByEmail($email);
        if ($existingUser && $existingUser['id'] != $userId) {
            $errors[] = "E-mail is al in gebruik.";
        }

        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['errors' => $errors]);
            exit();
        }

        // Update de gebruiker
        $data = [
            'full_name' => $fullName,
            'email' => $email,
            'age' => $age,
            'gender' => $gender
        ];

        if ($this->userModel->updateUser($userId, $data)) {
            echo json_encode(['success' => true]);
            exit();
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Fout bij het bijwerken van de gebruiker.']);
            exit();
        }
    }

    // Methode om een gebruiker te verwijderen
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Methode niet toegestaan.']);
            exit();
        }

        // Valideer CSRF token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->authController->validateCsrfToken($csrfToken)) {
            http_response_code(400);
            echo json_encode(['error' => 'Ongeldige CSRF token.']);
            exit();
        }

        $userId = $_POST['delete_user'] ?? null;

        if ($userId === null || !is_numeric($userId)) {
            http_response_code(400);
            echo json_encode(['error' => 'Ongeldige gebruikers-ID.']);
            exit();
        }

        if ($this->userModel->deleteUser($userId)) {
            echo json_encode(['success' => true]);
            exit();
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Fout bij het verwijderen van de gebruiker.']);
            exit();
        }
    }
 }
?>
