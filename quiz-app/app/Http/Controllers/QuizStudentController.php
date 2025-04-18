<?php

namespace App\Http\Controllers;

use App\Models\Firebase;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class QuizStudentController extends Controller
{

    protected $database;

    public function __construct(Firebase $firebase)
    {
        $this->database = $firebase->getDatabase();
    }
    public function quizPage($quizId, $questionId = null, Request $request)
    {
        $userId = session('user_id');
        $codeQuiz = $request->query('code_quiz');

        if (!$codeQuiz) {
            return redirect()->route('dashboard')->with('error', 'Code quiz tidak ditemukan.');
        }

        // Ambil data quiz
        $quizRef = $this->database->getReference("quizs")
                        ->orderByChild("code_quiz")
                        ->equalTo($codeQuiz)
                        ->getValue();

        if (!$quizRef) {
            return redirect()->route('dashboard')->with('error', 'Quiz tidak ditemukan.');
        }

        $quizData = reset($quizRef);
        $quizType = $quizData['type_quiz'] ?? 'Multiple Choice';

        // Ambil semua soal
        $questionsRef = $this->database->getReference("questions")
                                ->orderByChild("code_quiz")
                                ->equalTo($codeQuiz)
                                ->getValue();

        if (!$questionsRef) {
            return redirect()->route('dashboard')->with('error', 'Soal tidak ditemukan.');
        }

        // Urutkan soal berdasarkan tingkat kesulitan
        $levels = ['easy', 'medium', 'high'];
        $sortedQuestions = [];

        foreach ($levels as $level) {
            foreach ($questionsRef as $id => $question) {
                if (($question['level_questions'] ?? 'medium') === $level) {
                    $sortedQuestions[$id] = $question;
                }
            }
        }
        $isEssayCorrect = false;
        if (request('show_feedback') && $quizType === 'Essay') {
            $isEssayCorrect = $this->checkEssayAnswer(request('selected'), $currentQuestion['correct_answer'] ?? '');
        }


        // Jika tidak ada soal yang tersedia
        if (empty($sortedQuestions)) {
            return redirect()->route('dashboard')->with('error', 'Tidak ada soal yang tersedia.');
        }

        // Jika questionId tidak valid
        if (!$questionId || !array_key_exists($questionId, $sortedQuestions)) {
            $firstQuestionId = array_key_first($sortedQuestions);
            if (!$firstQuestionId) {
                return redirect()->route('dashboard')->with('error', 'Gagal memuat soal.');
            }

            return redirect()->route('quiz.question', [
                'quizId' => $quizId,
                'questionId' => $firstQuestionId,
                'code_quiz' => $codeQuiz
            ]);
        }

        // Persiapan data untuk view
        $questionIds = array_keys($sortedQuestions);
        $currentIndex = array_search($questionId, $questionIds);
        $currentQuestion = $sortedQuestions[$questionId];

        $paginator = new LengthAwarePaginator(
            [$currentQuestion],
            count($sortedQuestions),
            1,
            $currentIndex + 1,
            [
                'path' => route('quiz.question', ['quizId' => $quizId, 'questionId' => $questionId]),
                'pageName' => 'page'
            ]
        );

        return view('pages.students.quiz.quiz', [
            'quizId' => $quizId,
            'questions' => $paginator,
            'currentQuestion' => $currentQuestion,
            'currentQuestionId' => $questionId,
            'questionIds' => $questionIds,
            'quizType' => $quizType,
            'sortedQuestions' => $sortedQuestions, // Tambahkan sortedQuestions ke view
            'isEssayCorrect' => $isEssayCorrect,

        ]);
    }


    public function submitAnswer(Request $request, $quizId, $questionId)
{
    // 1. Authentication and Data Validation
    $userId = session('user_id');
    if (!$userId) {
        return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
    }

    // Validate that the submitted question ID matches the route parameter
    $submittedQuestionId = $request->input('id_questions');
    if ($submittedQuestionId !== $questionId) {
        return redirect()->back()->with('error', 'Invalid question ID');
    }

    // 2. Get Quiz and Question Data
    $quizRef = $this->database->getReference("quizs/{$quizId}")->getValue();
    $questionRef = $this->database->getReference("questions/{$questionId}")->getValue();

    // 3. Validate Resources
    if (!$quizRef) {
        return redirect()->route('dashboard')->with('error', 'Quiz tidak ditemukan');
    }

    if (!$questionRef) {
        return redirect()->route('dashboard')->with('error', 'Soal tidak ditemukan');
    }

    // 4. Process Answer
    $isCorrect = false;
    $quizType = $quizRef['type_quiz'] ?? 'Multiple Choice';
    $selectedAnswer = '';

    // Multiple Choice/True False Handling
    if (in_array($quizType, ['Multiple Choice', 'True False'])) {
        $selectedAnswer = $request->input('selected_option');
        $correctAnswer = $questionRef['correct_answer'] ?? null;

        if ($quizType === 'True False') {
            $isCorrect = strtolower(trim((string) $selectedAnswer)) === strtolower(trim((string) $correctAnswer));
        } else {
            $isCorrect = trim((string) $selectedAnswer) == trim((string) $correctAnswer);
        }

        \Log::info("MC/TF Question - ID: {$questionId}, Selected: {$selectedAnswer}, Correct: {$correctAnswer}, IsCorrect: " . ($isCorrect ? 'true' : 'false'));
    }
    // Essay Handling
    elseif ($quizType === 'Essay') {
        $selectedAnswer = $request->input('essay_answer');
        $correctAnswer = $questionRef['correct_answer'] ?? '';

        if (empty($selectedAnswer)) {
            return redirect()->back()->withErrors(['essay_answer' => 'Jawaban tidak boleh kosong']);
        }

        $isCorrect = $this->checkEssayAnswer($selectedAnswer, $correctAnswer);

        \Log::info("ESSAY Question - ID: {$questionId}, Answer: {$selectedAnswer}, Correct: {$correctAnswer}, IsCorrect: " . ($isCorrect ? 'true' : 'false'));
    }

    // 5. Save Attempt (dengan time_taken)
    $timeTaken = $request->input('time_taken', 0);


    // dd($timeTaken);
    $this->saveAnswerAttempt($userId, $quizId, $questionId, $selectedAnswer, $isCorrect, $quizType, $timeTaken);

    if ($isCorrect) {
        $this->updateUserScore($userId, $quizId, $questionId, $timeTaken);
    }

    // 6. Determine Next Step
    $questionsRef = $this->database->getReference("questions")
                            ->orderByChild("code_quiz")
                            ->equalTo($quizRef['code_quiz'])
                            ->getValue();

    $levels = ['easy', 'medium', 'high'];
    $sortedQuestions = [];

    foreach ($levels as $level) {
        foreach ($questionsRef as $id => $question) {
            if (($question['level_questions'] ?? 'medium') === $level) {
                $sortedQuestions[$id] = $question;
            }
        }
    }

    // Check if this is the last question
    $isLastQuestion = $this->isLastQuestion($questionId, $sortedQuestions);

    if ($isLastQuestion) {
        // Update attempt_quizs status to Done before redirecting
        $this->markQuizAsDone($userId, $quizId);
        return redirect()->route('quiz.completed', ['quizId' => $quizId]);
    }

    $nextQuestionId = $this->getNextQuestionId($questionRef['code_quiz'], $questionId, $sortedQuestions);

    if ($isCorrect) {
        return $nextQuestionId
            ? redirect()->route('quiz.question', [
                'quizId' => $quizId,
                'questionId' => $nextQuestionId,
                'code_quiz' => $questionRef['code_quiz']
            ])
            : redirect()->route('quiz.completed', ['quizId' => $quizId]);
    } else {
        $feedbackData = [
            'selected' => $selectedAnswer,
            'correct' => $correctAnswer,
            'feedback' => $questionRef['feedback'] ?? 'Jawaban belum tepat'
        ];
        return $this->handleWrongAnswer($quizId, $questionId, $questionRef['code_quiz'], $feedbackData);
    }
}
private function checkEssayAnswer($userAnswer, $correctAnswer)
{
    if (empty($correctAnswer)) return null;

    similar_text(
        strtolower(trim($userAnswer)),
        strtolower(trim($correctAnswer)),
        $similarity
    );

    return $similarity >= 70;
}
private function isLastQuestion($currentQuestionId, $sortedQuestions)
{
    $questionIds = array_keys($sortedQuestions);
    $lastIndex = count($questionIds) - 1;
    return $currentQuestionId === $questionIds[$lastIndex];
}

private function markQuizAsDone($userId, $quizId)
{
    // Find the attempt_quizs record for this user and quiz
    $attemptsRef = $this->database->getReference('attempt_quizs')
        ->orderByChild('user_id')
        ->equalTo($userId)
        ->getValue();

    foreach ($attemptsRef as $key => $attempt) {
        if ($attempt['quiz_id'] == $quizId && ($attempt['status'] ?? '') !== 'completed') {
            // Update the status to Done
            $this->database->getReference("attempt_quizs/{$key}")
                ->update([
                    'status' => 'completed',
                    'completed_at' => now()->toDateTimeString()
                ]);
            break;
        }
    }
}
private function handleWrongAnswer($quizId, $questionId, $codeQuiz, $feedbackData)
{
    return redirect()->route('quiz.question', [
        'quizId' => $quizId,
        'questionId' => $questionId,
        'code_quiz' => $codeQuiz,
        'selected' => $feedbackData['selected'],
        'show_feedback' => true
    ])->withErrors(['essay_error' => $feedbackData['feedback']]);
}
private function saveAnswerAttempt($userId, $quizId, $questionId, $answer, $isCorrect, $quizType = null, $timeTaken = 0)
{
    $answerData = [
        'id_user' => $userId,
        'id_quiz' => $quizId,
        'id_question' => $questionId,
        'is_correct' => $isCorrect,
        'answered_at' => now()->toDateTimeString(),
        'time_taken' => $timeTaken  // Add time_taken to the answer data
    ];

    // Store answer based on quiz type
    if ($quizType === 'Essay') {
        $answerData['essay_answer'] = $answer;
    } else {
        $answerData['selected_option'] = $answer;
    }

    $this->database->getReference('answers')->push($answerData);

    if ($isCorrect) {
        $this->updateUserScore($userId, $quizId, $questionId, $timeTaken);
    }
}

private function updateUserScore($userId, $quizId, $questionId, $timeTaken)
{
    // Dapatkan data pertanyaan
    $questionRef = $this->database->getReference("questions/{$questionId}")->getValue();
    $timer = $questionRef['timer'] ?? 60; // Default 60 detik jika tidak ada
    $baseScore = $questionRef['score_question'] ?? 0;

    // Hitung skor berdasarkan waktu
    $timeTake = $timer - $timeTaken  ;
    // dd($timeTake);
    $timeRatio = $timeTake / $timer;
    $scoreMultiplier = max(0.2, 1 - ($timeRatio * 0.8)); // Minimal 20% skor
    $calculatedScore = $scoreMultiplier * $baseScore;

    // Bulatkan skor ke 1 desimal
    $finalScore = round($calculatedScore, 1);

    // Update skor di attempt_quizs
    $attemptsRef = $this->database->getReference("attempt_quizs")
        ->orderByChild('user_id')
        ->equalTo($userId)
        ->getValue();

    foreach ($attemptsRef as $key => $attempt) {
        if ($attempt['quiz_id'] === $quizId) {
            $currentScore = $attempt['score'] ?? 0;
            $this->database->getReference("attempt_quizs/{$key}")->update([
                'score' => $currentScore + $finalScore,
                'details' => [
                    $questionId => [
                        'base_score' => $baseScore,
                        'time_taken' => $timeTake,
                        'calculated_score' => $finalScore
                    ]
                ]
            ]);
            break;
        }
    }

    \Log::info("Score calculation", [
        'user_id' => $userId,
        'question_id' => $questionId,
        'time_taken' => $timeTake,
        'timer' => $timer,
        'base_score' => $baseScore,
        'final_score' => $finalScore,
        'calculation' => "((1-($timeTake/$timer)*0.8)*$baseScore)"
    ]);
}
    private function getNextQuestionId($codeQuiz, $currentQuestionId, $sortedQuestions)
    {
        $questionIds = array_keys($sortedQuestions);
        $currentIndex = array_search($currentQuestionId, $questionIds);

        // Ambil soal berikutnya berdasarkan urutan yang sudah di-sort
        return $questionIds[$currentIndex + 1] ?? null;
    }

    public function joinQuiz(Request $request)
{
    $request->validate([
        'code_quiz' => 'required|string'
    ]);

    $quizCode = $request->code_quiz;
    $userId = session('user_id');

    // Validasi session user
    if (!$userId) {
        return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
    }

    // Cari quiz berdasarkan kode
    $quizRef = $this->database->getReference('quizs')
                    ->orderByChild('code_quiz')
                    ->equalTo($quizCode)
                    ->getValue();

    if (empty($quizRef)) {
        return redirect()->back()
               ->withInput()
               ->with('error', 'Kode quiz tidak valid atau tidak ditemukan.');
    }

    $quizId = array_key_first($quizRef);
    $quiz = $quizRef[$quizId];

    // Validasi status quiz
    if (!isset($quiz['status']) || $quiz['status'] !== 'Ongoing') {
        return redirect()->back()
               ->withInput()
               ->with('error', 'Quiz belum dimulai atau sudah selesai.');
    }

    // Cek attempt sebelumnya
    $attemptRef = $this->database->getReference('attempt_quizs')
                    ->orderByChild('user_id')
                    ->equalTo($userId)
                    ->getValue();

    $existingAttempt = null;
    if (!empty($attemptRef)) {
        foreach ($attemptRef as $key => $attempt) {
            if (isset($attempt['quiz_id']) && $attempt['quiz_id'] == $quizId) {
                $existingAttempt = $key;
                break;
            }
        }
    }

    // Buat attempt baru jika belum ada
    if (!$existingAttempt) {
        $newAttemptRef = $this->database->getReference('attempt_quizs')->push([
            'user_id' => $userId,
            'quiz_id' => $quizId,
            'start_time' => now()->toDateTimeString(),
            'status' => 'in_progress',
            'score' => 0
        ]);
        $attemptId = $newAttemptRef->getKey();
    } else {
        $attemptId = $existingAttempt;
    }

    // Ambil semua soal
    $questionsRef = $this->database->getReference("questions")
                        ->orderByChild("code_quiz")
                        ->equalTo($quizCode)
                        ->getValue();

    if (empty($questionsRef)) {
        return redirect()->back()
               ->withInput()
               ->with('error', 'Belum ada soal yang tersedia untuk quiz ini.');
    }

    // Urutkan soal berdasarkan tingkat kesulitan
    $levels = ['easy', 'medium', 'high'];
    $sortedQuestions = [];

    foreach ($levels as $level) {
        foreach ($questionsRef as $id => $question) {
            if (($question['level_questions'] ?? 'medium') === $level) {
                $sortedQuestions[$id] = $question;
            }
        }
    }

    $firstQuestionId = array_key_first($sortedQuestions);

    if (!$firstQuestionId) {
        return redirect()->back()
               ->withInput()
               ->with('error', 'Gagal memuat soal pertama.');
    }

    return redirect()->route('quiz.question', [
        'quizId' => $quizId,
        'questionId' => $firstQuestionId,
        'code_quiz' => $quizCode
    ])->with('success', 'Berhasil masuk ke quiz!');
}


    public function quizCompleted($quizId)
{
    // Ambil data attempt berdasarkan quiz_id
    $attempts = $this->database->getReference('attempt_quizs')
        ->orderByChild('quiz_id')
        ->equalTo($quizId)
        ->getValue();

    if (!$attempts) {
        return view('pages.students.quiz.completed', compact('quizId'))->withErrors('Belum ada peserta yang menyelesaikan quiz.');
    }

    // Ambil semua user dari database untuk mencocokkan user_id dengan nama
    $users = $this->database->getReference('users')->getValue();

    // Tambahkan student_name ke setiap attempt
    $attemptsWithNames = [];
    foreach ($attempts as $attemptId => $attempt) {
        $userId = $attempt['user_id'] ?? null;
        $studentName = isset($users[$userId]) ? $users[$userId]['name'] : 'Unknown'; // Ambil nama dari users

        $attempt['student_name'] = $studentName; // Tambahkan ke array attempt
        $attemptsWithNames[$attemptId] = $attempt;
    }

    // Urutkan berdasarkan skor tertinggi
    $leaderboard = collect($attemptsWithNames)->sortByDesc('score')->values()->all();

    return view('pages.students.quiz.completed', compact('quizId', 'leaderboard'));
}



}
