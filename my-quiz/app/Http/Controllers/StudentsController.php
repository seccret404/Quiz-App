<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentsController extends Controller
{
    public function students_index()
    {
        return view('Layout.Admin.header');
    }
}
