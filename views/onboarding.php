<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onboarding - Gezondheidsmeter</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-blue-50 font-sans">
    <div class="container mx-auto px-4 py-8 max-w-md">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-blue-800 mb-6">Laten we beginnen!</h1>
            
            <form action="/onboarding" method="POST" class="space-y-6">
             

                <div>
                    <label class="block text-gray-700 mb-2" for="age">
                        Leeftijd
                    </label>
                    <input type="number" 
                           name="age" 
                           id="age" 
                           required 
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-gray-700 mb-2" for="weight">
                        Gewicht (kg)
                    </label>
                    <input type="number" 
                           name="weight" 
                           id="weight" 
                           step="0.1" 
                           required 
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-gray-700 mb-2" for="length">
                        Lengte (cm)
                    </label>
                    <input type="number" 
                           name="length" 
                           id="length" 
                           required 
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Geslacht</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative">
                            <input type="radio" name="gender" value="man" required class="peer sr-only">
                            <div class="cursor-pointer rounded-lg border-2 p-4 text-center transition-all peer-checked:border-blue-600 peer-checked:bg-blue-50 hover:bg-gray-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-8 w-8 mb-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Man
                            </div>
                        </label>
                        <label class="relative">
                            <input type="radio" name="gender" value="vrouw" required class="peer sr-only">
                            <div class="cursor-pointer rounded-lg border-2 p-4 text-center transition-all peer-checked:border-blue-600 peer-checked:bg-blue-50 hover:bg-gray-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-8 w-8 mb-2 text-pink-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Vrouw
                            </div>
                        </label>
                    </div>
                </div>
                
                <button type="submit" 
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition">
                    Opslaan en doorgaan
                </button>
            </form>
        </div>
    </div>
</body>
</html>