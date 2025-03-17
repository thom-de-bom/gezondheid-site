<?php

require 'core/Router.php';
require 'core/Controller.php';
require 'core/Database.php';
require 'controllers/AuthController.php';
require 'controllers/AdminController.php';
require 'controllers/AdminOverviewController.php';
require 'controllers/DailyQuestionsController.php';

$router = new Router();

// Basic routes
$router->get('/', [new Controller(), 'home']);
$router->get('/login', [new AuthController(), 'showLoginForm']);
$router->get('/registreren', [new AuthController(), 'showRegisterForm']);
$router->get('/dashboard', [new AuthController(), 'showDashboard']);
$router->get('/geschiedenis', [new AuthController(), 'showGeschiedenis']);

// Onboarding routes
$router->get('/onboarding', [new AuthController(), 'showOnboarding']);
$router->post('/onboarding', [new AuthController(), 'submitOnboarding']);

// Authentication routes
$router->post('/login', [new AuthController(), 'login']);
$router->post('/registreren', [new AuthController(), 'register']);
$router->post('/logout', [new AuthController(), 'logout']);
$router->post('/reset-account', [new AuthController(), 'resetAccount']);

// Admin routes
$router->get('/admin/dashboard', [new AuthController(), 'showAdminDashboard']);
$router->get('/userCount', [new AdminOverviewController(), 'fetchAllUsersCount']);

// Daily Questions routes
$router->get('/daily-questions', [new DailyQuestionsController(), 'show']);
$router->post('/daily-questions', [new DailyQuestionsController(), 'submit']);

$router->dispatch($_SERVER['REQUEST_URI']);
