<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Firebase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class FirebaseController extends Controller
{
    protected $database;

    public function __construct(Firebase $firebase)
    {
        $this->database = $firebase->getDatabase();
    }

    public function index()
    {
        try {

            $this->database->getReference('users')->set([]);


            $userFields = [
                'id'          => '',
                'name'        => '',
                'email'       => '',
                'password'    => '',
                'profil_pic'  => '',
                'role'        => '',
                'created_at'  => now()->toDateTimeString(),
            ];

            $this->database->getReference('users')->set($userFields);

            $quizFields = [
                'id'             => '',
                'name_quiz'      => '',
                'type_quiz'      => '',
                'code_quiz'      => '',
                'total_question' => 0,
                'status'         => 'Open',
                'start_time'     => '',
                'end_time'       => '',
                'created_at'     => now()->toDateTimeString(),
            ];

            $this->database->getReference('quizs')->push($quizFields);

            $questionFields = [
                'id'              => '',
                'id_quiz'         => '',
                'code_quiz'       => '',
                'question'        => '',
                'options'         => [],
                'level_questions' => '',
                'correct_answer'  => '',
                'feedback'        => '',
                'score_question'  => 0,
                'timer'           => 0,
                'created_at'      => now()->toDateTimeString(),
            ];

            $this->database->getReference('questions')->push($questionFields);

            return response()->json([
                'message' => 'Firebase connected!!',
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
