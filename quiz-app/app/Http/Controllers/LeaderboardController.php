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


    function showQuestion($quizId) {
        // Ambil semua data quiz
        $quizzes = $this->database->getReference("quizs")->getValue();

        $quiz = null;
        foreach ($quizzes as $qzId => $quizData) {
            if (isset($quizData['code_quiz']) && $quizData['code_quiz'] == $quizId) {
                $quiz = $quizData;
                break;
            }
        }

        if ($quiz === null) {
            return abort(404, 'Quiz not found');
        }

        // Ambil semua data questions dari Firebase
        $questions = $this->database->getReference("questions")->getValue();

        // Filter hanya questions yang memiliki code_quiz yang sama dengan quiz yang diklik
        $filteredQuestions = [];
        if (!empty($questions)) {
            foreach ($questions as $questionId => $question) {
                if (isset($question['code_quiz']) && $question['code_quiz'] == $quizId) {
                    $filteredQuestions[$questionId] = $question;
                }
            }
        }
        
        $filteredQuestions = array_values($filteredQuestions);


        return view('pages.teacher.leaderboard.detail_soal', compact('filteredQuestions', 'quiz'));
    }


}
