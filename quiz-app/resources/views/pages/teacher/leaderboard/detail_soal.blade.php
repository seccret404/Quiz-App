<x-app-layout>
    <div class="container mx-auto p-6 bg-white h-full">
        <h1 class="text-2xl font-bold mb-4">List Questions</h1>
        <div class="container mx-auto px-4 py-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold">Quiz Questions</h1>
                    <p class="text-gray-600">Code: {{ $quiz['code_quiz'] ?? 'N/A' }}</p>
                </div>
                <a href="{{ url()->previous() }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition">
                    Back to Quiz List
                </a>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                @if(empty($questions))
                    <p class="text-gray-500 text-center py-8">No questions available for this quiz.</p>
                @else
                    <div class="space-y-8">
                        @foreach($questions as $index => $question)
                            <div class="border-b border-gray-200 pb-6 mb-6 last:border-0 last:pb-0 last:mb-0">
                                <div class="flex justify-between items-start">
                                    <div class="w-full">
                                        <div class="flex justify-between items-center mb-2">
                                            <h3 class="font-semibold text-lg">
                                                Question {{ $loop->iteration }}: {{ $question['question'] ?? 'No question text' }}
                                            </h3>
                                            <div class="flex items-center space-x-3">
                                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full capitalize">
                                                    {{ $question['level_questions'] ?? 'unknown' }} level
                                                </span>
                                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                                                    {{ $question['score_question'] ?? 0 }} points
                                                </span>
                                                <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">
                                                    {{ $question['timer'] ?? 0 }} seconds
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Check if question has options (Multiple Choice) --}}
                                        @if(isset($question['options']) && is_array($question['options']) && count($question['options']) > 0)
                                            <div class="mt-4 ml-4">
                                                <p class="font-medium text-gray-700 mb-2">Options:</p>
                                                <ul class="space-y-2">
                                                    @foreach(['A', 'B', 'C', 'D'] as $letter)
                                                        @if(isset($question['options'][$letter]))
                                                            <li class="flex items-start">
                                                                <span class="mr-2 mt-1">{{ $letter }}.</span>
                                                                <div class="{{ isset($question['correct_answer']) && $question['correct_answer'] == $letter ? 'bg-green-50 border border-green-200' : 'bg-gray-50' }} p-2 rounded w-full">
                                                                    {{ $question['options'][$letter] }}
                                                                    @if(isset($question['correct_answer']) && $question['correct_answer'] == $letter)
                                                                        <span class="ml-2 text-green-600 font-semibold">
                                                                            ✓ Correct Answer: {{ $question['options'][$letter] }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </div>

                                        {{-- True/False questions --}}
                                        @elseif(isset($question['correct_answer']) && is_string($question['correct_answer']) && in_array(strtolower($question['correct_answer']), ['true', 'false']))
                                            <div class="mt-4 ml-4">
                                                <p class="font-medium text-gray-700">Correct Answer:</p>
                                                <div class="mt-2 p-3 rounded {{ strtolower($question['correct_answer']) === 'true' ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200' }}">
                                                    {{ ucfirst($question['correct_answer']) }}
                                                </div>
                                            </div>

                                        {{-- Other question types --}}
                                        @else
                                            <div class="mt-4 ml-4">
                                                <p class="font-medium text-gray-700">Expected Answer:</p>
                                                <div class="mt-2 p-3 bg-blue-50 text-blue-800 rounded border border-blue-200">
                                                    {{ $question['correct_answer'] ?? 'No sample answer provided' }}
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Answer Statistics --}}
                                        <div class="mt-4 flex flex-wrap gap-4">
                                            <div class="flex items-center text-green-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                <span>{{ $question['correct_count'] ?? 0 }} answered correctly</span>
                                            </div>
                                            <div class="flex items-center text-red-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                <span>{{ $question['wrong_count'] ?? 0 }} answered incorrectly</span>
                                            </div>
                                            @php
                                                $totalAnswers = ($question['correct_count'] ?? 0) + ($question['wrong_count'] ?? 0);
                                                $correctPercentage = $totalAnswers > 0 ? round(($question['correct_count'] / $totalAnswers) * 100) : 0;
                                            @endphp
                                            <div class="flex-1 min-w-[200px]">
                                                <div class="flex justify-between text-sm text-gray-600 mb-1">
                                                    <span>Correct rate:</span>
                                                    <span>{{ $correctPercentage }}%</span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $correctPercentage }}%"></div>
                                                </div>
                                            </div>
                                        </div>

                                        @if(!empty($question['feedback']))
                                            <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
                                                <p class="font-medium text-gray-700">Feedback:</p>
                                                <p class="mt-1 text-gray-600">{{ $question['feedback'] }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
