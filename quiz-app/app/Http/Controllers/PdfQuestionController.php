<?php

namespace App\Http\Controllers;

use Log;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Http;

class PdfQuestionController extends Controller
{
    private function extractTextFromPDF($pdfPath)
    {
        //extract pdf to text
        $parser = new Parser();
        $pdf    = $parser->parseFile($pdfPath);
        return $pdf->getText();
    }

    private function generateQuestions($text, $totalQuestions, $questionType, $difficultyLevel)
    {
        //persipan API GEMINI

        $apiKey = env('GEMINI_API_KEY');

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=$apiKey";
        // $prompt = "Buatkan $totalQuestions soal tipe $questionType berdasarkan teks berikut kedalam format array [] pisahkan soal dengan option nya dan buat feedback(penjelasan) terhadap jawaban yang benar tersebut:\n" . $text;

        if ($questionType == "Multiple Choice") {
            $prompt = "Buatkan $totalQuestions soal pilihan ganda dengan format JSON yang valid.
            Setiap soal memiliki pertanyaan, 4 opsi jawaban (A, B, C, D), jawaban yang benar, dan feedback untuk jawaban yang benar.
            Semua soal yang dihasilkan harus memiliki tingkat kesulitan: $difficultyLevel.

                - Jika tingkat kesulitan adalah 'easy', buatlah pertanyaan yang sederhana dan jawaban yang langsung jelas.
                - Jika tingkat kesulitan adalah 'medium', buatlah pertanyaan yang membutuhkan pemahaman konsep yang lebih dalam.
                - Jika tingkat kesulitan adalah 'high', buatlah pertanyaan yang menantang dan membutuhkan analisis atau aplikasi konsep yang kompleks.

            Gunakan format berikut:

            [
            {
                \"question\": \"Apa ibukota Indonesia?\",
                \"options\": { \"A\": \"Jakarta\", \"B\": \"Surabaya\", \"C\": \"Bandung\", \"D\": \"Medan\" },
                \"answer\": \"A\",
                \"feedback\": \"Jakarta adalah ibukota Indonesia sejak tahun 1945.\"
            },
            ...
            ]

            Gunakan teks berikut sebagai referensi untuk membuat soal:\n" . $text;
        } elseif ($questionType == "Essay") {
            $prompt = "Buatkan $totalQuestions soal essay dengan format JSON yang valid.
            Setiap soal memiliki pertanyaan, jawaban yang benar, dan feedback untuk jawaban yang benar.
            Semua soal yang dihasilkan harus memiliki tingkat kesulitan: $difficultyLevel.

                - Jika tingkat kesulitan adalah 'easy', buatlah pertanyaan yang sederhana dan jawaban yang singkat.
                - Jika tingkat kesulitan adalah 'medium', buatlah pertanyaan yang membutuhkan penjelasan konsep yang lebih mendalam.
                - Jika tingkat kesulitan adalah 'high', buatlah pertanyaan yang menantang dan membutuhkan analisis atau sintesis informasi yang kompleks.

            Gunakan format berikut:

            [
            {
                \"question\": \"Jelaskan bagaimana proses fotosintesis berlangsung?\",
                \"answer\": \"Fotosintesis adalah proses di mana tumbuhan menggunakan sinar matahari untuk mengubah karbon dioksida dan air menjadi glukosa dan oksigen...\",
                \"feedback\": \"Jawaban yang tepat harus mencakup penjelasan tentang klorofil, cahaya matahari, dan proses kimia yang terjadi.\"
            },
            ...
            ]

            Gunakan teks berikut sebagai referensi untuk membuat soal:\n" . $text;
        } elseif ($questionType == "True False") {
            $prompt = "Buatkan $totalQuestions soal true/false dengan format JSON yang valid.
            Setiap soal memiliki pertanyaan, jawaban (True atau False), dan feedback mengapa jawaban tersebut benar atau salah.
            Semua soal yang dihasilkan harus memiliki tingkat kesulitan: $difficultyLevel.

                - Jika tingkat kesulitan adalah 'easy', buatlah pertanyaan yang sederhana dan jawaban yang langsung jelas.
                - Jika tingkat kesulitan adalah 'medium', buatlah pertanyaan yang membutuhkan pemahaman konsep yang lebih dalam.
                - Jika tingkat kesulitan adalah 'high', buatlah pertanyaan yang menantang dan membutuhkan analisis atau aplikasi konsep yang kompleks.

            Gunakan format berikut:

            [
            {
                \"question\": \"Matahari mengelilingi bumi.\",
                \"answer\": \"False\",
                \"feedback\": \"Matahari tidak mengelilingi bumi. Sebaliknya, bumi yang mengelilingi matahari.\"
            },
            ...
            ]

            Gunakan teks berikut sebagai referensi untuk membuat soal:\n" . $text;
        } else {
            return redirect()->back()->with('error', 'Tipe soal tidak valid. Pilih antara "Multiple Choice", "Essay", atau "True False".');
        }


        //Persiapan pengiriman Prompt
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($url, [
            "contents" => [
                [
                    "parts" => [
                        [
                            "text" => $prompt
                        ]
                    ]
                ]
            ]
        ]);

        return $response->json();
    }

