<?php

include_once 'core/Database.php';

// Maak een nieuwe instantie van de Database-klasse
$database = new Database();
$pdo = $database->getConnection();

// Controleer of het formulier is verzonden
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verkrijg de antwoorden van de gebruiker
    $userId = $_POST['user_id'];

    $dateCreated = $_POST['date_created'];

    // Maak een array van antwoorden en controleer op lege velden
    $answers = [
        'sleep_hours' => !empty($_POST['sleep_hours']) ? $_POST['sleep_hours'] : 0,
        'stress_level' => !empty($_POST['stress_level']) ? $_POST['stress_level'] : 0,
        'water_glasses' => !empty($_POST['water_glasses']) ? $_POST['water_glasses'] : 0,
        'exercise_done' => ($_POST['exercise_done'] === 'ja') ? 1 : 0, // Zet 'ja' om naar 1 en 'nee' naar 0
        'energy_level' => !empty($_POST['energy_level']) ? $_POST['energy_level'] : 0,
        'healthy_eating' => ($_POST['healthy_eating'] === 'ja') ? 1 : 0, // Zelfde voor gezonde voeding
        'physical_complaints' => $_POST['physical_complaints'], // En voor fysieke klachten
        'mental_state' => !empty($_POST['mental_state']) ? $_POST['mental_state'] : 0,
        'medication_taken' => ($_POST['medication_taken'] === 'ja') ? 1 : 0 // Zet 'ja' om naar 1 en 'nee' naar 0
    ];



    // Sla de antwoorden op in de database
    try {
        $stmt = $pdo->prepare("INSERT INTO daily_health_checks 
            (user_id, date_created, sleep_hours, stress_level, water_glasses, exercise_done, energy_level, healthy_eating, physical_complaints, mental_state, medication_taken) 
            VALUES 
            (:user_id, :date_created, :sleep_hours, :stress_level, :water_glasses, :exercise_done, :energy_level, :healthy_eating, :physical_complaints, :mental_state, :medication_taken)");

        $stmt->execute([
            ':user_id' => $userId,
            ':date_created' => $dateCreated,
            ':sleep_hours' => $answers['sleep_hours'],
            ':stress_level' => $answers['stress_level'],
            ':water_glasses' => $answers['water_glasses'],
            ':exercise_done' => $answers['exercise_done'],
            ':energy_level' => $answers['energy_level'],
            ':healthy_eating' => $answers['healthy_eating'],
            ':physical_complaints' => $answers['physical_complaints'],
            ':mental_state' => $answers['mental_state'],
            ':medication_taken' => $answers['medication_taken']
        ]);

        header('Location: dashboard');
    } catch (PDOException $e) {
        echo "Fout bij het opslaan van de gegevens: " . $e->getMessage();
    }
}
