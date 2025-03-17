<?php 
class User {
    private $db;
 
    public function __construct() {
        $this->db = new Database();
    }
 
    public function login($email, $password) {
        $sql = "SELECT id, full_name, email, password, is_admin FROM users WHERE email = :email";
        $stmt = $this->db->query($sql, ['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
 
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function GetAllUsersCount() {
        $sql = "SELECT COUNT(*) AS total_users FROM users;";
        $stmt = $this->db->query($sql);
        $userCount = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userCount) {
            return $userCount;
        }
        return false;
    }

    public function resetAccount($userId) {
        try {
            // Reset user data
            $sql = "UPDATE users SET 
                    health_score = NULL,
                    weight = NULL,
                    length = NULL,
                    bmi = NULL,
                    onboarding_complete = 0
                    WHERE id = ?";
            
            $this->db->query($sql, [$userId]);
            
            
            
            // Delete user's daily answers
            $sql = "DELETE FROM daily_health_checks WHERE user_id = ?";
            $this->db->query($sql, [$userId]);
            
            return true;
        } catch (Exception $e) {
            error_log("Error resetting account: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function getUserData($userId) {
        try {
            $sql = "SELECT * FROM users WHERE id = :id";
            $stmt = $this->db->query($sql, ['id' => $userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching user data: " . $e->getMessage());
            return false;
        }
    }
 
    public function emailExists($email) {
        $sql = "SELECT id FROM users WHERE email = :email";
        $stmt = $this->db->query($sql, ['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }
 
    public function register($fullName, $email, $hashedPassword) {
        $sql = "INSERT INTO users (full_name, email, password) VALUES (:full_name, :email, :password)";
        $this->db->query($sql, [
            'full_name' => $fullName,
            'email' => $email,
            'password' => $hashedPassword
        ]);
    }
 
    public function updateUserData($userId, $data) {
        try {
            $sql = "UPDATE users 
                    SET weight = :weight,
                        length = :length,
                        bmi = :bmi,
                        health_score = :health_score,
                        onboarding_complete = :onboarding_complete,
                        age = :age,
                        gender = :gender
                    WHERE id = :id";
            
            return $this->db->query($sql, [
                'id' => $userId,
                'weight' => $data['weight'],
                'length' => $data['length'],
                'bmi' => $data['bmi'],
                'health_score' => $data['health_score'],
                'onboarding_complete' => 1,
                'age' => $data['age'],
                'gender' => $data['gender']
            ]);
        } catch (Exception $e) {
            error_log("Error updating user data: " . $e->getMessage());
            return false;
        }
    }
 }
?>