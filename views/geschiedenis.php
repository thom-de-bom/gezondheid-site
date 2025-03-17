<?php
require_once 'core/Database.php';

// Controleer of de gebruiker is ingelogd
if (!isset($userData)) {
    header('Location: /login');
    exit();
}

$db = new Database();

// Haal alle dagelijkse checks op
$sql = "SELECT * FROM daily_health_checks WHERE user_id = :user_id ORDER BY date_created DESC";
$checks = $db->query($sql, ['user_id' => $userData['id']])->fetchAll();

// Bereken gemiddelde, hoogste en laagste scores
$scores = array_filter(array_column($checks, 'health_score'), function($score) {
    return $score !== null;
});
$gemiddeld = !empty($scores) ? round(array_sum($scores) / count($scores), 1) : 0;
$hoogste = !empty($scores) ? max($scores) : 0;
$laagste = !empty($scores) ? min($scores) : 0;
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geschiedenis - Gezondheidsmeter</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-blue-50">
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
    <!-- Navigatie behouden zoals in dashboard -->
    <div class="container mx-auto px-4 pt-24 pb-12 w-4/5">
        <h1 class="text-3xl font-bold text-blue-800 mb-8">Geschiedenis</h1>

        <!-- Statistieken Overzicht -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Gemiddelde Score -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center mb-2">
                    <i class="fas fa-chart-line text-blue-600 text-xl mr-2"></i>
                    <h2 class="text-gray-600">Gemiddeld</h2>
                </div>
                <p class="text-3xl font-bold text-blue-600"><?php echo $gemiddeld; ?></p>
                <p class="text-sm text-gray-500">Gemiddelde Score</p>
            </div>

            <!-- Hoogste Score -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center mb-2">
                    <i class="fas fa-arrow-up text-green-600 text-xl mr-2"></i>
                    <h2 class="text-gray-600">Hoogste</h2>
                </div>
                <p class="text-3xl font-bold text-green-600"><?php echo $hoogste; ?></p>
                <p class="text-sm text-gray-500">Hoogste Score</p>
            </div>

            <!-- Laagste Score -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center mb-2">
                    <i class="fas fa-arrow-down text-red-600 text-xl mr-2"></i>
                    <h2 class="text-gray-600">Laagste</h2>
                </div>
                <p class="text-3xl font-bold text-red-600"><?php echo $laagste; ?></p>
                <p class="text-sm text-gray-500">Laagste Score</p>
            </div>

            <!-- Totaal Metingen -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center mb-2">
                    <i class="fas fa-clipboard-list text-blue-600 text-xl mr-2"></i>
                    <h2 class="text-gray-600">Totaal</h2>
                </div>
                <p class="text-3xl font-bold text-blue-600"><?php echo count($checks); ?></p>
                <p class="text-sm text-gray-500">Totaal Metingen</p>
            </div>
        </div>

        <!-- Geschiedenis Tabel -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Datum</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acties</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($checks as $check): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php echo date('d-m-Y H:i', strtotime($check['date_created'])); ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <?php
                                    $scoreColor = '';
                                    if (isset($check['health_score'])) {
                                        if ($check['health_score'] >= 70) {
                                            $scoreColor = 'text-green-600';
                                        } elseif ($check['health_score'] >= 40) {
                                            $scoreColor = 'text-yellow-600';
                                        } else {
                                            $scoreColor = 'text-red-600';
                                        }
                                    }
                                    ?>
                                    <span class="font-medium <?php echo $scoreColor; ?>"><?php echo $check['health_score'] ?? 'N/A'; ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 details-button"
                                        data-id="<?php echo $check['id']; ?>"
                                        onclick="showDetails(<?php echo htmlspecialchars(json_encode($check)); ?>)">
                                    Details
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Details Modal -->
    <div id="detailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-4/5 md:w-2/3 lg:w-1/2 shadow-lg rounded-lg bg-white">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-xl font-bold text-blue-800">Details Meting</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="modalContent" class="mt-4"></div>
        </div>
    </div>

    <script>
    function showDetails(check) {
        const modal = document.getElementById('detailsModal');
        const content = document.getElementById('modalContent');
        
        // Format the details
        const details = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-semibold mb-2">Slaap & Energie</h4>
                    <p>Slaapuren: ${check.sleep_hours}</p>
                    <p>Energie niveau: ${check.energy_level}/10</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-semibold mb-2">Mentaal Welzijn</h4>
                    <p>Stressniveau: ${check.stress_level}/10</p>
                    <p>Mentale staat: ${check.mental_state}/10</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-semibold mb-2">Fysieke Activiteit</h4>
                    <p>Beweging: ${check.exercise_done ? 'Ja' : 'Nee'}</p>
                    <p>Water glazen: ${check.water_glasses}</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-semibold mb-2">Voeding & Medicatie</h4>
                    <p>Gezond gegeten: ${check.healthy_eating ? 'Ja' : 'Nee'}</p>
                    <p>Medicatie genomen: ${check.medication_taken ? 'Ja' : 'Nee'}</p>
                </div>
            </div>
            ${check.physical_complaints ? `
            <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                <h4 class="font-semibold mb-2">Fysieke Klachten</h4>
                <p>${check.physical_complaints}</p>
            </div>
            ` : ''}
        `;
        
        content.innerHTML = details;
        modal.classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('detailsModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('detailsModal');
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    }



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
</script>
</body>
</html>