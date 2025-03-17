<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dagelijkse Gezondheids Check - Gezondheidsmeter</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-blue-50 font-sans">
<div class="container mx-auto px-4 pt-24 pb-12 w-4/5 max-w-screen-xl">
    <div class="bg-white p-8 rounded-lg shadow-md">
        <div class="mb-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-blue-800">Dagelijkse Gezondheids Check</h1>
                <span class="text-gray-500">Vraag <span id="currentQuestionNumber">1</span> van 10</span>
            </div>
            
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div id="progressBar" class="bg-blue-600 h-2.5 rounded-full" style="width: 10%"></div>
            </div>
        </div>

        <form id="healthCheckForm" method="POST" action="submit_answers.php" class="space-y-6">
            <div class="mb-6">
                <h2 id="questionText" class="text-xl font-semibold text-gray-700 mb-4"></h2>
                <div id="answerInput" class="w-full"></div>
            </div>

            <!-- Hidden inputs to store all answers -->
            <input type="hidden" id="userIdInput" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
            <input type="hidden" id="dateCreatedInput" name="date_created" value="<?php echo date('Y-m-d H:i:s'); ?>">
            <input type="hidden" id="currentQuestionInput" name="current_question" value="1">
            <input type="hidden" id="1" name="sleep_hours">
            <input type="hidden" id="2" name="stress_level">
            <input type="hidden" id="3" name="water_glasses">
            <input type="hidden" id="4" name="exercise_done">
            <input type="hidden" id="5" name="energy_level">
            <input type="hidden" id="6" name="healthy_eating">
            <input type="hidden" id="7" name="physical_complaints">
            <input type="hidden" id="8" name="mental_state">
            <input type="hidden" id="9" name="medication_taken">

            <div class="flex justify-between pt-6">
                <button type="button" id="prevButton" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition" style="display: none;">
                    Vorige
                </button>
                <div></div>
                <button type="button" id="nextButton" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    Volgende
                </button>
            </div>

            <!-- Hidden submit input for final submission -->
            <input type="hidden" name="submit" id="submitInput" value="">
        </form>
    </div>
</div>

<script>
    const questions = {
        1: "Hoeveel uur heb je geslapen afgelopen nacht?",
        2: "Hoe zou je je stress niveau vandaag beoordelen? (1-10)",
        3: "Hoeveel glazen water heb je vandaag gedronken?",
        4: "Heb je vandaag bewust tijd genomen om te bewegen?",
        5: "Hoe zou je je energie niveau op dit moment beoordelen? (1-10)",
        6: "Heb je vandaag gezond gegeten?",
        7: "Heb je last van lichamelijke klachten?",
        8: "Hoe is je mentale gesteldheid vandaag? (1-10)",
        9: "Heb je vandaag je medicatie ingenomen? (indien van toepassing)",
        10: "Hoe tevreden ben je over je dag tot nu toe? (1-10)"
    };

    let currentQuestion = 1;
    const answers = {};

    function updateQuestion() {
        document.getElementById('currentQuestionNumber').textContent = currentQuestion;
        document.getElementById('questionText').textContent = questions[currentQuestion];
        document.getElementById('progressBar').style.width = `${(currentQuestion / 10) * 100}%`;
        document.getElementById('currentQuestionInput').value = currentQuestion;

        const answerInput = document.getElementById('answerInput');
        answerInput.innerHTML = '';

        switch(currentQuestion) {
            case 1: // Sleep hours
                answerInput.innerHTML = `
                    <input type="number" id="activeInput" min="0" max="24" step="0.5" 
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        value="${answers[currentQuestion] || ''}" required>
                `;
                break;
            case 4: // Exercise
            case 6: // Healthy eating
            case 9: // Medication
                answerInput.innerHTML = `
                    <select id="activeInput" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Maak een keuze</option>
                        <option value="ja" ${answers[currentQuestion] === 'ja' ? 'selected' : ''}>Ja</option>
                        <option value="nee" ${answers[currentQuestion] === 'nee' ? 'selected' : ''}>Nee</option>
                    </select>
                `;
                break;
            case 7: // Physical complaints
                answerInput.innerHTML = `
                    <textarea id="activeInput" rows="3" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                            placeholder="Beschrijf eventuele klachten...">${answers[currentQuestion] || ''}</textarea>
                `;
                break;
            default: // Rating questions (1-10)
                answerInput.innerHTML = `
                    <div class="space-y-2">
                        <input type="range" id="activeInput" min="1" max="10" class="w-full" 
                            value="${answers[currentQuestion] || 5}"
                            oninput="document.getElementById('rangeValue').textContent = this.value">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">1</span>
                            <span id="rangeValue" class="text-blue-600 font-semibold">${answers[currentQuestion] || 5}</span>
                            <span class="text-gray-500">10</span>
                        </div>
                    </div>
                `;
                break;
        }

        document.getElementById('prevButton').style.display = currentQuestion > 1 ? 'block' : 'none';
        const nextButton = document.getElementById('nextButton');
        nextButton.textContent = currentQuestion === 10 ? 'Afronden' : 'Volgende';
        nextButton.className = currentQuestion === 10 
            ? 'bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition'
            : 'bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition';
    }

    function saveAnswer() {
        const activeInput = document.getElementById('activeInput');
        if (activeInput) {
            answers[currentQuestion] = activeInput.value;

            // Zoek het juiste verborgen invoerveld op basis van het vraagnummer (currentQuestion)
            const hiddenInput = document.getElementById(currentQuestion.toString());
            if (hiddenInput) {
                hiddenInput.value = activeInput.value;
            }
        }
    }

    function submitForm() {
        saveAnswer();
        document.getElementById('submitInput').value = 'true';
        const submitButton = document.createElement('button');
        submitButton.type = 'submit';
        submitButton.style.display = 'none';
        document.getElementById('healthCheckForm').appendChild(submitButton);
        submitButton.click();
        document.getElementById('healthCheckForm').removeChild(submitButton);
    }

    document.getElementById('nextButton').addEventListener('click', () => {
        saveAnswer();
        if (currentQuestion === 10) {
            submitForm();
        } else {
            currentQuestion++;
            updateQuestion();
        }
    });

    document.getElementById('prevButton').addEventListener('click', () => {
        saveAnswer();
        currentQuestion--;
        updateQuestion();
    });

    updateQuestion();

</script>
</body>
</html>