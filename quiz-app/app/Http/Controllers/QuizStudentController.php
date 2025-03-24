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
        $userId = session('user_id'); // ID user yang sedang login
        $codeQuiz = $request->query('code_quiz');

        if (!$codeQuiz) {
            return redirect()->route('dashboard')->with('error', 'Code quiz tidak ditemukan.');
        }

        // Ambil semua soal berdasarkan `code_quiz`
        $questionsRef = $this->database->getReference("questions")
                            ->orderByChild("code_quiz")
                            ->equalTo($codeQuiz)
                            ->getValue();

        if (!$questionsRef) {
            return redirect()->route('dashboard')->with('error', 'Soal tidak ditemukan.');
        }

        // Urutkan soal berdasarkan key Firebase
        $questions = array_values($questionsRef);
        $questionIds = array_keys($questionsRef);

        // Pastikan `questionId` ada, jika tidak, ambil soal pertama
        if (!$questionId || !in_array($questionId, $questionIds)) {
            return redirect()->route('quiz.question', [
                'quizId' => $quizId,
                'questionId' => reset($questionIds), // Soal pertama
                'code_quiz' => $codeQuiz
            ]);
        }

        // Cari index dari soal saat ini berdasarkan `questionId`
        $currentIndex = array_search($questionId, $questionIds);
        $perPage = 1;

        // Pagination
        $paginator = new LengthAwarePaginator([$questions[$currentIndex]], count($questions), $perPage, $currentIndex + 1, [
            'path' => route('quiz.question', [
                'quizId' => $quizId,
                'questionId' => $questionId
            ]) . '?code_quiz=' . $codeQuiz
        ]);

        return view('pages.students.quiz.quiz', [
            'quizId' => $quizId,
            'questions' => $paginator,
            'currentQuestion' => $questions[$currentIndex] ?? null, // Soal saat ini
            'currentQuestionId' => $questionId, // ID soal saat ini
            'questionIds' => $questionIds, // List semua ID soal
        ]);
    }

    public function submitAnswer(Request $request, $quizId, $questionId)
    {
        $userId = session('user_id'); // ID pengguna yang sedang login
        $selectedAnswer = $request->input('selected_option');
        $isCorrect = false; // Default jawaban salah
        $feedback = null; // Default feedback kosong

        // Ambil soal saat ini
        $questionRef = $this->database->getReference("questions/{$questionId}")->getValue();

        if (!$questionRef) {
            return redirect()->route('dashboard')->with('error', 'Soal tidak ditemukan.');
        }

        // Dapatkan jawaban benar dari soal
        $correctAnswer = $questionRef['correct_answer'] ?? null;
        $scoreToAdd = $questionRef['score_question'] ?? 0; // Ambil skor soal

        // Cek apakah jawaban benar
        if ($selectedAnswer === $correctAnswer) {
            $isCorrect = true;
        } else {
            $feedback = $questionRef['feedback'] ?? 'Jawaban salah! Coba lagi dengan lebih teliti.';
        }

        // Simpan jawaban pengguna ke database
        $answerQuizFields = [
            'id_user' => $userId,
            'id_quiz' => $quizId,
            'id_question' => $questionId,
            'selected_option' => $selectedAnswer,
            'is_correct' => $isCorrect,
        ];
        $this->database->getReference('answers')->push($answerQuizFields);

        // **UPDATE SCORE DI NODE attempt_quizs**
        $attemptsRef = $this->database->getReference("attempt_quizs")->getValue();
        $attemptId = null;

        // Cari attempt berdasarkan quiz_id dan user_id
        foreach ($attemptsRef as $key => $attempt) {
            if ($attempt['quiz_id'] === $quizId && $attempt['user_id'] === $userId) {
                $attemptId = $key;
                break;
            }
        }

        // Jika attempt ditemukan, update score
        if ($attemptId && $isCorrect) {
            $attemptRef = $this->database->getReference("attempt_quizs/{$attemptId}");
            $currentScore = $attemptRef->getChild('score')->getValue() ?? 0;
            $newScore = $currentScore + $scoreToAdd;

            // Update skor di Firebase
            $attemptRef->update(['score' => $newScore]);
        }

        // Ambil semua soal berdasarkan `code_quiz`
        $questionsRef = $this->database->getReference("questions")
            ->orderByChild("code_quiz")
            ->equalTo($questionRef['code_quiz'])
            ->getValue();

        if (!$questionsRef) {
            return redirect()->route('quiz.completed', ['quizId' => $quizId])
                ->with('success', 'Quiz telah selesai!');
        }

        // Urutkan soal berdasarkan Firebase key
        $questionKeys = array_keys($questionsRef);
        $currentIndex = array_search($questionId, $questionKeys);

        // Tentukan soal berikutnya
        $nextQuestionId = $currentIndex !== false && isset($questionKeys[$currentIndex + 1])
            ? $questionKeys[$currentIndex + 1]
            : null;

        // Jika jawaban salah, tetap di halaman yang sama dan tampilkan feedback
        if (!$isCorrect) {
            return redirect()->back()->withErrors(['feedback' => $feedback]);
        }

        if (!$nextQuestionId) {
            return redirect()->route('quiz.completed', ['quizId' => $quizId])
                ->with('success', 'Quiz telah selesai!');
        }

        // Jika benar, lanjut ke soal berikutnya atau selesai
        if ($nextQuestionId) {
            return redirect()->route('quiz.question', [
                'quizId' => $quizId,
                'questionId' => $nextQuestionId,
                'code_quiz' => $questionRef['code_quiz']
            ]);
        } else {
            return redirect()->route('quiz.completed', ['quizId' => $quizId])
                ->with('success', 'Quiz telah selesai!');
        }
    }

    public function joinQuiz(Request $request)
    {
        $request->validate([
            'code_quiz' => 'required|string'
        ]);

        $quizCode = $request->code_quiz;

        // find quiz -> code_quiz
        $quizRef = $this->database->getReference('quizs')
                                ->orderByChild('code_quiz')
                                ->equalTo($quizCode)
                                ->getValue();

        // if err
        if (empty($quizRef)) {
            return redirect()->back()->with('error', 'Quiz tidak ditemukan.');
        }

        // if not err, take quiz_id
        $quizId = array_key_first($quizRef);
        $quiz = $quizRef[$quizId];

        //cek duplicate
        if (!isset($quiz['status']) || $quiz['status'] !== 'Ongoing') {
            return redirect()->back()->with('error', 'Quiz belum dimulai.');
        }

        $userId = session('user_id'); //take id student

        // Check duplicate id student
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

        // push if not duplicate
        if (!$existingAttempt) {
            $newAttemptRef = $this->database->getReference('attempt_quizs')->push([
                'user_id' => $userId,
                'quiz_id' => $quizId,
                'start_time' => now()->toDateTimeString(),
                'status' => 'in_progress',
                'score' => 0
            ]);
            // dd($newAttemptRef);
            $attemptId = $newAttemptRef->getKey();
        } else {
            $attemptId = $existingAttempt;
        }

        // Ambil soal pertama
        $questionsRef = $this->database->getReference("questions")
                            ->orderByChild("code_quiz")
                            ->equalTo($quizCode)
                            ->getValue();

        if (empty($questionsRef)) {
            return redirect()->back()->with('error', 'Tidak ada soal dalam quiz ini.');
        }

        $firstQuestionId = array_key_first($questionsRef);

        return redirect()->route('quiz.question', [
            'quizId' => $quizId,
            'questionId' => $firstQuestionId,
            'code_quiz' => $quizCode
        ]);

    }

    public function quizCompleted($quizId)
{
    // Ambil data attempt berdasarkan quiz_id dan urutkan berdasarkan skor tertinggi
    $attempts = $this->database->getReference('attempt_quizs')
        ->orderByChild('quiz_id')
        ->equalTo($quizId)
        ->getValue();

    // Urutkan leaderboard berdasarkan skor tertinggi
    $leaderboard = collect($attempts)->sortByDesc('score')->values()->all();

    return view('pages.students.quiz.completed', compact('quizId', 'leaderboard'));
}


}
