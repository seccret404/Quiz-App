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
        $prompt = "Buatkan $totalQuestions soal tipe $questionType berdasarkan teks berikut kedalam bentuk array pisahkan soal dengan option nya:\n" . $text;

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
        //dari form quiz
        $request->validate([
            'pdf' => 'required|mimes:pdf|max:2048',
            'total_questions' => 'required|integer|min:1',
            'question_type' => 'required|string'
        ]);

        $pdfPath = $request->file('pdf')->store('uploads');
        $text = $this->extractTextFromPDF(storage_path("app/$pdfPath"));

        $response = $this->generateQuestions($text, $request->total_questions, $request->question_type);

        // dd($response);

        $rawJson = data_get($response, 'candidates.0.content.parts.0.text');

        // 2. cleaning array
        $cleanJson = str_replace(["```json", "```"], "", $rawJson);

        //    parse item to array
        $questions = json_decode($cleanJson, true);

        return view('pages.teacher.quiz.quiz', compact('questions'));

    }
}
