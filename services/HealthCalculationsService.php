<?php

class HealthCalculationsService
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function calculateHealthScore($userId)
    {
        $baseScore = 70; // Basisscore

        try {
            // Haal de meest recente dagelijkse check op
            $sql = "SELECT * FROM daily_health_checks 
                   WHERE user_id = ? 
                   ORDER BY date_created DESC 
                   LIMIT 1";

            $result = $this->db->query($sql, [$userId]);
            $dailyCheck = $result->fetch(PDO::FETCH_ASSOC);

            if ($dailyCheck) {
                $score = $baseScore;

                // Slaap berekening (7-9 uur is optimaal)
                if ($dailyCheck['sleep_hours'] >= 7 && $dailyCheck['sleep_hours'] <= 9) {
                    $score += 5;
                } elseif ($dailyCheck['sleep_hours'] < 6 || $dailyCheck['sleep_hours'] > 10) {
                    $score -= 5;
                }

                // Stress niveau (lager is beter)
                if ($dailyCheck['stress_level'] <= 4) {
                    $score += 5;
                } elseif ($dailyCheck['stress_level'] >= 8) {
                    $score -= 5;
                }

                // Water inname (8 glazen of meer is optimaal)
                if ($dailyCheck['water_glasses'] >= 8) {
                    $score += 5;
                } elseif ($dailyCheck['water_glasses'] < 4) {
                    $score -= 5;
                }

                // Beweging
                if ($dailyCheck['exercise_done']) {
                    $score += 5;
                }

                // Energie niveau
                if ($dailyCheck['energy_level'] >= 7) {
                    $score += 5;
                } elseif ($dailyCheck['energy_level'] <= 4) {
                    $score -= 5;
                }

                // Gezond eten
                if ($dailyCheck['healthy_eating']) {
                    $score += 5;
                }

                // Mentale staat
                if ($dailyCheck['mental_state'] >= 7) {
                    $score += 5;
                } elseif ($dailyCheck['mental_state'] <= 4) {
                    $score -= 5;
                }

                // Zorg dat de score tussen 0 en 100 blijft
                $score = max(0, min(100, $score));

                // Update de gebruiker's gezondheidsscore
                $updateSql = "UPDATE users SET health_score = ? WHERE id = ?";
                $this->db->query($updateSql, [$score, $userId]);

                return $score;
            }

            return $baseScore;
        } catch (Exception $e) {
            error_log("Error calculating health score: " . $e->getMessage());
            return $baseScore;
        }
    }

    public function calculateBMI($weight, $height)
    {
        // Hoogte moet in meters zijn
        $heightInMeters = $height / 100;
        return round($weight / ($heightInMeters * $heightInMeters), 1);
    }

    public function getBMICategory($bmi)
    {
        if ($bmi < 18.5) {
            return "Ondergewicht";
        }
        if ($bmi < 25) {
            return "Gezond gewicht";
        }
        if ($bmi < 30) {
            return "Overgewicht";
        }
        return "Obesitas";
    }

    public function calculateWeeklyProgress($userId)
    {
        try {
            $sql = "SELECT 
                    AVG(sleep_hours) as avg_sleep,
                    AVG(stress_level) as avg_stress,
                    AVG(water_glasses) as avg_water,
                    COUNT(CASE WHEN exercise_done = 1 THEN 1 END) as exercise_days,
                    AVG(energy_level) as avg_energy,
                    COUNT(CASE WHEN healthy_eating = 1 THEN 1 END) as healthy_eating_days,
                    AVG(mental_state) as avg_mental_state,
                    COUNT(*) as total_days
                    FROM daily_health_checks
                    WHERE user_id = ?
                    AND date_created >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)";

            $result = $this->db->query($sql, [$userId]);
            $weeklyStats = $result->fetch(PDO::FETCH_ASSOC);

            $progressScore = 0;
            $maxScore = 0;

            // Bereken voortgangsscore
            if ($weeklyStats['avg_sleep'] >= 7 && $weeklyStats['avg_sleep'] <= 9) {
                $progressScore += 10;
            }
            $maxScore += 10;

            if ($weeklyStats['avg_stress'] <= 5) {
                $progressScore += 10;
            }
            $maxScore += 10;

            if ($weeklyStats['avg_water'] >= 8) {
                $progressScore += 10;
            }
            $maxScore += 10;

            if ($weeklyStats['exercise_days'] >= 3) {
                $progressScore += 10;
            }
            $maxScore += 10;

            if ($weeklyStats['avg_energy'] >= 7) {
                $progressScore += 10;
            }
            $maxScore += 10;

            if ($weeklyStats['healthy_eating_days'] >= 5) {
                $progressScore += 10;
            }
            $maxScore += 10;

            if ($weeklyStats['avg_mental_state'] >= 7) {
                $progressScore += 10;
            }
            $maxScore += 10;

            $weeklyStats['progress_percentage'] = ($progressScore / $maxScore) * 100;

            return $weeklyStats;
        } catch (Exception $e) {
            error_log("Error calculating weekly progress: " . $e->getMessage());
            return null;
        }
    }
}
