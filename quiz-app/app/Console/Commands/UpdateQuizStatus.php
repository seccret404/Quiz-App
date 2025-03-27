<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;
use Carbon\Carbon;

class UpdateQuizStatus extends Command
{
    protected $signature = 'quiz:update-status';
    protected $description = 'Update quiz status to Done if end_time has passed';
    protected $database;

    public function __construct()
    {
        parent::__construct();

        $firebase = (new Factory)
            ->withServiceAccount(base_path('storage/firebase/firebase.json')) // Ganti dengan path file JSON Firebase kamu
            ->withDatabaseUri('https://quiz-app-9717f-default-rtdb.firebaseio.com/'); // Ganti dengan URL database Firebase kamu

        $this->database = $firebase->createDatabase();
    }

    public function handle()
    {
        $now = Carbon::now()->format('Y-m-d\TH:i'); // Format sesuai dengan Firebase
        $quizzes = $this->database->getReference('quizs')->getValue();

        if ($quizzes) {
            foreach ($quizzes as $key => $quiz) {
                if (isset($quiz['end_time']) && isset($quiz['status'])) {
                    if ($quiz['end_time'] <= $now && $quiz['status'] === "Ongoing") {
                        $this->database->getReference("quizs/{$key}")
                            ->update(['status' => 'Done']);

                        $this->info("Quiz {$quiz['nama_quiz']} marked as Done.");
                    }
                }
            }
        }
    }
}
