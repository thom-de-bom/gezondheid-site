<?php 
class RegistrationService {
    private $userModel;

    public function __construct(User $userModel) {
        $this->userModel = $userModel;
    }

    public function register($data) {
        $this->validateRegistration($data);
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        return $this->userModel->register($data['full_name'], $data['email'], $hashedPassword);
    }

    private function validateRegistration($data) {
        if (empty($data['full_name']) || empty($data['email']) || empty($data['password'])) {
            throw new Exception("Alle velden moeten ingevuld.");
        }

        if ($data['password'] !== $data['confirm_password']) {
            throw new Exception("Wachtwoorden komen niet overeen.");
        }

        if ($this->userModel->emailExists($data['email'])) {
            throw new Exception("Email is al geregistreerd.");
        }
    }
}
?>