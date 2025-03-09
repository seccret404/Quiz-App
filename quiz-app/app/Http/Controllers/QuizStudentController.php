<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QuizStudentController extends Controller
{
    public function quizPage(){
        return view('pages.students.quiz.quiz');
    }
}
