<?php

namespace App\Http\Controllers;

use App\Models\Firebase;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;

class GenerateQuizController extends Controller
{
    protected $database;

    public function __construct(Firebase $firebase)
    {
        $this->database = $firebase->getDatabase();
    }

    public function index()
    {
        $questions = session('questions', []);
        return view('pages.teacher.quiz.quiz', compact('questions'));
    }

    public function saveQuiz(Request $request)
    {
        try {
            // **1. Validasi Input**
            $request->validate([
                'nama_quiz' => 'required|string',
                'code_quiz' => 'required|string',
                'type_quiz' => 'required|string',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
                'questions' => 'required|array',
            ]);

            // **2. Simpan Data Quiz ke Firebase**
            $quizRef = $this->database->getReference('quizs')->push();
            $quizId = $quizRef->getKey(); // Mendapatkan ID unik dari Firestore

            $quizData = [
                'id'             => $quizId,
                'nama_quiz'      => $request->nama_quiz,
                'code_quiz'      => $request->code_quiz,
                'type_quiz'      => $request->type_quiz,
                'status'         =>"Open",
                'total_question' => count(array_filter($request->questions, fn($q) => isset($q['select']))),
                'start_time'     => $request->start_time,
                'end_time'       => $request->end_time,
                'created_at'     => now(),
            ];

            $quizRef->set($quizData);

            // **3. Simpan Hanya Pertanyaan yang Dipilih**
            $questionRef = $this->database->getReference('questions');

            foreach ($request->questions as $qIndex => $question) {
                if (!isset($question['select'])) continue; // Lewati jika tidak dipilih

                $questionData = [
                    'id_quiz'         => $quizId,
                    'code_quiz'       => $request->code_quiz,
                    'question'        => $question['question'],
                    'options'         => $question['options'],
                    'correct_answer'  => $question['answer'],
                    'level_questions' => $question['level'],
                    'feedback'        => $question['feedback'],
                    'score_question'  => $question['point'],
                    'timer'           => $question['time_limit'],
                    'created_at'      => now(),
                ];

                $questionRef->push($questionData);
            }

            return redirect()->back()->with('success', 'Quiz and selected questions saved successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to save quiz: ' . $e->getMessage());
        }
    }

    public function showquiz()
    {
        $quizzes = $this->database->getReference('quizs')->getValue();

        if (!empty($quizzes) && is_array($quizzes)) {
            $filteredQuizzes = array_filter($quizzes, function ($quiz) {
                return isset($quiz['status']) && $quiz['status'] === 'Open';
            });
        } else {
            $filteredQuizzes = [];
        }

        return view('pages.teacher.quiz.quiz_open', compact('filteredQuizzes'));
    }

    public function quizStart($id)
    {
        $quizRef = $this->database->getReference('quizs/' . $id);

        // dd($id);
        $quizRef->update([
            'status' => 'Ongoing'
        ]);

        return redirect()->route('quiz.ongoing')->with('success', 'Quiz started successfully!');

    }


    public function showQuizOngoing()
    {
        $quizzes = $this->database->getReference('quizs')->getValue();

        if (!empty($quizzes) && is_array($quizzes)) {
            $filteredQuizzes = array_filter($quizzes, function ($quiz) {
                return isset($quiz['status']) && $quiz['status'] === 'Ongoing';
            });
        } else {
            $filteredQuizzes = [];
        }

        return view('pages.teacher.quiz.quiz_ongoing', compact('filteredQuizzes'));
    }
}
