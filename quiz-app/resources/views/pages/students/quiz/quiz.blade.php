<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        .progress-ring__circle {
            transition: stroke-dashoffset 0.5s ease;
            transform: rotate(-90deg);
            transform-origin: 50% 50%;
        }
        .fade-out {
            animation: fadeOut 3s ease forwards;
        }
        @keyframes fadeOut {
            to { opacity: 0; }
        }
        .selected-wrong {
            background-color: #fee2e2;
            border-color: #ef4444;
        }
        .correct-answer {
            background-color: #dcfce7;
            border-color: #22c55e;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <!-- Header Section -->
        <header class="bg-[#2B1966] shadow-md">
            <div class="container mx-auto px-4 py-4">
                <div class="flex justify-between items-center">
                    <div class="text-white text-lg font-semibold">
                        Quiz App
                    </div>
                    <div class="text-white">
                        {{ $questions->currentPage() }} / {{ $questions->lastPage() }}
                    </div>
                </div>

                <!-- Progress Bar -->
                @php
                    $progressPercentage = ($questions->currentPage() / $questions->lastPage()) * 100;
                    $progressColor = match($currentQuestion['level_questions']) {
                        'medium' => '#FCC21B',
                        'high' => '#FF5050',
                        default => '#137B00'
                    };
                @endphp
                <div class="mt-4 w-full bg-gray-200 rounded-full h-4">
                    <div class="h-4 rounded-full flex items-center justify-center text-white text-xs font-bold transition-all duration-500"
                         style="width: {{ $progressPercentage }}%; background-color: {{ $progressColor }};">
                        <span class="hidden sm:inline">Level: {{ ucfirst($currentQuestion['level_questions']) }} - {{ round($progressPercentage) }}%</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow container mx-auto px-4 py-6">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Question Header -->
                <div class="bg-[#2B1966] p-4 text-white flex justify-between items-center">
                    <div class="flex space-x-2">
                        <div class="bg-[#FCC21B] rounded px-3 py-1 flex items-center">
                            <span>{{ $currentQuestion['score_question'] }}.0 Poin</span>
                        </div>
                        <div class="rounded px-3 py-1 flex items-center"
                             style="background-color: {{ $progressColor }};">
                            <i class="bi bi-bar-chart mr-1"></i>
                            <span>{{ ucfirst($currentQuestion['level_questions']) }}</span>
                        </div>
                    </div>

                    <!-- Timer -->
                    <div class="flex items-center">
                        <div class="relative w-12 h-12 mr-2">
                            <svg class="w-full h-full" viewBox="0 0 36 36">
                                <circle cx="18" cy="18" r="16" fill="none" class="stroke-gray-300" stroke-width="2"></circle>
                                <circle id="progress-ring" cx="18" cy="18" r="16" fill="none"
                                        class="stroke-red-500" stroke-width="2"
                                        stroke-dasharray="100"
                                        stroke-dashoffset="100"></circle>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center text-white font-bold text-sm">
                                <span id="countdown">{{ $currentQuestion['timer'] }}</span>
                            </div>
                        </div>
                        <span class="text-sm">Detik</span>
                    </div>
                </div>

                <!-- Question Content -->
                <div class="p-6">
                    <div class="text-gray-800 text-xl font-medium mb-8">
                        {{ $currentQuestion['question'] }}
                    </div>

                    <!-- Answer Options -->
                    @if ($quizType === 'Multiple Choice')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($currentQuestion['options'] as $optionKey => $optionValue)
                                <form method="POST"
                                      action="{{ route('quiz.answer', ['quizId' => $quizId, 'questionId' => $currentQuestionId]) }}"
                                      id="form-{{ $optionKey }}"
                                      class="answer-form">
                                    @csrf
                                    <input type="hidden" name="selected_option" value="{{ $optionKey }}">
                                    <input type="hidden" name="id_questions" value="{{ $currentQuestionId }}">
                                    <input type="hidden" name="time_taken" class="time-taken-input" value="">

                                    <button type="button"
                                            onclick="submitAnswer('form-{{ $optionKey }}')"
                                            class="w-full p-4 border rounded-lg transition-all flex items-start
                                                  @if(request('selected') == $optionKey && request('show_feedback')) selected-wrong
                                                  @elseif($optionKey == $currentQuestion['correct_answer'] && request('show_feedback')) correct-answer
                                                  @else hover:bg-blue-50 @endif">
                                        <div class="bg-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">
                                            {{ $optionKey }}
                                        </div>
                                        <div class="text-left">
                                            {{ $optionValue }}
                                            @if($optionKey == $currentQuestion['correct_answer'] && request('show_feedback'))
                                                <span class="ml-2 text-green-600 text-sm">✓ Jawaban benar</span>
                                            @endif
                                        </div>
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    @elseif ($quizType === 'Essay')
                        <form method="POST"
                            action="{{ route('quiz.answer', ['quizId' => $quizId, 'questionId' => $currentQuestionId]) }}"
                            id="essay-form"
                            class="answer-form space-y-4">
                            @csrf
                            <input type="hidden" name="id_questions" value="{{ $currentQuestionId }}">
                            <input type="hidden" name="time_taken" class="time-taken-input" value="">

                            <textarea name="essay_answer" rows="5"
                                    class="w-full px-4 py-2 border
                                        @if(request('show_feedback'))
                                            @if($isEssayCorrect) border-green-500
                                            @else border-red-500
                                            @endif
                                        @else border-gray-300
                                        @endif
                                        rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Tulis jawaban Anda disini...">{{ old('essay_answer', request('selected')) }}</textarea>

                            @error('essay_answer')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                            @error('essay_error')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror

                            <button type="button"
                                    onclick="submitAnswer('essay-form')"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors">
                                Submit Jawaban
                            </button>
                        </form>
                    @elseif ($quizType === 'True False')
                        <div class="flex space-x-4 justify-center">
                            <form method="POST"
                                  action="{{ route('quiz.answer', ['quizId' => $quizId, 'questionId' => $currentQuestionId]) }}"
                                  id="true-form"
                                  class="answer-form">
                                @csrf
                                <input type="hidden" name="selected_option" value="True">
                                <input type="hidden" name="id_questions" value="{{ $currentQuestionId }}">
                                <input type="hidden" name="time_taken" class="time-taken-input" value="">

                                <button type="button"
                                        onclick="submitAnswer('true-form')"
                                        class="bg-green-500 hover:bg-green-600 text-white px-8 py-3 rounded-lg transition-colors
                                        @if(request('selected') == 'True' && request('show_feedback')) selected-wrong
                                        @elseif('True' == $currentQuestion['correct_answer'] && request('show_feedback')) correct-answer
                                        @else hover:bg-blue-50 @endif">
                                    Benar
                                </button>
                            </form>
                            <form method="POST"
                                  action="{{ route('quiz.answer', ['quizId' => $quizId, 'questionId' => $currentQuestionId]) }}"
                                  id="false-form"
                                  class="answer-form">
                                @csrf
                                <input type="hidden" name="selected_option" value="False">
                                <input type="hidden" name="id_questions" value="{{ $currentQuestionId }}">
                                <input type="hidden" name="time_taken" class="time-taken-input" value="">

                                <button type="button"
                                        onclick="submitAnswer('false-form')"
                                        class="bg-red-500 hover:bg-red-600 text-white px-8 py-3 rounded-lg transition-colors
                                        @if(request('selected') == 'False' && request('show_feedback')) selected-wrong
                                        @elseif('False' == $currentQuestion['correct_answer'] && request('show_feedback')) correct-answer
                                        @else hover:bg-blue-50 @endif">
                                    Salah
                                </button>
                            </form>
                        </div>
                    @endif

                    <!-- Feedback Section -->
                    @if(request('show_feedback'))
                        <div id="feedback-container" class="border-t border-gray-200 p-6 bg-amber-50 @if(request('is_final')) fade-out @endif">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 text-amber-500 pt-1">
                                    <i class="bi bi-exclamation-triangle-fill text-xl"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-amber-800 font-bold mb-2">Pembahasan:</h3>
                                    <p class="text-amber-700">
                                        {{ $currentQuestion['feedback'] ?? 'Jawaban Anda belum tepat.' }}
                                    </p>

                                    <div class="mt-2">
                                        <span class="font-medium">Jawaban benar:</span>
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded ml-2">
                                            {{ $currentQuestion['correct_answer'] }}
                                        </span>
                                    </div>

                                    @if(request('is_final'))
                                        <div class="mt-3 text-sm text-amber-600 flex items-center">
                                            <i class="bi bi-hourglass-split animate-spin mr-2"></i>
                                            Mengarahkan ke halaman hasil dalam 3 detik...
                                        </div>
                                        <script>
                                            setTimeout(function() {
                                                window.location.href = "{{ route('quiz.completed', ['quizId' => $quizId]) }}";
                                            }, 3000);
                                        </script>
                                    @else
                                        @php
                                            $currentIndex = array_search($currentQuestionId, $questionIds);
                                            $nextQuestionId = ($currentIndex !== false && isset($questionIds[$currentIndex + 1]))
                                                            ? $questionIds[$currentIndex + 1]
                                                            : null;
                                        @endphp

                                        @if($nextQuestionId))
                                            <div class="mt-4">
                                                <a href="{{ route('quiz.question', [
                                                    'quizId' => $quizId,
                                                    'questionId' => $nextQuestionId,
                                                    'code_quiz' => request()->query('code_quiz')
                                                ]) }}"
                                                class="inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition-colors">
                                                    Lanjut ke Soal Berikutnya
                                                    <i class="bi bi-arrow-right ml-2"></i>
                                                </a>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </main>

        <!-- Footer Navigation -->
        <footer class="bg-white border-t border-gray-200 py-4">
            <div class="container mx-auto px-4 flex justify-between">
                @php
                    $currentIndex = array_search($currentQuestionId, $questionIds);
                    $prevQuestionId = ($currentIndex > 0) ? $questionIds[$currentIndex - 1] : null;
                @endphp

                @if ($prevQuestionId))
                    <a href="{{ route('quiz.question', [
                        'quizId' => $quizId,
                        'questionId' => $prevQuestionId,
                        'code_quiz' => request()->query('code_quiz')
                    ])}}"
                    class="text-blue-500 hover:text-blue-700 flex items-center">
                        <i class="bi bi-arrow-left mr-2"></i> Soal Sebelumnya
                    </a>
                @else
                    <div></div>
                @endif

                <div class="text-gray-500 text-sm">
                    Sisa Waktu: <span id="footer-countdown">{{ $currentQuestion['timer'] }}</span> detik
                </div>
            </div>
        </footer>
    </div>

    <!-- Timer Script -->
    <script>
        let countdown, initialCountdown, progressRing, interval, startTime;
        let hasFeedback = {{ request('show_feedback') ? 'true' : 'false' }};

        document.addEventListener('DOMContentLoaded', function() {
            countdown = parseInt(document.getElementById("countdown").textContent);
            initialCountdown = countdown;
            progressRing = document.getElementById("progress-ring");
            startTime = new Date().getTime();

            if (!hasFeedback) {
                startTimer();
            }
        });

        function startTimer() {
            interval = setInterval(updateTimer, 1000);
            console.log("Timer started with initial time:", initialCountdown);
        }

        function updateTimer() {
            if (countdown > 0) {
                countdown--;
                document.getElementById("countdown").textContent = countdown;
                document.getElementById("footer-countdown").textContent = countdown;

                const offset = 100 - (countdown / initialCountdown * 100);
                progressRing.style.strokeDashoffset = offset;

                if (countdown <= 5) {
                    progressRing.classList.remove('stroke-red-500');
                    progressRing.classList.add('stroke-red-700');
                }
            } else {
                handleTimeout();
            }
        }

        function handleTimeout() {
            clearInterval(interval);
            const timeTaken = initialCountdown; // Waktu habis
            document.querySelectorAll('.time-taken-input').forEach(input => {
                input.value = timeTaken;
            });
            console.log("Time expired. Setting time_taken to:", timeTaken);

            // Set timeout flag
            document.getElementById('timeout-flag').value = '1';

            setTimeout(() => {
                document.getElementById('auto-submit-form').submit();
            }, 100);
        }

        function submitAnswer(formId) {
            clearInterval(interval);
            const endTime = new Date().getTime();
            const timeUsed = Math.round((endTime - startTime) / 1000);
            const remainingTime = Math.max(0, initialCountdown - timeUsed);

            // Set time_taken value in the specific form
            const form = document.getElementById(formId);
            const timeInput = form.querySelector('.time-taken-input');
            timeInput.value = remainingTime;

            console.log("Submitting form:", formId, "Time taken:", remainingTime);

            // Submit after a small delay
            setTimeout(() => {
                form.submit();
            }, 100);
        }
    </script>

    <!-- Auto-Submit Form for Timeout -->
    <form id="auto-submit-form" method="POST" action="{{ route('quiz.answer', ['quizId' => $quizId, 'questionId' => $currentQuestionId]) }}">
        @csrf
        <input type="hidden" name="selected_option" value="">
        <input type="hidden" name="time_taken" class="time-taken-input" value="">
        <input type="hidden" name="timeout" id="timeout-flag" value="0">
    </form>
</body>
</html>
