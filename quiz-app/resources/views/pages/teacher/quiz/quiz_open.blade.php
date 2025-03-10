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
            <div class="text-white p-6">{{ $currentIndex + 1 }}/{{ count($questions) }}</div>
        </div>
        <div class="bg-[#2B196681] p-6 h-full">
            @if (!empty($questions) && isset($questions[$currentIndex]))
                @php
                    $question = $questions[$currentIndex];
                @endphp

                <div class="flex justify-between items-center">
                    <div class="flex">
                        <div class="flex items-center justify-center text-white bg-[#FCC21B] rounded-[5px] p-4 w-[123px] mr-2">
                            {{ $question['score_question'] }}.0
                        </div>
                        <div class="w-[123px] flex items-center justify-center text-white rounded-[5px]  p-4"
                            @if ($question['level_questions'] === 'medium')
                                style="background-color: #FCC21B"
                            @elseif($question['level_questions'] === 'high')
                                style="background-color: red"
                            @else
                                style="background-color: #137B00"
                            @endif>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mr-2">
                                <g clip-path="url(#clip0_1132_66)">
                                    <path d="M22.5 24.5H16.5V5H22.5V24.5ZM18 23H21V6.5H18V23ZM15 24.5H9V11H15V24.5ZM10.5 23H13.5V12.5H10.5V23ZM7.5 24.5H1.5V15.5H7.5V24.5ZM3 23H6V17H3V23Z" fill="white"/>
                                </g>
                                <defs>
                                    <clipPath id="clip0_1132_66">
                                        <rect width="24" height="24" fill="white"/>
                                    </clipPath>
                                </defs>
                            </svg>
                            {{ $question['level_questions'] }}
                        </div>
                    </div>
                    <div class="relative w-[120px] h-[120px] flex items-center justify-center">
                        <div class="border border-[5px] p-1 border-[#FF5050] rounded-[60px]">
                            <div class="flex flex-col bg-[#FF5050] text-center text-white font-bold rounded-[50px] p-5 w-[95px] h-[95px]">
                                <div>Timer</div>
                                <div id="countdown">{{ $question['timer'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    let countdown = parseInt(document.getElementById("countdown").textContent);
                    let interval = setInterval(() => {
                        if (countdown > 0) {
                            document.getElementById("countdown").textContent = countdown;
                            countdown--;
                        } else {
                            clearInterval(interval);
                        }
                    }, 1000);
                </script>

                <div class="pl-[100px] pr-[100px] pt-[40px] flex flex-col items-center justify-center">
                    <div class="text-white text-center text-[24px] mb-6">
                        {{ $question['question'] }}
                    </div>
                    <div class="mt-6 grid grid-cols-2 gap-10">
                        @foreach ($question['options'] as $optionKey => $optionValue)
                            <form action="{{ route('quiz.answer', ['quizId' => $quizId, 'questionId' => $questionIds[$currentIndex]]) }}" method="POST">
                                @csrf
                                <input type="hidden" name="selected_answer" value="{{ $optionKey }}">
                                <button type="submit" class="bg-[#4E73DF] flex items-center text-white w-[300px] p-3">
                                    <div class="border border-white mr-2 p-4">{{ $optionKey }}</div>
                                    <div class="p-2">{{ $optionValue }}</div>
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            @else
                <p class="text-white text-center">Soal tidak tersedia.</p>
            @endif
        </div>
    </div>
</body>
</html>
