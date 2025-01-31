<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentsController;

// Route::get('/', function () {
//     return view('welcome');
// });

// Auth
Route::get('/', [AuthController::class, 'LoginForm']);
Route::get('/register-form', [AuthController::class, 'RegisterForm']);
Route::post('/register', [AuthController::class, 'Register']);

// Students
Route::get('/students-index', [StudentsController::class, 'students_index']);
