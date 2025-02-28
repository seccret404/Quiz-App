<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">List Quiz Start</h1>

        <div class="grid grid-cols-2 gap-4">
            @foreach ($quizzes as $quiz)
                <div class="border rounded-lg shadow p-4">
                    <h2 class="text-lg font-semibold">{{ $quiz['nama_quiz'] ?? 'Quiz Name Not Available' }}</h2>
                    <p>Total Questions: {{ $quiz['total_question'] ?? 'N/A' }}</p>
                    <p>Quiz start: {{ $quiz['start_time'] ?? 'N/A' }}</p>
                    <p>Quiz end: {{ $quiz['end_time'] ?? 'N/A' }}</p>

                    <div class="mt-2">
                        <label class="font-semibold">Quiz Code:</label>
                        <div class="flex items-center">
                            <input type="text" value="{{ $quiz['code_quiz'] ?? 'N/A' }}"
                                class="border rounded px-2 py-1 w-full" readonly>
                            <button onclick="copyToClipboard('{{ $quiz['code_quiz'] ?? '' }}')"
                                class="ml-2 bg-gray-300 px-2 py-1 rounded">📋</button>
                        </div>
                    </div>

                    <button class="bg-blue-600 text-white px-4 py-2 rounded mt-4 w-full">Start Quiz</button>
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
