<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TeacherController extends Controller
{
    function index(){
        return view('pages.teacher.Home.index');
    }
}
