<?php 
$error = $error ?? null;
$old = $old ?? [];
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gezondheidsmeter</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-blue-50 font-sans">
    <!-- Navbar (same as previous page) -->
    <nav class="fixed top-0 left-0 right-0 bg-white shadow-md z-10">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center w-4/5 max-w-screen-xl relative">
        <a href="/" class="text-2xl font-bold text-blue-600">
    Gezondheidsmeter
</a>            
            <!-- Mobile Menu Button -->
            <button id="mobile-menu-toggle" class="md:hidden text-blue-600 focus:outline-none">
                <i class="fas fa-bars text-2xl"></i>
            </button>
            
            <!-- Desktop Navigation -->
            <div class="hidden md:flex space-x-4">
                <a href="login" class="bg-white text-blue-600 border border-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50 transition flex items-center">
                    <i class="fas fa-lock mr-2"></i>
                    Login
                </a>
                <a href="registreren" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
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

    <!-- Login Section -->
    <div class="container mx-auto px-4 pt-24 pb-12 w-4/5 max-w-screen-xl">
        <div class="max-w-md mx-auto bg-white rounded-xl shadow-lg p-8 space-y-6">
            <h2 class="text-3xl font-bold text-blue-800 text-center mb-6">Registreren</h2>
            
            <?php if ($error): ?>
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700">
                    <?php echo htmlspecialchars($error); ?>
                </p>
            </div>
        </div>
    </div>
<?php endif; ?>

            <form action="/registreren" method="POST" class="space-y-4">
            <div>
                    <label for="text" class="block text-blue-700 mb-2">Volledige Naam</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                        <input 
                            type="text" 
                            id="name" 
                            name="full_name" 
                            required 
                            placeholder="Uw volledige naam" 
                            class="w-full pl-10 pr-4 py-2 border border-blue-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                    </div>
                </div>
                
                <div>
                    <label for="email" class="block text-blue-700 mb-2">E-mail</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required 
                            placeholder="Uw e-mailadres" 
                            class="w-full pl-10 pr-4 py-2 border border-blue-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-blue-700 mb-2">Wachtwoord</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required 
                            placeholder="Uw wachtwoord" 
                            class="w-full pl-10 pr-4 py-2 border border-blue-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                        <button 
                            type="button" 
                            id="toggle-password" 
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-500 focus:outline-none"
                        >
                            <i class="fas fa-eye-slash" id="password-toggle-icon"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-blue-700 mb-2">Wachtwoord Bevestigen</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                        <input 
                            type="password" 
                            id="password" 
                            name="confirm_password" 
                            required 
                            placeholder="Uw wachtwoord" 
                            class="w-full pl-10 pr-4 py-2 border border-blue-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                        <button 
                            type="button" 
                            id="toggle-password" 
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-500 focus:outline-none"
                        >
                            <i class="fas fa-eye-slash" id="password-toggle-icon"></i>
                        </button>
                    </div>
                </div>

               

                <button 
                    type="submit" 
                    class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition flex items-center justify-center"
                >
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Registreren
                </button>
            </form>

            <div class="text-center mt-4">
                <p class="text-blue-700">
                    Heb je al een account? 
                    <a href="/login" class="text-blue-600 hover:underline">Inloggen</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Add JavaScript for Mobile Menu and Password Toggle -->
    <script>
        // Mobile Menu Toggle
        document.getElementById('mobile-menu-toggle').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });

        // Password Visibility Toggle
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.getElementById('toggle-password');
        const passwordToggleIcon = document.getElementById('password-toggle-icon');

        passwordToggle.addEventListener('click', function() {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggleIcon.classList.remove('fa-eye-slash');
                passwordToggleIcon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                passwordToggleIcon.classList.remove('fa-eye');
                passwordToggleIcon.classList.add('fa-eye-slash');
            }
        });
    </script>
</body>
</html>