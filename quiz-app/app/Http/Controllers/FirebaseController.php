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


            $fields = [
                'id'          => '',
                'name'        => '',
                'email'       => '',
                'password'    => '',
                'profil_pic'  => '',
                'role'        => '',
                'created_at'  => now()->toDateTimeString(),
            ];

            $this->database->getReference('users')->set($fields);

            return response()->json([
                'message' => 'Firebase connected!!',
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
