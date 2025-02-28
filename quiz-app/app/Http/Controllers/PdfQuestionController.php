<?php

namespace App\Http\Controllers;

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
        $prompt = "Buatkan $totalQuestions soal tipe $questionType berdasarkan teks berikut kedalam bentuk array pisahkan soal dengan option nya dan buat feedback(penjelasan) terhadap jawaban yang benar tersebut:\n" . $text;

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
                'question_type' => 'required|string'
            ]);

            // Simpan file PDF ke storage
            $pdfPath = $request->file('pdf')->store('uploads');

            // Ekstrak teks dari PDF
            $text = $this->extractTextFromPDF(storage_path("app/$pdfPath"));

            // Kirim teks ke API Gemini untuk generate soal
            $response = $this->generateQuestions($text, $request->total_questions, $request->question_type);

            // Ambil raw JSON dari respons Gemini
            $rawJson = data_get($response, 'candidates.0.content.parts.0.text');

            // 🔍 Debug sebelum membersihkan JSON
            // dd($rawJson);

            // 1. Hapus blok kode Markdown ```javascript
            $cleanJson = preg_replace('/```(json|javascript)?/', '', trim($rawJson));

            // 2. Hapus deklarasi variabel "const soal ="
            $cleanJson = preg_replace('/const soal = /', '', trim($cleanJson));

            // 3. Gunakan regex untuk mengambil hanya array JSON di dalamnya
            if (preg_match('/\[(.*)\]/s', $cleanJson, $matches)) {
                $cleanJson = "[" . $matches[1] . "]";
            }

            // 🔍 Debug setelah membersihkan JSON
            // dd($cleanJson);

            // 4. Parsing JSON ke array PHP
            $questions = json_decode($cleanJson, true);

            // Jika terjadi error dalam parsing JSON, tampilkan error
            // if (json_last_error() !== JSON_ERROR_NONE) {
            //     dd(json_last_error_msg());
            // }

            session()->flash('questions', $questions);

            // 🔍 Debug hasil parsing JSON yang sudah diperbaiki
            // dd($questions);  

            session(['question_type' => request('question_type')]);

            // return view('pages.teacher.quiz.quiz', compact('questions'));
            return redirect()->route('generate');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
