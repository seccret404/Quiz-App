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
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaderboardController;
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

    Route::get('/dashboard/admin', [TeacherController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/quiz', [GenerateQuizController::class, 'index'])->name('generate');


    Route::get('/dashboard/fintech', [DashboardController::class, 'fintech'])->name('quiz');
    Route::get('/dashboard/admin/leaderboard', [LeaderboardController::class, 'leaderboard'])->name('leaderboard');
    Route::get('/dashboard/orders', [OrderController::class, 'index'])->name('logout');
    Route::get('/dashboard/invoices', [InvoiceController::class, 'index'])->name('invoices');

