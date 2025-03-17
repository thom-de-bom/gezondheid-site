<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gezondheidsmeter - Uw Gezondheid, Onder Controle</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-blue-50 font-sans">
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 bg-white shadow-md z-10">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center w-4/5 max-w-screen-xl relative">
            <div class="text-2xl font-bold text-blue-600">Gezondheidsmeter</div>
            
            <!-- Mobile Menu Button -->
            <button id="mobile-menu-toggle" class="md:hidden text-blue-600 focus:outline-none">
                <i class="fas fa-bars text-2xl"></i>
            </button>
            
            <!-- Desktop Navigation -->
            <div class="hidden md:flex space-x-4">
                <a href="/login" class="bg-white text-blue-600 border border-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50 transition flex items-center">
                    <i class="fas fa-lock mr-2"></i>
                    Login
                </a>
                <a href="/registreren" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                    <i class="fas fa-user mr-2"></i>
                    Registreren
                </a>
            </div>
            
            <!-- Mobile Navigation Dropdown -->
            <div id="mobile-menu" class="absolute top-full left-0 right-0 bg-white shadow-md md:hidden hidden">
                <div class="flex flex-col items-center py-4 space-y-4">
                    <a href="/login" class="text-blue-600 border border-blue-600 px-6 py-3 rounded-lg hover:bg-blue-50 transition w-3/4 text-center flex items-center justify-center">
                        <i class="fas fa-lock mr-2"></i>
                        Login
                    </a>
                    <a href="register.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition w-3/4 text-center flex items-center justify-center">
                        <i class="fas fa-user mr-2"></i>
                        Registreren
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="container mx-auto px-4 pt-24 pb-12 w-4/5 max-w-screen-xl">
        <div class="grid md:grid-cols-2 gap-8 items-center">
            <div>
                <h1 class="text-5xl font-bold text-blue-800 mb-6">
                    Uw Gezondheid, <br />Onder Controle
                </h1>
                <p class="text-xl text-blue-700 mb-8">
                    Volg, analyseer en verbeter uw gezondheid met Gezondheidsmeter - 
                    uw persoonlijke gezondheidsdashboard.
                </p>
                <div class="flex space-x-4">
                    <a href="dashboard.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                        Aan de Slag
                    </a>
                    <a href="#features" class="bg-white text-blue-600 border border-blue-600 px-6 py-3 rounded-lg hover:bg-blue-50 transition">
                        Meer Info
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="bg-white py-16">
        <div class="container mx-auto px-4 text-center w-4/5 max-w-screen-xl">
            <h2 class="text-4xl font-bold text-blue-800 mb-12">Onze Functies</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <?php 
                $features = [
                    [
                        'icon' => 'lock',
                        'title' => 'Veilig',
                        'description' => 'Uw gegevens worden versleuteld en veilig opgeslagen.'
                    ],
                    [
                        'icon' => 'user',
                        'title' => 'Persoonlijk',
                        'description' => 'Volledig aangepast aan uw individuele gezondheidsbehoeften.'
                    ],
                    [
                        'icon' => 'chart-line',
                        'title' => 'Inzichtelijk',
                        'description' => 'Duidelijke grafieken en analyses van uw gezondheidsvoortgang.'
                    ]
                ];

                foreach ($features as $feature): ?>
                    <div class="bg-blue-50 p-6 rounded-lg shadow-md hover:shadow-xl transition">
                        <i class="fas fa-<?= htmlspecialchars($feature['icon']) ?> w-12 h-12 mx-auto text-blue-600 mb-4 text-5xl"></i>
                        <h3 class="text-xl font-bold text-blue-800 mb-4"><?= htmlspecialchars($feature['title']) ?></h3>
                        <p class="text-blue-700"><?= htmlspecialchars($feature['description']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-blue-600 text-white py-8">
        <div class="container mx-auto px-4 text-center w-4/5 max-w-screen-xl">
            <p>&copy; <?= date('Y') ?> Gezondheidsmeter. Alle rechten voorbehouden.</p>
        </div>
    </footer>

    <script>
        document.getElementById('mobile-menu-toggle').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>
</html>