<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FirebaseConnectionController;


Route::get('/',[FirebaseConnectionController::class,'index']);
