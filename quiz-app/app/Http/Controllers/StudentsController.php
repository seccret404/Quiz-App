<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentsController extends Controller
{
    public function home(){
        return view('pages.students.Home.home');
    }

    public function dashboard(){
        return view('pages.students.Home.dashboard');
    }
}
