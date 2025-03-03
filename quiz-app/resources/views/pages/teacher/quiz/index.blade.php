<x-app-layout>
    <div class="container mx-auto p-6 bg-white h-full">
        <h1 class="text-2xl font-bold mb-4">List Quiz Start</h1>

        <div class="grid grid-cols-4 gap-6">
            @foreach ($quizzes as $quiz)
                <div class="border rounded-lg text-[#808080] text-center p-[25px]">
                    <h2 class="text-[18px] text-black font-semibold">{{ $quiz['nama_quiz'] ?? 'Quiz Name Not Available' }}</h2>
                    <p class="m-4">Total Questions: {{ $quiz['total_question'] ?? 'N/A' }}</p>
                    <p>Quiz start: {{ $quiz['start_time'] ?? 'N/A' }}</p>
                    <p>Quiz end: {{ $quiz['end_time'] ?? 'N/A' }}</p>

                    <div class="mt-2">
                        <label class="font-semibold">Quiz Code:</label>
                        <div class="flex items-center border bg-white rounded  border-[#80808083]">
                            <input type="text" value="{{ $quiz['code_quiz'] ?? 'N/A' }}"
                                class=" border-none text-center rounded w-full" disabled>
                            <button onclick="copyToClipboard('{{ $quiz['code_quiz'] ?? '' }}')"
                                class="h-full bg-white px-2 py-1  ">📋</button>
                        </div>
                    </div>

                    <button class="bg-[#4E73DF] text-white px-4 py-2 rounded mt-4 w-full">Start Quiz</button>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        function copyToClipboard(code) {
            navigator.clipboard.writeText(code).then(() => {
                alert('Copied: ' + code);
            });
        }
    </script>
</x-app-layout>
