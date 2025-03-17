<?php
require_once 'core/Database.php';

// Create database connection
$db = new Database();

// Calculate total users (excluding admin)
$totalUsers = $db->fetch("SELECT COUNT(*) as total FROM users WHERE id != 3")['total'];

// Calculate active users (users who have submitted a health check in the last 30 days)
$activeUsers = $db->fetch("
    SELECT COUNT(DISTINCT user_id) as active 
    FROM daily_health_checks 
    WHERE date_created >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
")['active'];

// Calculate average health score
$avgHealthScore = $db->fetch("
    SELECT AVG(health_score) as avg_score 
    FROM users 
    WHERE health_score IS NOT NULL
")['avg_score'] ?? 0;

// Calculate average BMI
$avgBMI = $db->fetch("
    SELECT AVG(bmi) as avg_bmi 
    FROM users 
    WHERE bmi IS NOT NULL
")['avg_bmi'] ?? 0;

// Get weekly trend data with comprehensive health score calculation
$weeklyTrend = $db->fetchAll("
    SELECT 
        CONCAT('Week ', WEEK(date_created)) as week,
        AVG(
            (
                -- Sleep score (0-8 hours = poor, 8-9 hours = good, >9 hours = needs improvement)
                CASE 
                    WHEN sleep_hours >= 8 AND sleep_hours <= 9 THEN 100
                    WHEN sleep_hours >= 7 AND sleep_hours < 8 THEN 80
                    WHEN sleep_hours > 9 THEN 70
                    ELSE 50
                END +
                -- Stress level (1-10, reversed as lower is better)
                (11 - stress_level) * 10 +
                -- Water intake (glasses, max 10 for score)
                LEAST(water_glasses * 10, 100) +
                -- Exercise
                CASE exercise_done
                    WHEN 'ja' THEN 100
                    ELSE 50
                END +
                -- Energy level (1-10)
                energy_level * 10 +
                -- Healthy eating
                CASE healthy_eating
                    WHEN 'ja' THEN 100
                    ELSE 50
                END +
                -- Mental state (1-10)
                mental_state * 10
            ) / 7  -- Divide by number of factors for average
        ) as avg_score,
        COUNT(DISTINCT user_id) as active_users,
        -- Add categorization
        CASE 
            WHEN AVG((
                CASE 
                    WHEN sleep_hours >= 8 AND sleep_hours <= 9 THEN 100
                    WHEN sleep_hours >= 7 AND sleep_hours < 8 THEN 80
                    WHEN sleep_hours > 9 THEN 70
                    ELSE 50
                END +
                (11 - stress_level) * 10 +
                LEAST(water_glasses * 10, 100) +
                CASE exercise_done
                    WHEN 'ja' THEN 100
                    ELSE 50
                END +
                energy_level * 10 +
                CASE healthy_eating
                    WHEN 'ja' THEN 100
                    ELSE 50
                END +
                mental_state * 10
            ) / 7) >= 80 THEN 'Goed'
            WHEN AVG((
                CASE 
                    WHEN sleep_hours >= 8 AND sleep_hours <= 9 THEN 100
                    WHEN sleep_hours >= 7 AND sleep_hours < 8 THEN 80
                    WHEN sleep_hours > 9 THEN 70
                    ELSE 50
                END +
                (11 - stress_level) * 10 +
                LEAST(water_glasses * 10, 100) +
                CASE exercise_done
                    WHEN 'ja' THEN 100
                    ELSE 50
                END +
                energy_level * 10 +
                CASE healthy_eating
                    WHEN 'ja' THEN 100
                    ELSE 50
                END +
                mental_state * 10
            ) / 7) >= 60 THEN 'Kan Beter'
            ELSE 'Aandacht Nodig'
        END as health_category
    FROM daily_health_checks
    WHERE date_created >= DATE_SUB(CURRENT_DATE, INTERVAL 4 WEEK)
    GROUP BY WEEK(date_created)
    ORDER BY date_created DESC
    LIMIT 4
");

// Calculate BMI distribution
$bmiDistribution = [
    ['range' => 'Ondergewicht', 'count' => $db->fetch("SELECT COUNT(*) as count FROM users WHERE bmi < 18.5")['count']],
    ['range' => 'Normaal', 'count' => $db->fetch("SELECT COUNT(*) as count FROM users WHERE bmi >= 18.5 AND bmi < 25")['count']],
    ['range' => 'Overgewicht', 'count' => $db->fetch("SELECT COUNT(*) as count FROM users WHERE bmi >= 25 AND bmi < 30")['count']],
    ['range' => 'Obesitas', 'count' => $db->fetch("SELECT COUNT(*) as count FROM users WHERE bmi >= 30")['count']]
];

// Fetch all users except admin
$users = $db->fetchAll("SELECT id, full_name, email, age, gender, bmi, health_score FROM users WHERE is_admin = 0");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_user'])) {
        $userId = $_POST['delete_user'];
        $db->execute("DELETE FROM users WHERE id = ?", [$userId]);
        header("Location: dashboard");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Gezondheidsmeter</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="./public/dashboard.css">
</head>
<body class="min-h-screen bg-blue-50">
    <nav class="fixed top-0 left-0 right-0 bg-white shadow-md z-10">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center w-4/5">
            <div class="text-2xl font-bold text-blue-600">Admin Dashboard</div>
            <div class="flex items-center space-x-4">
                <form action="/logout" method="POST" class="inline">
                    <button type="submit" class="bg-white text-blue-600 border border-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50">
                        Uitloggen
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 pt-24 pb-12 w-4/5">
        <div class="grid grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-gray-600 mb-2">Totaal Gebruikers</h3>
                <p class="text-3xl font-bold text-blue-600"><?= $totalUsers ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-gray-600 mb-2">Actieve Gebruikers</h3>
                <p class="text-3xl font-bold text-blue-600"><?= $activeUsers ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-gray-600 mb-2">Gemiddelde Score</h3>
                <p class="text-3xl font-bold text-blue-600"><?= number_format($avgHealthScore, 1) ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-gray-600 mb-2">Gemiddelde BMI</h3>
                <p class="text-3xl font-bold text-blue-600"><?= number_format($avgBMI, 1) ?></p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-bold text-blue-800 mb-4">Wekelijkse Trend</h2>
                <canvas id="trendChart"></canvas>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-bold text-blue-800 mb-4">BMI Verdeling</h2>
                <canvas id="bmiChart"></canvas>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 pt-24 pb-12 w-4/5">
        <h2 class="text-xl font-bold text-blue-800 mb-4">Gebruikersbeheer</h2>
        <table class="min-w-full bg-white rounded-lg shadow-md">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">Naam</th>
                    <th class="py-2 px-4 border-b">E-mail</th>
                    <th class="py-2 px-4 border-b">Leeftijd</th>
                    <th class="py-2 px-4 border-b">Geslacht</th>
                    <th class="py-2 px-4 border-b">BMI</th>
                    <th class="py-2 px-4 border-b">Gezondheidsscore</th>
                    <th class="py-2 px-4 border-b">Acties</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="py-2 px-4 border-b"><?= htmlspecialchars($user['full_name']) ?></td>
                        <td class="py-2 px-4 border-b"><?= htmlspecialchars($user['email']) ?></td>
                        <td class="py-2 px-4 border-b"><?= $user['age'] ?? '-' ?></td>
                        <td class="py-2 px-4 border-b"><?= $user['gender'] ?? '-' ?></td>
                        <td class="py-2 px-4 border-b"><?= number_format($user['bmi'], 2) ?? '-' ?></td>
                        <td class="py-2 px-4 border-b"><?= $user['health_score'] ?? '-' ?></td>
                        <td class="py-2 px-4 border-b">
                            <a href="edit_user.php?id=<?= $user['id'] ?>" class="text-blue-600">Wijzigen</a>
                            <form method="POST" class="inline">
                                <input type="hidden" name="delete_user" value="<?= $user['id'] ?>">
                                <button type="submit" class="text-red-600 ml-2" onclick="return confirm('Weet je zeker dat je deze gebruiker wilt verwijderen?')">Verwijderen</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Weekly Trend Chart
        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: <?= json_encode(array_column($weeklyTrend, 'week')) ?>,
                datasets: [{
                    label: 'Gemiddelde Gezondheidsscore',
                    data: <?= json_encode(array_column($weeklyTrend, 'avg_score')) ?>,
                    borderColor: '#2563EB',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 0,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const weekData = <?= json_encode($weeklyTrend) ?>[context.dataIndex];
                                return [
                                    `Score: ${Math.round(context.raw)}%`,
                                    `Status: ${weekData.health_category}`,
                                    `Actieve gebruikers: ${weekData.active_users}`
                                ];
                            }
                        }
                    }
                }
            }
        });

        // BMI Distribution Chart
        new Chart(document.getElementById('bmiChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($bmiDistribution, 'range')) ?>,
                datasets: [{
                    label: 'Aantal Gebruikers',
                    data: <?= json_encode(array_column($bmiDistribution, 'count')) ?>,
                    backgroundColor: '#2563EB'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>