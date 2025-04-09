<?php

namespace App\Http\Controllers;

use App\Models\Firebase;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{

    protected $database;

    public function __construct(Firebase $firebase)
    {
        $this->database = $firebase->getDatabase();
    }

    function leaderboard(){

        $quizzes = $this->database->getReference('quizs')->getValue();

        if ($quizzes === null) {
            return response()->json(['message' => 'No quizzes found']);
        }
        return view('pages.teacher.leaderboard.index', compact('quizzes'));
    }

    public function showQuestions($id)
    {
        $quizRef = $this->database->getReference("quizs/{$id}")->getValue();
        $questionsRef = $this->database->getReference("questions")
            ->orderByChild("id_quiz")
            ->equalTo($id)
            ->getValue();

        // Ambil semua jawaban untuk quiz ini
        $answersRef = $this->database->getReference("answers")
            ->orderByChild("id_quiz")
            ->equalTo($id)
            ->getValue();

        foreach ($questionsRef as $qId => &$question) {
            // Konversi options
            if (isset($question['options'])) {
                $question['options'] = (array)$question['options'];
            }

            // Hitung jawaban benar/salah
            $question['correct_count'] = 0;
            $question['wrong_count'] = 0;

            if ($answersRef) {
                foreach ($answersRef as $answer) {
                    if ($answer['id_question'] == $qId) {
                        if ($answer['is_correct']) {
                            $question['correct_count']++;
                        } else {
                            $question['wrong_count']++;
                        }
                    }
                }
            }
        }

        return view('pages.teacher.leaderboard.detail_soal', [
            'quiz' => array_merge($quizRef, ['id' => $id]),
            'questions' => $questionsRef ?: []
        ]);
    }

    public function showLeaderboard($quizId)
{
    // Ambil data quiz
    $quizRef = $this->database->getReference("quizs/{$quizId}")->getValue();

    // Ambil semua attempt untuk quiz ini dan urutkan berdasarkan score tertinggi
    $attemptsRef = $this->database->getReference("attempt_quizs")
        ->orderByChild("quiz_id")
        ->equalTo($quizId)
        ->getValue();

    // Urutkan attempts berdasarkan score (descending) dan beri ranking
    $leaderboard = [];
    if ($attemptsRef) {
        usort($attemptsRef, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        $currentRank = 1;
        $prevScore = null;

        foreach ($attemptsRef as $index => $attempt) {
            $userRef = $this->database->getReference("users/{$attempt['user_id']}")->getValue();

            // Jika score berbeda dengan sebelumnya, update ranking
            if ($prevScore !== null && $attempt['score'] !== $prevScore) {
                $currentRank = $index + 1;
            }

            $leaderboard[] = [
                'rank' => $currentRank,
                'user_name' => $userRef['name'] ?? 'Unknown',
                'score' => $attempt['score'],
                'timestamp' => $attempt['timestamp'] ?? null
            ];

            $prevScore = $attempt['score'];
        }
    }

    return view('pages.teacher.leaderboard.leader_board', [
        'quiz' => $quizRef,
        'leaderboard' => $leaderboard
    ]);
}

}
