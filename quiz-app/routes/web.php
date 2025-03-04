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
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\GenerateQuizController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



    Route::get('/connect',[FirebaseController::class, 'index']);


    Route::get('/login', [AuthController::class, 'LoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'Login']);
    Route::get('/register', [AuthController::class, 'RegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'Register']);


    // Route for the getting the data feed
    Route::get('/json-data-feed', [DataFeedController::class, 'getDataFeed'])->name('json_data_feed');

    // Admin
    Route::get('/dashboard/admin', [TeacherController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/quiz', [GenerateQuizController::class, 'index'])->name('generate');
    Route::post('/save-quiz', [GenerateQuizController::class, 'saveQuiz'])->name('save.quiz');
    Route::get('/dashboard/quiz-open', [GenerateQuizController::class, 'showquiz'])->name('quiz.open');
    Route::get('/dashboard/quiz-ongoing', [GenerateQuizController::class, 'showQuizOngoing'])->name('quiz.ongoing');
    Route::get('/dashboard/admin/leaderboard', [LeaderboardController::class, 'leaderboard'])->name('leaderboard');
    Route::get('/dashboard/leadeboard/soal/{quizId}', [LeaderboardController::class, 'showQuestion'])->name('quiz.question');
    Route::post('/generate-questions', [PdfQuestionController::class, 'processPDF'])->name('generate.quiz');


    // Students
    Route::get('/student/home', [StudentsController::class,'home'])->name('home.student');
    Route::get('/student/dashboard', [StudentsController::class,'dashboard'])->name('my.dashboard');
