<?php

class DailyQuestionsController extends Controller
{
    private $db;

    public function __construct()
    {
        // Start session at construction time to avoid multiple session_start calls
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = new Database();
    }

    public function show()
    {
        $userData = $this->getUserData();
        if (!$userData) {
            $this->redirect('/login');
            return;
        }

        // Check if user has completed onboarding
        if (!$userData['onboarding_complete']) {
            $_SESSION['error_message'] = 'Voltooi eerst je onboarding voordat je de vragenlijst invult.';
            $this->redirect('/dashboard');
            return;
        }

        // Initialize question variables
        if (!isset($_SESSION['current_question'])) {
            $_SESSION['current_question'] = 1;
        }
        if (!isset($_SESSION['answers'])) {
            $_SESSION['answers'] = array();
        }

        // Check if already submitted today
        if ($this->hasSubmittedToday()) {
            $_SESSION['error_message'] = 'Je hebt de vragen voor vandaag al ingevuld.';
            $this->redirect('/dashboard');
            return;
        }

        // Get questions from database
        $questions = $this->getQuestions();

        // Load view with data
        $data = [
            'questions' => $questions,
            'answers' => $_SESSION['answers'],
            'userData' => $userData
        ];

        require 'views/dailyquestions.php';
    }

    public function submit()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/daily-questions');
            return;
        }

        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }

        // Handle the answers from the form
        if (isset($_POST['answers']) && is_array($_POST['answers'])) {
            $_SESSION['answers'] = $_POST['answers'];
        }

        if (isset($_POST['current_question'])) {
            $_SESSION['current_question'] = intval($_POST['current_question']);
        }

        // If this is the final submission
        if (isset($_POST['submit']) && $_POST['submit'] === 'true') {
            if ($this->saveAnswers()) {
                // Update health score
                $this->updateHealthScore();

                // Clear session variables
                unset($_SESSION['current_question']);
                unset($_SESSION['answers']);

                $_SESSION['success_message'] = 'Je antwoorden zijn succesvol opgeslagen!';
                $this->redirect('/dashboard');
                return;
            } else {
                $_SESSION['error_message'] = 'Er is iets misgegaan bij het opslaan van je antwoorden.';
                $this->redirect('/daily-questions');
                return;
            }
        }

        $this->redirect('/daily-questions');
    }

    private function saveAnswers()
    {
        try {
            if ($this->hasSubmittedToday()) {
                return false;
            }

            $data = $this->prepareAnswersData();
            
            $sql = "INSERT INTO daily_health_checks 
                    (user_id, date_created, sleep_hours, stress_level, water_glasses,
                    exercise_done, energy_level, healthy_eating, physical_complaints,
                    mental_state, medication_taken, satisfaction_level)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $this->db->query($sql, [
                $data['user_id'], $data['date_created'], $data['sleep_hours'],
                $data['stress_level'], $data['water_glasses'], $data['exercise_done'],
                $data['energy_level'], $data['healthy_eating'], $data['physical_complaints'],
                $data['mental_state'], $data['medication_taken'], $data['satisfaction_level']
            ]);

            return true;
        } catch (Exception $e) {
            error_log("Error saving daily health check: " . $e->getMessage());
            return false;
        }
    }

    private function updateHealthScore()
    {
        try {
            $userId = $_SESSION['user_id'];
            $score = $this->calculateHealthScore();

            // Update user's health score
            $sql = "UPDATE users SET health_score = ? WHERE id = ?";
            $this->db->query($sql, [$score, $userId]);

            return true;
        } catch (Exception $e) {
            error_log("Error updating health score: " . $e->getMessage());
            return false;
        }
    }

    // Helper methods
    private function getUserData()
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        return $this->db->query($sql, [$_SESSION['user_id'] ?? null])->fetch();
    }

    private function getQuestions()
    {
        $sql = "SELECT * FROM daily_questions WHERE id <= 10 ORDER BY display_order ASC";
        return $this->db->query($sql)->fetchAll();
    }

    private function hasSubmittedToday()
    {
        $today = date('Y-m-d');
        $sql = "SELECT id FROM daily_health_checks WHERE user_id = ? AND date_created = ?";
        return (bool) $this->db->query($sql, [$_SESSION['user_id'], $today])->fetch();
    }

    private function prepareAnswersData()
    {
        return [
            'user_id' => $_SESSION['user_id'],
            'date_created' => date('Y-m-d'),
            'sleep_hours' => isset($_POST['answers'][1]) ? floatval($_POST['answers'][1]) : 0,
            'stress_level' => isset($_POST['answers'][2]) ? intval($_POST['answers'][2]) : 5,
            'water_glasses' => isset($_POST['answers'][3]) ? intval($_POST['answers'][3]) : 0,
            'exercise_done' => isset($_POST['answers'][4]) ? ($_POST['answers'][4] === 'ja' ? 1 : 0) : 0,
            'energy_level' => isset($_POST['answers'][5]) ? intval($_POST['answers'][5]) : 5,
            'healthy_eating' => isset($_POST['answers'][6]) ? ($_POST['answers'][6] === 'ja' ? 1 : 0) : 0,
            'physical_complaints' => isset($_POST['answers'][7]) ? $_POST['answers'][7] : '',
            'mental_state' => isset($_POST['answers'][8]) ? intval($_POST['answers'][8]) : 5,
            'medication_taken' => isset($_POST['answers'][9]) ? ($_POST['answers'][9] === 'ja' ? 1 : 0) : 0,
            'satisfaction_level' => isset($_POST['answers'][10]) ? intval($_POST['answers'][10]) : 5
        ];
    }

    private function calculateHealthScore()
    {
        $score = 70; // Base score

        if (isset($_POST['answers'])) {
            // Adjust score based on answers
            if ($_POST['answers'][1] >= 7 && $_POST['answers'][1] <= 9) {
                $score += 5;
            } // Good sleep
            if ($_POST['answers'][2] <= 4) {
                $score += 5;
            } // Low stress
            if ($_POST['answers'][3] >= 8) {
                $score += 5;
            } // Good water intake
            if ($_POST['answers'][4] === 'ja') {
                $score += 5;
            } // Exercise
            if ($_POST['answers'][5] >= 7) {
                $score += 5;
            } // Good energy
            if ($_POST['answers'][6] === 'ja') {
                $score += 5;
            } // Healthy eating
        }

        // Ensure score stays within 0-100 range
        return max(0, min(100, $score));
    }

    // private function redirect($path)
    // {
    //     if (!headers_sent()) {
    //         header("Location: $path");
    //         exit();
    //     }
    // }
}