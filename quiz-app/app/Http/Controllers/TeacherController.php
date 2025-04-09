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
            // Ambil data dari node 'quiz' dan 'attempt_quizs'
            $quizzes = $this->database->getReference('quizs')->getValue();
            $attempts = $this->database->getReference('attempt_quizs')->getValue();

            // Hitung quiz done dan ongoing
            $completed = 0;
            $ongoing = 0;

            if ($quizzes) {
                foreach ($quizzes as $quiz) {
                    if (isset($quiz['status'])) {
                        if ($quiz['status'] === 'Done') {
                            $completed++;
                        } elseif ($quiz['status'] === 'Ongoing') {
                            $ongoing++;
                        }
                    }
                }
            }
            // Hitung distribusi skor dari attempt_quizs
            $scores = [];
            $scoreDistribution = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0]; // 10 bins

            if ($attempts) {
                foreach ($attempts as $attempt) {
                    if (isset($attempt['status']) && $attempt['status'] === 'completed' && isset($attempt['score'])) {
                        $score = (int)$attempt['score'];
                        $scores[] = $score;

                        // Klasifikasikan score
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

            return view('pages.teacher.Home.index', [
                'completed' => $completed,
                'ongoing' => $ongoing,
                'scoreRanges' => ['5-10', '15-20', '25-30', '35-40', '45-50', '55-60', '65-70', '75-80', '85-90', '95-100'],
                'scoreDistribution' => $scoreDistribution,
                'totalParticipants' => count($scores)
            ]);

        } catch (\Exception $e) {
            Log::error('HomeController error: ' . $e->getMessage());

            return view('pages.teacher.Home.index', [
                'completed' => 0,
                'ongoing' => 0,
                'scoreRanges' => ['5-10', '15-20', '25-30', '35-40', '45-50', '55-60', '65-70', '75-80', '85-90', '95-100'],
                'scoreDistribution' => array_fill(0, 10, 0),
                'totalParticipants' => 0,
                'error' => 'Failed to load data'
            ]);
        }
    }



}
