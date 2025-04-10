<?php

namespace App\Http\Controllers;

use App\Models\Firebase;
use Illuminate\Http\Request;

class TeacherController extends Controller
{

    protected $database;

    public function __construct(Firebase $firebase)
    {
        $this->database = $firebase->getDatabase();
    }
    public function index()
{
    try {
        // Get data from Firebase
        $quizzes = $this->database->getReference('quizs')->getValue();
        $attempts = $this->database->getReference('attempt_quizs')->getValue();
        $users = $this->database->getReference('users')->getValue();

        // Calculate quiz status counts
        $completed = 0;
        $ongoing = 0;

        if ($quizzes) {
            foreach ($quizzes as $quiz) {
                if (isset($quiz['status'])) {
                    if ($quiz['status'] === 'Done') $completed++;
                    elseif ($quiz['status'] === 'Ongoing') $ongoing++;
                }
            }
        }

        // Calculate score distribution and user rankings
        $scores = [];
        $scoreDistribution = array_fill(0, 10, 0);
        $userStats = [];

        if ($attempts && $users) {
            foreach ($attempts as $attemptId => $attempt) {
                if (isset($attempt['status']) && $attempt['status'] === 'completed' && isset($attempt['score'])) {
                    $score = (int)$attempt['score'];
                    $scores[] = $score;
                    $userId = $attempt['user_id'] ?? $attemptId;

                    // Get user name from users collection
                    $userName = 'Unknown';
                    if (isset($users[$userId]) && isset($users[$userId]['name'])) {
                        $userName = $users[$userId]['name'];
                    }

                    // Initialize user stats if not exists
                    if (!isset($userStats[$userId])) {
                        $userStats[$userId] = [
                            'name' => $userName,
                            'total_score' => 0,
                            'attempt_count' => 0,
                            'highest_score' => 0,
                            'last_attempt' => $attempt['completed_at'] ?? null
                        ];
                    }

                    // Update user stats
                    $userStats[$userId]['total_score'] += $score;
                    $userStats[$userId]['attempt_count']++;
                    $userStats[$userId]['highest_score'] = max($userStats[$userId]['highest_score'], $score);

                    // Classify score
                    if ($score >= 5 && $score <= 10) $scoreDistribution[0]++;
                    elseif ($score >= 15 && $score <= 20) $scoreDistribution[1]++;
                    elseif ($score >= 25 && $score <= 30) $scoreDistribution[2]++;
                    elseif ($score >= 35 && $score <= 40) $scoreDistribution[3]++;
                    elseif ($score >= 45 && $score <= 50) $scoreDistribution[4]++;
                    elseif ($score >= 55 && $score <= 60) $scoreDistribution[5]++;
                    elseif ($score >= 65 && $score <= 70) $scoreDistribution[6]++;
                    elseif ($score >= 75 && $score <= 80) $scoreDistribution[7]++;
                    elseif ($score >= 85 && $score <= 90) $scoreDistribution[8]++;
                    elseif ($score >= 95 && $score <= 100) $scoreDistribution[9]++;
                }
            }
        }

        // Convert to array and calculate averages
        $participants = array_map(function($userId, $stats) {
            return [
                'user_id' => $userId,
                'name' => $stats['name'],
                'total_score' => $stats['total_score'],
                'average_score' => round($stats['total_score'] / max(1, $stats['attempt_count']), 1),
                'attempt_count' => $stats['attempt_count'],
                'highest_score' => $stats['highest_score'],
                'last_attempt' => $stats['last_attempt']
            ];
        }, array_keys($userStats), $userStats);

        // Sort by total score (descending)
        usort($participants, function($a, $b) {
            return $b['total_score'] <=> $a['total_score'];
        });

        // Get top 10 participants
        $topParticipants = array_slice($participants, 0, 10);

        return view('pages.teacher.Home.index', [
            'completed' => $completed,
            'ongoing' => $ongoing,
            'scoreRanges' => ['5-10', '15-20', '25-30', '35-40', '45-50', '55-60', '65-70', '75-80', '85-90', '95-100'],
            'scoreDistribution' => $scoreDistribution,
            'totalParticipants' => count($scores),
            'topParticipants' => $topParticipants
        ]);

    } catch (\Exception $e) {
        Log::error('HomeController error: ' . $e->getMessage());

        return view('pages.teacher.Home.index', [
            'completed' => 0,
            'ongoing' => 0,
            'scoreRanges' => ['5-10', '15-20', '25-30', '35-40', '45-50', '55-60', '65-70', '75-80', '85-90', '95-100'],
            'scoreDistribution' => array_fill(0, 10, 0),
            'totalParticipants' => 0,
            'topParticipants' => [],
            'error' => 'Failed to load data'
        ]);
    }
}

}
