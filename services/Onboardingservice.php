<?php 
class OnboardingService {
    private $userModel;

    public function __construct(User $userModel) {
        $this->userModel = $userModel;
    }

    public function processOnboarding($userId, $data) {
        $processedData = [
            'weight' => floatval($data['weight']),
            'length' => intval($data['length']),
            'age' => intval($data['age']),
            'gender' => $data['gender'],
            'bmi' => $this->calculateBMI($data['weight'], $data['length']),
            'health_score' => $this->calculateInitialHealthScore(),
            'onboarding_complete' => true
        ];
        // var_dump($processedData);
        // die;
        return $this->userModel->updateUserData($userId, $processedData);
    }

    private function calculateBMI($weight, $length) {
    
        return round($weight / (($length/100) * ($length/100)), 3);
    }

    private function calculateInitialHealthScore() {
        return 70; // Base score, could be made more sophisticated
    }
}
?>