    public function processPDF(Request $request)
    {
        try {
            $request->validate([
                'pdf' => 'required|mimes:pdf|max:2048',
                'total_questions' => 'required|integer|min:1',
                'question_type' => 'required|string',
                'difficulty_level' => 'required|in:easy,medium,high',
            ]);

            // Simpan file PDF ke storage
            $pdfPath = $request->file('pdf')->store('uploads');

            // Ekstrak teks dari PDF
            $text = $this->extractTextFromPDF(storage_path("app/$pdfPath"));

            // Kirim teks ke API Gemini untuk generate soal
            $response = $this->generateQuestions($text, $request->total_questions, $request->question_type, $request->difficulty_level);

            // Ambil raw JSON dari respons Gemini
            $rawJson = data_get($response, 'candidates.0.content.parts.0.text');
            Log::info('Raw Gemini Response: ' . $rawJson);

            // 1️⃣ Bersihkan JSON dari format markdown & deklarasi JS
            $cleanJson = trim($rawJson);
            $cleanJson = preg_replace('/```(json|javascript)?/', '', $cleanJson); // Hapus ``` di awal/akhir
            $cleanJson = preg_replace('/(const soal =|var soal =|let soal =)/', '', $cleanJson); // Hapus "const soal ="
            $cleanJson = trim($cleanJson, ';'); // Hapus `;` di akhir jika ada

            // 2️⃣ Perbaiki kutipan & tanda kutip yang salah
            $cleanJson = str_replace(["’", "“", "”"], ["'", '"', '"'], $cleanJson);

            // 3️⃣ Cek apakah JSON memiliki karakter aneh (misalnya BOM)
            $cleanJson = str_replace("\xEF\xBB\xBF", '', $cleanJson);

            // 4️⃣ Validasi format JSON sebelum decode
            if (json_decode($cleanJson, true) !== null) {
                Log::info('JSON sudah valid, tidak perlu ditambahkan array.');
            } else {
                if (!str_starts_with($cleanJson, '[')) {
                    Log::error('JSON tidak diawali dengan array, menyesuaikan format.');
                    $cleanJson = '[' . $cleanJson;
                }
                if (!str_ends_with($cleanJson, ']')) {
                    Log::error('JSON terpotong! Menambahkan penutup.');
                    $cleanJson .= ']';
                }
            }


            // 5️⃣ Uji JSON sebelum decode
            json_decode($cleanJson);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON Error: ' . json_last_error_msg());
                return redirect()->back()->with('error', 'JSON Error: ' . json_last_error_msg());
            }

            Log::info('Cleaned JSON after fixing format: ' . $cleanJson);


            // 6️⃣ Decode JSON ke array PHP
            $questions = json_decode($cleanJson, true);
            // dd(count($questions));
            // Log::info(count($questions));


            // 7️⃣ Simpan ke session
            session()->flash('questions', $questions);
            session(['question_type' => request('question_type')]);
            session(['difficulty_level' => request('difficulty_level')]);

            return redirect()->route('generate');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
