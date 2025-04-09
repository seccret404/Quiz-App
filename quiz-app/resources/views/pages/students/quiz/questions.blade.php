<x-student-layout>
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <h1 class="text-2xl font-bold mb-6">Questions for {{ $quiz['nama_quiz'] }}</h1>

        <div class="space-y-6">
            @foreach($questions as $question)
            <div class="bg-white rounded-lg shadow-md p-6">
                <!-- Question -->
                <div class="mb-4">
                    <h3 class="text-lg font-semibold">Question {{ $loop->iteration }}</h3>
                    <p class="text-gray-700 mt-2">{{ $question['question'] }}</p>
                </div>

                <!-- Answer - with green background -->
                <div class="bg-green-100 rounded-lg p-4">
                    <h4 class="font-medium text-green-800 mb-2">Correct Answer:</h4>
                    <p class="text-green-900">{{ $question['correct_answer'] }}</p>
                </div>

                <!-- Additional answer options if available -->
                @if(isset($question['answers']))
                <div class="mt-4 space-y-2">
                    <h4 class="font-medium">Other Options:</h4>
                    @foreach($question['answers'] as $key => $answer)
                        @if($key !== 'correct_answer' && !empty($answer))
                        <div class="bg-gray-50 rounded p-3">
                            <p>{{ $answer }}</p>
                        </div>
                        @endif
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</x-student-layout>
