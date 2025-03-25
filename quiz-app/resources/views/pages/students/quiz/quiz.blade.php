<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Quiz</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>
<body>
    <div class="h-screen">
        <div class="bg-[#2B1966]">
            <div class="text-white p-6">
                {{ $questions->currentPage() }} / {{ $questions->lastPage() }}
            </div>
        </div>
        <div class="bg-[#2B196681] p-6 h-full">
            <div class="flex justify-between items-center">
                <div class="flex">
                    <div class="flex items-center justify-center text-white bg-[#FCC21B] rounded-[5px] p-4 w-[123px] mr-2">
                        {{ $currentQuestion['score_question'] }}.0
                    </div>
                    <div class="w-[123px] flex items-center justify-center text-white rounded-[5px] p-4"
                         style="background-color: {{ $currentQuestion['level_questions'] === 'medium' ? '#FCC21B' : ($currentQuestion['level_questions'] === 'high' ? 'red' : '#137B00') }};">
                        <i class="bi bi-bar-chart"></i>
                        {{ $currentQuestion['level_questions'] }}
                    </div>
                </div>
                <div class="relative w-[120px] h-[120px] flex items-center justify-center">
                    <svg class="absolute w-full h-full" viewBox="0 0 100 100">
                        <circle class="text-gray-700" stroke-width="5" stroke="currentColor" fill="transparent" r="45" cx="50" cy="50" />
                        <circle id="progress" class="text-red-500 transition-all duration-1000" stroke-width="5" stroke="currentColor" fill="transparent" r="45" cx="50" cy="50" stroke-dasharray="282" stroke-dashoffset="282" stroke-linecap="round" transform="rotate(-90 50 50)" />
                    </svg>
                    <div class="border border-[5px] p-1 border-[#FF5050] rounded-[60px]">
                        <div class="flex flex-col bg-[#FF5050] text-center text-white font-bold rounded-[50px] p-5 w-[95px] h-[95px]">
                            <div>Timer</div>
                            <div id="countdown">{{ $currentQuestion['timer'] }}</div>
                        </div>
                    </div>
                </div>
            </div>



            <script>
                let countdown = parseInt(document.getElementById("countdown").textContent);
                let progressCircle = document.getElementById("progress");
                let countdownText = document.getElementById("countdown");
                let totalDashArray = 282;
                let initialCountdown = countdown;
                let interval;

                // Cek apakah ada feedback error
                let hasFeedbackError = "{{ $errors->has('feedback') ? 'true' : 'false' }}" === 'true';

                function startTimer() {
                    if (hasFeedbackError) {
                        // Jika jawaban salah, hentikan timer dan sembunyikan submit
                        countdownText.textContent = "X";
                        progressCircle.style.strokeDashoffset = totalDashArray;
                        return;
                    }

                    interval = setInterval(() => {
                        if (countdown > 0) {
                            countdownText.textContent = countdown;
                            countdown--;
                            let progress = (countdown / initialCountdown) * totalDashArray;
                            progressCircle.style.strokeDashoffset = progress;
                        } else {
                            clearInterval(interval);
                            document.getElementById('next-question').submit();
                        }
                    }, 1000);
                }

                function stopTimer() {
                    clearInterval(interval);
                    progressCircle.style.animation = "none"; // Hentikan animasi stroke
                }

                // Jalankan timer hanya jika tidak ada feedback error
                if (!hasFeedbackError) {
                    startTimer();
                }
            </script>

            <div class="pl-[100px] pr-[100px] pt-[40px] flex flex-col items-center justify-center">
                <div class="text-white text-center text-[24px] mb-6">
                    {{ $currentQuestion['question'] }}
                </div>

                @if ($quizType === 'Multiple Choice')
                    <!-- Tampilan Soal Pilihan Ganda -->
                    <div class="mt-6 grid grid-cols-2 gap-10">
                        @foreach ($currentQuestion['options'] as $optionKey => $optionValue)
                            <form action="{{ route('quiz.answer', ['quizId' => $quizId, 'questionId' => $currentQuestionId]) }}"
                                  method="POST"
                                  onsubmit="stopTimer()">
                                @csrf
                                <input type="hidden" name="selected_option" value="{{ $optionKey }}">
                                <button type="submit" class="bg-[#4E73DF] flex items-center text-white w-[300px] p-3">
                                    <div class="border border-white mr-2 p-4">{{ $optionKey }}</div>
                                    <div class="p-2">{{ $optionValue }}</div>
                                </button>
                            </form>
                        @endforeach
                    </div>
                @elseif ($quizType === 'Essay')
                    <!-- Tampilan Soal Essay -->
                    <form action="{{ route('quiz.answer', ['quizId' => $quizId, 'questionId' => $currentQuestionId]) }}"
                          method="POST"
                          class="w-full flex flex-col items-center">
                        @csrf
                        <textarea name="essay_answer"
                                  class="w-full p-4 text-black border rounded-md"
                                  rows="5"
                                  placeholder="Ketik jawaban Anda di sini..."></textarea>
                        <button type="submit" class="bg-blue-500 text-white p-3 rounded mt-4">
                            Submit Jawaban
                        </button>
                    </form>
                @endif
                   {{-- Navigasi Soal --}}
            <div class="flex justify-between mt-6">
                @php
                    $currentIndex = array_search($currentQuestionId, $questionIds);
                    $prevQuestionId = $currentIndex > 0 ? $questionIds[$currentIndex - 1] : null;
                    $nextQuestionId = $currentIndex < count($questionIds) - 1 ? $questionIds[$currentIndex + 1] : null;
                @endphp
            </div>
                {{-- Tombol Selanjutnya --}}
                @if ($nextQuestionId && $errors->has('feedback'))
                <div class="p-6 textc-center border rounded">
                    {{ $errors->first('feedback') }}
                </div>
                <a href="{{ route('quiz.question', ['quizId' => $quizId, 'questionId' => $nextQuestionId ?? $currentQuestionId, 'code_quiz' => request()->query('code_quiz')]) }}"
                class="bg-blue-500 text-white p-3 rounded mt-2">
                    Next →
                </a>
                @endif
            </div>


            <!-- Form untuk auto-submit saat waktu habis -->
            <form id="next-question" method="POST" action="{{ route('quiz.answer', ['quizId' => $quizId, 'questionId' => $currentQuestionId]) }}">
                @csrf
                <input type="hidden" name="selected_option" value="">
            </form>

        </div>
    </div>
</body>
</html>
