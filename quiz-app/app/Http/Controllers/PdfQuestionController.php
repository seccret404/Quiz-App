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

    private function generateQuestions($text, $totalQuestions, $questionType)
    {
        //persipan API GEMINI

        $apiKey = env('GEMINI_API_KEY');

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=$apiKey";
        // $prompt = "Buatkan $totalQuestions soal tipe $questionType berdasarkan teks berikut kedalam format array [] pisahkan soal dengan option nya dan buat feedback(penjelasan) terhadap jawaban yang benar tersebut:\n" . $text;

        $easyCount = floor($totalQuestions * 0.3);
        $mediumCount = floor($totalQuestions * 0.3);
        $highCount = $totalQuestions - $easyCount - $mediumCount;

        if ($questionType == "Multiple Choice") {
            $prompt = "Buatkan $totalQuestions soal pilihan ganda dengan format JSON yang valid.
            Distribusi soal sebagai berikut:
                - $easyCount soal tingkat easy
                - $mediumCount soal tingkat medium
                - $highCount soal tingkat high

                - Jika tingkat kesulitan adalah 'easy', buatlah pertanyaan yang sederhana dan jawaban yang langsung jelas.
                - Jika tingkat kesulitan adalah 'medium', buatlah pertanyaan yang membutuhkan pemahaman konsep yang lebih dalam.
                - Jika tingkat kesulitan adalah 'high', buatlah pertanyaan yang menantang dan membutuhkan analisis atau aplikasi konsep yang kompleks.

            Setiap soal memiliki pertanyaan, 4 opsi jawaban (A, B, C, D), jawaban yang benar, level quiz, dan feedback untuk jawaban yang benar.
            Soal di generate menggunakan bahasa pdf nya, jika pdf bahasa inggris maka soal bahasa inggris. Demikian dengan pdf bahasa yang lain.

            Gunakan format berikut:

            [
            {
                \"question\": \"Apa ibukota Indonesia?\",
                \"options\": { \"A\": \"Jakarta\", \"B\": \"Surabaya\", \"C\": \"Bandung\", \"D\": \"Medan\" },
                \"answer\": \"A\",
                \"level\": \"easy\",
                \"feedback\": \"Jakarta adalah ibukota Indonesia sejak tahun 1945.\"
            },
            ...
            ]

            Gunakan teks berikut sebagai referensi untuk membuat soal:\n" . $text;
        } elseif ($questionType == "Essay") {
            $prompt = "Buatkan $totalQuestions pernyataan dengan format JSON yang valid.
            Distribusi soal sebagai berikut:
                - $easyCount soal tingkat easy
                - $mediumCount soal tingkat medium
                - $highCount soal tingkat high

                - Jika tingkat kesulitan adalah 'easy', buatlah pernyataan yang sederhana dengan jawaban berupa istilah tunggal/singkat.
                - Jika tingkat kesulitan adalah 'medium', buatlah pernyataan yang membutuhkan pemahaman konsep dengan jawaban berupa istilah kunci.
                - Jika tingkat kesulitan adalah 'high', buatlah pernyataan yang menantang dengan jawaban berupa istilah teknis atau konsep kompleks.

            Setiap pernyataan memiliki:
                - Informasi singkat yang meminta jawaban berupa istilah
                - Jawaban yang harus berupa istilah/kata kunci tunggal (bukan kalimat)
                - Feedback singkat untuk jawaban yang benar
                - Level kesulitan
                - Bahasa soal mengikuti bahasa pdf nya

            Pernyataan yang dihasilkan tidak berupa pertanyaan tapi pernyataan yang meminta istilah.
            Soal yang  dihasilkan menggunakan bahasa yang sama dengan teks referensi.

            Format contoh (perhatikan jawaban berupa istilah singkat):

            [
            {
                \"question\": \"Nama ilmuwan yang menemukan lampu pijar.\",
                \"answer\": \"Edison\",
                \"level\": \"easy\",
                \"feedback\": \"Thomas Alva Edison adalah penemu lampu pijar.\"
            },
            {
                \"question\": \"Ibukota negara Indonesia.\",
                \"answer\": \"Jakarta\",
                \"level\": \"easy\",
                \"feedback\": \"Jakarta merupakan ibukota Indonesia sejak 1945.\"
            },
            {
                \"question\": \"Teori yang menjelaskan evolusi spesies melalui seleksi alam.\",
                \"answer\": \"Darwinisme\",
                \"level\": \"medium\",
                \"feedback\": \"Darwinisme adalah teori evolusi yang dikembangkan Charles Darwin.\"
            },
            ...
            ]

            Gunakan teks berikut sebagai referensi untuk membuat soal:\n" . $text;
        } elseif ($questionType == "True False") {
            $prompt = "Buatkan $totalQuestions soal true/false dengan format JSON yang valid.
            Distribusi soal sebagai berikut:
                - $easyCount soal tingkat easy
                - $mediumCount soal tingkat medium
                - $highCount soal tingkat high

                - Jika tingkat kesulitan adalah 'easy', buatlah pertanyaan yang sederhana dan jawaban yang langsung jelas.
                - Jika tingkat kesulitan adalah 'medium', buatlah pertanyaan yang membutuhkan pemahaman konsep yang lebih dalam.
                - Jika tingkat kesulitan adalah 'high', buatlah pertanyaan yang menantang dan membutuhkan analisis atau aplikasi konsep yang kompleks.

            Setiap soal memiliki pertanyaan, jawaban (True atau False), dan feedback mengapa jawaban tersebut benar atau salah.
            Soal di generate menggunakan bahasa pdf nya, jika pdf bahasa inggris maka soal bahasa inggris. Demikian dengan pdf bahasa yang lain.
            Gunakan format berikut:

            [
            {
                \"question\": \"Matahari mengelilingi bumi.\",
                \"answer\": \"False\",
                \"level\": \"easy\",
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
            ]);

            // Simpan file PDF ke storage
            $pdfPath = $request->file('pdf')->store('uploads');

            // Ekstrak teks dari PDF
            $text = $this->extractTextFromPDF(storage_path("app/$pdfPath"));

            // Kirim teks ke API Gemini untuk generate soal
            $response = $this->generateQuestions($text, $request->total_questions, $request->question_type);

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
            // session(['difficulty_level' => request('difficulty_level')]);

            return redirect()->route('generate');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
