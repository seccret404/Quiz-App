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

        // Ambil data quiz berdasarkan code_quiz
        $quizRef = $this->database->getReference("quizs")
                            ->orderByChild("code_quiz")
                            ->equalTo($codeQuiz)
                            ->getValue();

        if (!$quizRef) {
            return redirect()->route('dashboard')->with('error', 'Quiz tidak ditemukan.');
        }

        // Ambil hanya satu quiz (karena code_quiz harus unik)
        $quizData = reset($quizRef);
        $quizType = $quizData['type_quiz'] ?? 'Multiple Choice'; // Default ke pilihan ganda

        // Ambil semua soal berdasarkan `code_quiz`
        $questionsRef = $this->database->getReference("questions")
                                ->orderByChild("code_quiz")
                                ->equalTo($codeQuiz)
                                ->getValue();

        if (!$questionsRef) {
            return redirect()->route('dashboard')->with('error', 'Soal tidak ditemukan.');
        }

        // Ubah menjadi array numerik
        $questions = array_values($questionsRef);
        $questionIds = array_keys($questionsRef);

        // Validasi level_questions hanya menerima 'easy', 'medium', atau 'high'
        $validLevels = ['easy', 'medium', 'high'];

        // Urutkan soal berdasarkan tingkat kesulitan
        usort($questions, function ($a, $b) use ($validLevels) {
            $order = ['easy' => 1, 'medium' => 2, 'high' => 3];

            $difficultyA = in_array($a['level_questions'] ?? 'medium', $validLevels) ? $a['level_questions'] : 'medium';
            $difficultyB = in_array($b['level_questions'] ?? 'medium', $validLevels) ? $b['level_questions'] : 'medium';

            return $order[$difficultyA] <=> $order[$difficultyB];
        });

        // Pastikan `questionId` ada, jika tidak, ambil soal pertama
        if (!$questionId || !in_array($questionId, $questionIds)) {
            return redirect()->route('quiz.question', [
                'quizId' => $quizId,
                'questionId' => array_keys($questionsRef)[0], // Soal pertama setelah diurutkan
                'code_quiz' => $codeQuiz
            ]);
        }

        // Cari index dari soal saat ini berdasarkan `questionId`
        $currentIndex = array_search($questionId, $questionIds);
        $perPage = 1;

        // Ambil soal saat ini
        $currentQuestion = $questions[$currentIndex] ?? null;

        // Pagination
        $paginator = new LengthAwarePaginator([$currentQuestion], count($questions), $perPage, $currentIndex + 1, [
            'path' => route('quiz.question', [
                'quizId' => $quizId,
                'questionId' => $questionId
            ]) . '?code_quiz=' . $codeQuiz
        ]);

        return view('pages.students.quiz.quiz', [
            'quizId' => $quizId,
            'questions' => $paginator,
            'currentQuestion' => $currentQuestion,
            'currentQuestionId' => $questionId,
            'questionIds' => $questionIds,
            'quizType' => $quizType, // Kirim tipe quiz ke Blade
        ]);
    }


    public function submitAnswer(Request $request, $quizId, $questionId)
    {
        $userId = session('user_id');
        $selectedAnswer = $request->input('selected_option');
        $essayAnswer = $request->input('essay_answer');
        $isCorrect = false;
        $feedback = null;
        $scoreToAdd = 0;

        $quizRef = $this->database->getReference("quizs/{$quizId}")->getValue();
        if (!$quizRef) return redirect()->route('dashboard')->with('error', 'Quiz tidak ditemukan.');

        $quizType = $quizRef['type'] ?? 'Multiple Choice';
        $questionRef = $this->database->getReference("questions/{$questionId}")->getValue();
        if (!$questionRef) return redirect()->route('dashboard')->with('error', 'Soal tidak ditemukan.');

        if ($quizType === 'Multiple Choice' || $quizType === 'True False') {
            $correctAnswer = $questionRef['correct_answer'] ?? null;
            $scoreToAdd = $questionRef['score_question'] ?? 0;

            if ($selectedAnswer === $correctAnswer) {
                $isCorrect = true;
            } else {
                $feedback = $questionRef['feedback'] ?? 'Jawaban salah! Coba lagi dengan lebih teliti.';
            }

            $answerData = [
                'id_user' => $userId,
                'id_quiz' => $quizId,
                'id_question' => $questionId,
                'selected_option' => $selectedAnswer,
                'is_correct' => $isCorrect,
            ];
        }

        elseif ($quizType === 'Essay') {
            if (!$essayAnswer) {
                return redirect()->back()->withErrors(['feedback' => 'Jawaban tidak boleh kosong.']);
            }

            $correctEssayAnswer = $questionRef['correct_answer'] ?? null;
            $feedback = $questionRef['feedback'] ?? 'Jawaban telah disimpan untuk dinilai secara manual.';

            if ($correctEssayAnswer) {
                similar_text(strtolower(trim($essayAnswer)), strtolower(trim($correctEssayAnswer)), $similarity);
                $isCorrect = $similarity >= 70;
            } else {
                $isCorrect = null;
            }

            $answerData = [
                'id_user' => $userId,
                'id_quiz' => $quizId,
                'id_question' => $questionId,
                'essay_answer' => $essayAnswer,
                'is_correct' => $isCorrect
            ];
        }

        // Simpan jawaban ke database, baik benar maupun salah
        $this->database->getReference('answers')->push($answerData);

        // Update skor jika jawaban benar
        if (($quizType === 'Multiple Choice' || $quizType === 'True False') && $isCorrect) {
            $attemptsRef = $this->database->getReference("attempt_quizs")->getValue();
            $attemptId = null;

            foreach ($attemptsRef as $key => $attempt) {
                if ($attempt['quiz_id'] === $quizId && $attempt['user_id'] === $userId) {
                    $attemptId = $key;
                    break;
                }
            }

            if ($attemptId) {
                $attemptRef = $this->database->getReference("attempt_quizs/{$attemptId}");
                $currentScore = $attemptRef->getChild('score')->getValue() ?? 0;
                $newScore = $currentScore + $scoreToAdd;
                $attemptRef->update(['score' => $newScore]);
            }
        }

        // Cek soal berikutnya
        $questionsRef = $this->database->getReference("questions")
            ->orderByChild("code_quiz")
            ->equalTo($questionRef['code_quiz'])
            ->getValue();

        if (!$questionsRef) {
            return redirect()->route('quiz.completed', ['quizId' => $quizId])
                ->with('success', 'Quiz telah selesai!');
        }

        $questionKeys = array_keys($questionsRef);
        $currentIndex = array_search($questionId, $questionKeys);
        $nextQuestionId = $currentIndex !== false && isset($questionKeys[$currentIndex + 1])
            ? $questionKeys[$currentIndex + 1]
            : null;

        // **PERBAIKAN UTAMA DI SINI**
        if (!$nextQuestionId) {
            return redirect()->route('quiz.completed', ['quizId' => $quizId])
                ->with('success', 'Quiz telah selesai!');
        }

        // **PERBAIKAN LAINNYA: CEGAH REDIRECT BACK PADA SOAL TERAKHIR JIKA JAWABAN SALAH**
        if (!$isCorrect && $nextQuestionId) {
            return redirect()->back()->withErrors(['feedback' => $feedback])->with('next', true);
        }

        return redirect()->route('quiz.question', [
            'quizId' => $quizId,
            'questionId' => $nextQuestionId,
            'code_quiz' => $questionRef['code_quiz']
        ]);
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
