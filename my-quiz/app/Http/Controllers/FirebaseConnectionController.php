<?php

namespace App\Http\Controllers;
use Exception;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use App\Models\FirebaseConfig;

class FirebaseConnectionController extends Controller
{

    protected $database;

    public function __construct(FirebaseConfig $firebase)
    {
        $this->database = $firebase->getDatabase();
    }


    public function index()
    {
        try {
            $this->database->getReference('users')->set([]);//set node kosong on databse its a table


            $field = $this->database->getReference('users/id')->set(''); //set key node or on databse its a column


            $idUser = $field->getKey();// on developmet we use it for key user


            return response()->json([
                'message' => 'Node "User" added successfully',

            ]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


}
