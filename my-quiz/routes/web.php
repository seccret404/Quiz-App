<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\FirebaseConnectionController;

// Route::get('/', function () {
//     return view('welcome');
// });

// Auth
// Route::get('/', [AuthController::class, 'LoginForm']);
Route::get('/db', [FirebaseConnectionController::class, 'index']);

Route::get('/register-form', [AuthController::class, 'RegisterForm']);
Route::post('/register', [AuthController::class, 'Register']);


Route::get('/login', [AuthController::class, 'LoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'Login']);

// Students
Route::get('/admin', [StudentsController::class, 'students_index'])->name('students_index');



