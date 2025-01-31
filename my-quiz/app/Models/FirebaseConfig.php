<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kreait\Firebase\Factory;
class FirebaseConfig extends Model
{
  protected $database;

  public function __construct(){

    $path = base_path('storage/firebase/firebase.json');

    if(!file_exists($path)){
        die(`{$path} not found`);
    }

        $factory = (new Factory)
        ->withServiceAccount($path)
        ->withDatabaseUri('https://quiz-app-9717f-default-rtdb.firebaseio.com/');

        $this->database = $factory->createDatabase();

    }

    public function getDatabase(){
        return $this->database;
    }
}
