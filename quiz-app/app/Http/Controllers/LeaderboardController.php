<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    function leaderboard(){
        return view('pages.teacher.leaderboard.index');
    }
}
