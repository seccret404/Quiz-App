<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DataFeedController;
use App\Http\Controllers\FirebaseController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\PdfQuestionController;
use App\Http\Controllers\QuizStudentController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\GenerateQuizController;


Route::get('/connect', [FirebaseController::class, 'index']);


Route::get('/', [AuthController::class, 'LoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'Login']);
Route::get('/register', [AuthController::class, 'RegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'Register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'admin'])->group(function () {
    // Admin
    Route::get('/dashboard/admin', [TeacherController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/quiz', [GenerateQuizController::class, 'index'])->name('generate');
    Route::post('/save-quiz', [GenerateQuizController::class, 'saveQuiz'])->name('save.quiz');
    Route::get('/dashboard/quiz-open', [GenerateQuizController::class, 'showquiz'])->name('quiz.open');
    Route::get('/dashboard/quiz-ongoing', [GenerateQuizController::class, 'showQuizOngoing'])->name('quiz.ongoing');
    Route::get('/dashboard/admin/leaderboard', [LeaderboardController::class, 'leaderboard'])->name('leaderboard');
    Route::get('/quiz/questions-detail/{quizId}', [LeaderboardController::class, 'showQuestions'])->name('quiz.questions-detail');
    Route::post('/generate-questions', [PdfQuestionController::class, 'processPDF'])->name('generate.quiz');
    Route::get('/quiz/leaderboards/{quizId}', [LeaderboardController::class, 'showLeaderboard'])->name('quiz.leaderboards');

    Route::get('/quiz/attempts/data', [TeacherController::class, 'getQuizAttemptsData'])
    ->name('quiz.attempts.data');
    //quiz
    Route::post('/student/quiz/{id}', [GenerateQuizController::class, 'quizStart'])->name('quiz.start');


});

Route::middleware(['auth', 'user'])->group(function () {
    Route::get('/student/home', [StudentsController::class, 'home'])->name('home.student');
    Route::get('/student/dashboard', [StudentsController::class, 'dashboard'])->name('my.dashboard');

    //quiz
    Route::post('/quiz/join', [QuizStudentController::class, 'joinQuiz'])->name('quiz.join');
  // routes/web.php
Route::get('/student/quiz/{quizId}', [QuizStudentController::class, 'redirectToFirstQuestion'])
->name('quiz.start');

Route::get('/student/quiz/{quizId}/{questionId}', [QuizStudentController::class, 'quizPage'])
->name('quiz.question');
    Route::post('/student/quiz/{quizId}/{questionId}/answer', [QuizStudentController::class, 'submitAnswer'])->name('quiz.answer');
    Route::get('/student/quiz/{quizId}/completed', [QuizStudentController::class, 'completed'])->name('quiz.completed');
    Route::get('/quiz/{quizId}/completed', [QuizStudentController::class, 'quizCompleted'])->name('quiz.completed');

    Route::get('/quiz/questions/{quizId}', [StudentsController::class, 'showQuestions'])->name('quiz.questions');
    Route::get('/quiz/leaderboard/{quizId}', [StudentsController::class, 'showLeaderboard'])->name('quiz.leaderboard');

});
