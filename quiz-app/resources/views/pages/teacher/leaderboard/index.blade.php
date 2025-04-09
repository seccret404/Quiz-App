<x-app-layout>
    <div class="container mx-auto p-6 bg-white h-full">
        <h1 class="text-2xl font-bold mb-4">List Quiz Start</h1>

        <div class="grid grid-cols-3 gap-3">
            @foreach ($quizzes as $quiz)
                <div class="border rounded-lg text-[12px] text-[#808080] text-center p-[25px]">
                    <h2 class="text-[18px] text-black font-semibold">{{ $quiz['nama_quiz'] ?? 'Quiz Name Not Available' }}</h2>
                    <p class="m-4">Total Questions: {{ $quiz['total_question'] ?? 'N/A' }}</p>
                    <p>Quiz start: {{ $quiz['start_time'] ?? 'N/A' }}</p>
                    <p>Quiz end: {{ $quiz['end_time'] ?? 'N/A' }}</p>

                    <div class="mt-2">
                        <label class="font-semibold">Quiz Code:</label>
                        <div class="text-[10px] text-[#808080] hidden copied-message">Code Copied!</div>
                        <div class="flex items-center border bg-white rounded border-[#80808083]">
                            <input type="text" value="{{ $quiz['code_quiz'] ?? 'N/A' }}"
                                class="border-none text-center rounded w-full" disabled>
                            <button onclick="copyToClipboard('{{ $quiz['code_quiz'] ?? '' }}', this)"
                                class="h-full bg-white px-2 py-1 cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 48 48">
                                    <path d="M 18.5 5 C 15.480226 5 13 7.4802259 13 10.5 L 13 32.5 C 13 35.519774 15.480226 38 18.5 38 L 34.5 38 C 37.519774 38 40 35.519774 40 32.5 L 40 10.5 C 40 7.4802259 37.519774 5 34.5 5 L 18.5 5 z M 18.5 8 L 34.5 8 C 35.898226 8 37 9.1017741 37 10.5 L 37 32.5 C 37 33.898226 35.898226 35 34.5 35 L 18.5 35 C 17.101774 35 16 33.898226 16 32.5 L 16 10.5 C 16 9.1017741 17.101774 8 18.5 8 z M 11 10 L 9.78125 10.8125 C 8.66825 11.5545 8 12.803625 8 14.140625 L 8 33.5 C 8 38.747 12.253 43 17.5 43 L 30.859375 43 C 32.197375 43 33.4465 42.33175 34.1875 41.21875 L 35 40 L 17.5 40 C 13.91 40 11 37.09 11 33.5 L 11 10 z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('quiz.questions-detail', ['quizId' => $quiz['id'] ?? '']) }}">
                            <button class="bg-[#4E73DF] text-white px-4 py-2 cursor-pointer rounded mt-4 w-full flex items-center text-[12px]">
                                <img width="15" height="15" class="mr-2" src="{{asset('images/eye.png')}}" alt=""/>Questions
                            </button>
                        </a>
                        <a href="{{ route('quiz.leaderboards', $quiz['id']) }}">
                            <button class="bg-[#4E73DF] text-white px-4 py-2 cursor-pointer rounded mt-4 w-full flex items-center text-[12px]"><img width="15" height="15" class="mr-2" src="{{asset('images/eye.png')}}" alt=""/>Leaderboard</button>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        function copyToClipboard(code, buttonElement) {
            navigator.clipboard.writeText(code).then(() => {
                const card = buttonElement.closest('.border.rounded-lg');
                const copiedMessage = card.querySelector('.copied-message');

                if (copiedMessage) {
                    copiedMessage.style.display = 'block';
                    setTimeout(() => {
                        copiedMessage.style.display = 'none';
                    }, 3000);
                }
            });
        }
    </script>


</x-app-layout>
{{-- {{route('quiz.question', ['quizId' => $quiz['code_quiz']])}} --}}
{{--  --}}
