<?php
require_once 'core/Database.php';

// Create database connection
$db = new Database();

// Check if user is logged in
if (!isset($userData)) {
    header('Location: /login');
    exit();
}

$needsOnboarding = !isset($userData['onboarding_complete']) || !$userData['onboarding_complete'];

// Query to count daily health checks for current day
$query = "SELECT COUNT(*) FROM daily_health_checks WHERE user_id = :user_id AND DATE(date_created) = CURDATE()";
$stmt = $db->query($query, ['user_id' => $userData['id']]);

// Get result and ensure it's an integer
$hasCompletedDailyCheck = (int) $stmt->fetchColumn();

// Get health history
$historyQuery = "SELECT * FROM daily_health_checks WHERE user_id = :user_id ORDER BY date_created DESC LIMIT 5";
$historyStmt = $db->query($historyQuery, ['user_id' => $userData['id']]);
$history = $historyStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gezondheidsmeter</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-blue-50 font-sans">
    <nav class="fixed top-0 left-0 right-0 bg-white shadow-md z-10">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center w-4/5 max-w-screen-xl">
        <div class="text-2xl font-bold text-blue-600"><a href="/dashboard">Gezondheidsmeter</a></div>
            <div class="flex items-center space-x-4">
                <div class="relative inline-block text-left">
                    <div>
                        <button type="button" class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-white px-5 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50" id="menu-button" aria-expanded="true" aria-haspopup="true">
                            <?php echo htmlspecialchars($userData['full_name']); ?>
                            <svg class="-mr-1 size-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <div id="menu" class="hidden absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none divide-y divide-gray-200" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                        <div class="py-1" role="none">
                            <div class="px-4 py-3 text-sm text-gray-900">
                                <div><?php echo htmlspecialchars($userData['full_name']); ?></div>
                                <div class="font-medium truncate"><?php echo htmlspecialchars($userData['email']); ?></div>
                            </div>
                        </div>
                        <div class="py-1" role="none">
                            <a href="/geschiedenis" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1">Geschiedenis</a>
                            <a href="#" id="resetAccountLink" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1">Reset Account</a>
                            <form method="POST" action="/logout" role="none">
                                <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1" id="menu-item-3">Uitloggen</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>  
    </nav>
    <?php if ($hasCompletedDailyCheck === 0) : ?>
    <div class="container mx-auto px-4 pt-24 pb-1 w-4/5 max-w-screen-xl">
        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-red-800">
            <h4 class="text-xl font-bold text-red-800 mb-2">Let op</h4>
            <p class="text-gray-600 mb-4">Je hebt nog niet de dagelijkse check ingevuld. </p>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($needsOnboarding): ?>
    <div class="container mx-auto px-4 pt-24 pb-6 w-4/5 max-w-screen-xl">
        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-blue-600">
            <h2 class="text-xl font-bold text-blue-800 mb-2">Laten we beginnen!</h2>
            <p class="text-gray-600 mb-4">Om je gezondheid beter te kunnen monitoren, hebben we wat informatie van je nodig.</p>
            <a href="/onboarding" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 inline-block">
                Start vragenlijst
            </a>
        </div>
    </div>
    <?php endif; ?>

    <div class="container mx-auto px-4 <?= $needsOnboarding ? 'pt-6' : 'pt-24' ?> <?= $hasCompletedDailyCheck ? 'pt-24' : 'pt-6' ?> pb-12 w-4/5 max-w-screen-xl">
        <!-- Main Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Health Score Gauge -->
            <div class="col-span-2 md:col-span-1 bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold text-blue-800 mb-4">Gezondheids Score</h2>
                <div class="relative w-full h-48">
                    <canvas id="healthScoreGauge"></canvas>
                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-4xl font-bold">
                        <span class="text-blue-600"><?php echo $userData['health_score']; ?></span>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="col-span-2 md:col-span-1 grid grid-cols-2 gap-6">
                <div class="col-span-1 bg-white p-4 rounded-lg shadow-md">
                    <h3 class="text-gray-600 mb-2">BMI</h3>
                    <p class="text-3xl font-bold text-blue-600">
                        <?= isset($userData['bmi']) ? number_format($userData['bmi'], 1) : '-' ?>
                    </p>
                </div>
                <div class="col-span-1 bg-white p-4 rounded-lg shadow-md">
                    <h3 class="text-gray-600 mb-2">Gewicht</h3>
                    <p class="text-3xl font-bold text-blue-600">
                        <?= isset($userData['weight']) ? $userData['weight'] . ' kg' : '-' ?>
                    </p>
                </div>
                <div class="col-span-1 bg-white p-4 rounded-lg shadow-md">
                    <h3 class="text-gray-600 mb-2">Lengte</h3>
                    <p class="text-3xl font-bold text-blue-600">
                        <?= isset($userData['length']) ? $userData['length'] . ' cm' : '-' ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Daily Check Section with History Button -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h2 class="text-xl font-bold text-blue-800">Dagelijkse Check</h2>
                    <p class="text-gray-600 mt-1">Vul je dagelijkse vragenlijst in voor een betere gezondheidsscore</p>
                </div>
                <a href="/geschiedenis" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-history mr-2"></i>Bekijk Geschiedenis
                </a>
            </div>
            <a href="/daily-questions" class="block w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition text-center <?php echo $needsOnboarding ? 'opacity-50 cursor-not-allowed' : ($hasCompletedDailyCheck ? 'opacity-50 cursor-not-allowed' : '') ?>" <?php echo $hasCompletedDailyCheck || $needsOnboarding ? 'disabled' : ''; ?>>
                Start Vragenlijst
            </a>
        </div>

        <!-- Weekly Summary Section -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-blue-800">Wekelijks Overzicht</h2>
                <a href="/geschiedenis" class="text-blue-600 hover:text-blue-800 transition">
                    Bekijk alle statistieken <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <?php if (isset($weeklySummary) && $weeklySummary !== null): ?>
                <!-- Weekly stats content -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Add your weekly summary content here -->
                </div>
            <?php else: ?>
                <p class="text-gray-600">
                    Nog geen weekoverzicht beschikbaar. Vul dagelijks je gezondheidscheck in om inzichten te krijgen.
                </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Reset Account Popup -->
    <div id="confirmResetPopup" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-md max-w-md mx-auto text-center">
            <h3 class="text-xl font-medium text-gray-900 mb-4">Account Reset</h3>
            <p class="text-base text-gray-500 mb-4">
                Weet je het zeker dat je je account wilt resetten? Dit zal alle gegevens, waaronder je gezondheidshistorie en vooruitgang, permanent verwijderen.
            </p>
            <p class="text-base text-gray-500 mb-6">
                Na het resetten moet je opnieuw beginnen met het invoeren van je gegevens en het voltooien van de vragenlijsten.
            </p>
            <div class="flex justify-center space-x-4">
                <button id="confirmResetYes" type="button" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2">Ja, reset mijn account</button>
                <button id="confirmResetNo" type="button" class="bg-gray-200 text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300">Nee, annuleren</button>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize the gauge chart
        const ctx = document.getElementById('healthScoreGauge').getContext('2d');
        const score = <?php echo $userData['health_score']; ?>;
        
        // Create gradient with extended red and orange ranges
        const gradient = ctx.createLinearGradient(0, 0, 200, 0);
        gradient.addColorStop(0, '#DC2626');    // red-600 starts at 0%
        gradient.addColorStop(0.5, '#DC2626');  // red-600 extends to 50%
        gradient.addColorStop(0.7, '#D97706');  // yellow-600 from 50% to 70%
        gradient.addColorStop(1, '#059669');    // green-600 from 70% to 100%

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [score, 100 - score],
                    backgroundColor: [
                        gradient,
                        '#E5E7EB' // gray-200
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                circumference: 180,
                rotation: -90,
                cutout: '80%',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false
                    }
                }
            }
        });

        // Existing dropdown and reset account code
        const dropdownButton = document.getElementById('menu-button');
        const dropdownMenu = document.getElementById('menu');
        const confirmResetPopup = document.getElementById('confirmResetPopup');
        const confirmResetYes = document.getElementById('confirmResetYes');
        const confirmResetNo = document.getElementById('confirmResetNo');

        dropdownButton.addEventListener('click', function () {
            dropdownMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', function (event) {
            if (!dropdownButton.contains(event.target)) {
                dropdownMenu.classList.add('hidden');
            }
        });

        document.getElementById('resetAccountLink').addEventListener('click', function(e) {
            e.preventDefault();
            confirmResetPopup.classList.remove('hidden');
        });

        confirmResetYes.addEventListener('click', function() {
            confirmResetYes.disabled = true;
            confirmResetYes.textContent = 'Bezig met resetten...';

            fetch('/reset-account', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.error || 'Er is een fout opgetreden');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('Je account is succesvol gereset. De pagina wordt nu herladen.');
                    window.location.reload();
                } else {
                    throw new Error('Reset mislukt');
                }
            })
            .catch(error => {
                // Toon een foutmelding in de interface
                alert(`Fout: ${error.message}`);
            })
            .finally(() => {
                confirmResetPopup.classList.add('hidden');
                confirmResetYes.disabled = false;
                confirmResetYes.textContent = 'Ja, reset mijn account';
            });
        });

        confirmResetNo.addEventListener('click', function() {
            confirmResetPopup.classList.add('hidden');
        });
    });
</script>
</body>
</html>
