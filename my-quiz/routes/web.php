<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentsController;
<<<<<<< HEAD
use App\Http\Controllers\FirebaseConnectionController;
=======
>>>>>>> a6a8f127245a5e54acc72a6d6f354144bbcc0aed

// Route::get('/', function () {
//     return view('welcome');
// });

// Auth
<<<<<<< HEAD
Route::get('/login', [AuthController::class, 'LoginForm']);
=======
Route::get('/', [AuthController::class, 'LoginForm']);
>>>>>>> a6a8f127245a5e54acc72a6d6f354144bbcc0aed
Route::get('/register-form', [AuthController::class, 'RegisterForm']);
Route::post('/register', [AuthController::class, 'Register']);

// Students
Route::get('/students-index', [StudentsController::class, 'students_index']);
<<<<<<< HEAD


// Route::get('/',[FirebaseConnectionController::class,'index']);
=======
>>>>>>> a6a8f127245a5e54acc72a6d6f354144bbcc0aed
