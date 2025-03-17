<?php

class WeeklySummaryService
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function generateWeeklySummary($userId)
    {
        try {
            $weekStart = date('Y-m-d', strtotime('monday this week'));
            $weekEnd = date('Y-m-d', strtotime('sunday this week'));

            // Calculate weekly averages and totals
            $sql = "SELECT 
                    AVG(sleep_hours) as avg_sleep,
                    AVG(stress_level) as avg_stress,
                    AVG(water_glasses) as avg_water,
                    SUM(exercise_done) as exercise_days,
                    AVG(energy_level) as avg_energy,
                    SUM(healthy_eating) as healthy_eating_days,
                    AVG(mental_state) as avg_mental_state,
                    AVG(satisfaction_level) as avg_satisfaction,
                    COUNT(*) as total_days
                    FROM daily_health_checks
                    WHERE user_id = ?
                    AND date_created BETWEEN ? AND ?";

            $result = $this->db->query($sql, [$userId, $weekStart, $weekEnd]);
            $stats = $result->fetch(PDO::FETCH_ASSOC);

            // If no data found, return null
            if (!$stats || $stats['total_days'] == 0) {
                return null;
            }

            // Round the averages
            foreach ($stats as $key => $value) {
                if (strpos($key, 'avg_') === 0) {
                    $stats[$key] = round($value, 1);
                }
            }

            return [
                'stats' => $stats,
                'week_start' => $weekStart,
                'week_end' => $weekEnd
            ];

        } catch (Exception $e) {
            error_log("Error in WeeklySummaryService: " . $e->getMessage());
            return null;
        }
    }
}
