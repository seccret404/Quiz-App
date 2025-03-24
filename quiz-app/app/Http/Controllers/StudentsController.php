<?php

namespace App\Http\Controllers;

use App\Models\Firebase;
use Illuminate\Http\Request;

class StudentsController extends Controller
{
    protected $database;

    public function __construct(Firebase $firebase)
    {
        $this->database = $firebase->getDatabase();
    }
    public function home(){

        return view('pages.students.Home.home');
    }


    public function dashboard()
    {
        $userId = session('user_id'); // ID siswa yang sedang login

    // Ambil daftar attempt quiz berdasarkan user_id
    $attemptsRef = $this->database->getReference("attempt_quizs")
        ->orderByChild("user_id")
        ->equalTo($userId)
        ->getValue();

    $attemptedQuizIds = [];
    if ($attemptsRef) {
        foreach ($attemptsRef as $attempt) {
            $attemptedQuizIds[] = $attempt['quiz_id'];
        }
    }

    // Hitung total contribution berdasarkan quiz_id di attempt_quizs
    $contributionCount = [];
    $allAttemptsRef = $this->database->getReference("attempt_quizs")->getValue();

    if ($allAttemptsRef) {
        foreach ($allAttemptsRef as $attempt) {
            $quizId = $attempt['quiz_id'];
            if (!isset($contributionCount[$quizId])) {
                $contributionCount[$quizId] = 0;
            }
            $contributionCount[$quizId] += 1; // Hitung jumlah pengguna yang telah mengerjakan quiz ini
        }
    }

    // Ambil jumlah soal berdasarkan quiz_id di questions
    $quizData = [];
    foreach ($attemptedQuizIds as $quizId) {
        // Ambil semua soal yang memiliki id_quiz sama
        $questionsRef = $this->database->getReference("questions")
            ->orderByChild("id_quiz")
            ->equalTo($quizId)
            ->getValue();

        // Hitung jumlah soal
        $totalQuestions = $questionsRef ? count($questionsRef) : 0;

        // Ambil informasi quiz dari Firebase
        $quizRef = $this->database->getReference("quizs/{$quizId}")->getValue();

        if ($quizRef) {
            $quizData[] = [
                'id' => $quizId,
                'nama_quiz' => $quizRef['nama_quiz'] ?? 'Quiz Name',
                'total_questions' => $totalQuestions,
                'total_contribution' => $contributionCount[$quizId] ?? 0, // Total siswa yang mengerjakan quiz
            ];
        }
    }


    return view('pages.students.Home.dashboard', compact('quizData'));

    }

}
