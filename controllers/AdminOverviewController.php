<?php

class AdminOverviewController extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }
    public function fetchAllUsersCount() {
        $userCount = $this->userModel->GetAllUsersCount();

        // Return the response as JSON
        header('Content-Type: application/json');
        echo json_encode(['total_users' => $userCount]);
        exit;
    }
}