<x-app-layout>
    <div class="container mx-auto p-6 bg-white h-full">
        <h1 class="text-2xl font-bold mb-4">List Questions {{ $quiz['nama_quiz'] ?? 'Quiz Name Not Available' }}</h1>

        @if (!empty($filteredQuestions))
        @foreach ($filteredQuestions as $qIndex => $question)
        @php
            $optionKeys = ['A', 'B', 'C', 'D'];
        @endphp
            <div class="bg-white rounded p-4 m-6 shadow-md">
                <div class="text-black font-bold">Question {{ $qIndex + 1 }}:</div>
                <textarea disabled name="questions[{{ $qIndex }}][question]" class="w-full p-2 border-none rounded">{{ $question['question'] ?? 'No question text' }}</textarea>

                <div class="grid grid-cols-4 gap-2">
                    <div class="col-span-3">
                        <div class="text-black font-bold mt-2">Options</div>
                        <div class="grid grid-cols-2 gap-4">
                            @if (isset($question['options']) && is_array($question['options']))
                                @foreach ($question['options'] as $key => $option)
                                    @php
                                        if (isset($question['correct_answer'])) {
                                            if (is_array($question['correct_answer'])) {
                                                $isCorrect = in_array($option, $question['correct_answer']);
                                            } else {
                                                $isCorrect = trim((string) $question['correct_answer']) === trim((string) $option);
                                            }
                                        } else {
                                            $isCorrect = false;
                                        }
                                    @endphp
                                    <div class="flex items-center border {{ $isCorrect ? 'border-green-500' : 'border-[#4E73DF]' }}">
                                        <div class="{{ $isCorrect ? 'bg-green-500' : 'bg-[#4E73DF]' }} w-[30px] h-full flex items-center justify-center text-white">
                                            {{ $optionKeys[$key] ?? $key }}
                                        </div>
                                        <input type="text" disabled name="questions[{{ $qIndex }}][options][{{ $key }}]"
                                            value="{{ $option ?? '' }}" class="w-full m-0 border-none">
                                    </div>
                                @endforeach
                            @endif
                        </div>

                    </div>
                    <div class="col-span-1 grid grid-cols-2 gap-8 mt-2 ml-5">
                            <div>
                                <div class="flex items-center">
                                    <img src="{{asset('images/incorrect.png')}}" width="15" alt="">
                                    <label class="text-[#FF0000] text-center ml-2">incorrect</label>
                                </div>
                                <div class="flex items-center border rounded pl-2">
                                    <svg width="17" height="18" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M7.5 7C6.83696 7 6.20107 6.73661 5.73223 6.26777C5.26339 5.79893 5 5.16304 5 4.5C5 3.83696 5.26339 3.20107 5.73223 2.73223C6.20107 2.26339 6.83696 2 7.5 2C8.16304 2 8.79893 2.26339 9.26777 2.73223C9.73661 3.20107 10 3.83696 10 4.5C10 5.16304 9.73661 5.79893 9.26777 6.26777C8.79893 6.73661 8.16304 7 7.5 7ZM7.5 3C6.67 3 6 3.67 6 4.5C6 5.33 6.67 6 7.5 6C8.33 6 9 5.33 9 4.5C9 3.67 8.33 3 7.5 3Z" fill="#808080"/>
                                        <path d="M13.5 11C13.22 11 13 10.78 13 10.5C13 10.22 13.22 10 13.5 10C13.78 10 14 9.78 14 9.5C14 8.83696 13.7366 8.20107 13.2678 7.73223C12.7989 7.26339 12.163 7 11.5 7H10.5C10.22 7 10 6.78 10 6.5C10 6.22 10.22 6 10.5 6C11.33 6 12 5.33 12 4.5C12 3.67 11.33 3 10.5 3C10.22 3 10 2.78 10 2.5C10 2.22 10.22 2 10.5 2C11.163 2 11.7989 2.26339 12.2678 2.73223C12.7366 3.20107 13 3.83696 13 4.5C13 5.12 12.78 5.68 12.4 6.12C13.89 6.52 15 7.88 15 9.5C15 10.33 14.33 11 13.5 11ZM1.5 11C0.67 11 0 10.33 0 9.5C0 7.88 1.1 6.52 2.6 6.12C2.23 5.68 2 5.12 2 4.5C2 3.83696 2.26339 3.20107 2.73223 2.73223C3.20107 2.26339 3.83696 2 4.5 2C4.78 2 5 2.22 5 2.5C5 2.78 4.78 3 4.5 3C3.67 3 3 3.67 3 4.5C3 5.33 3.67 6 4.5 6C4.78 6 5 6.22 5 6.5C5 6.78 4.78 7 4.5 7H3.5C2.83696 7 2.20107 7.26339 1.73223 7.73223C1.26339 8.20107 1 8.83696 1 9.5C1 9.78 1.22 10 1.5 10C1.78 10 2 10.22 2 10.5C2 10.78 1.78 11 1.5 11ZM10.5 14H4.5C3.67 14 3 13.33 3 12.5V11.5C3 9.57 4.57 8 6.5 8H8.5C10.43 8 12 9.57 12 11.5V12.5C12 13.33 11.33 14 10.5 14ZM6.5 9C5.83696 9 5.20107 9.26339 4.73223 9.73223C4.26339 10.2011 4 10.837 4 11.5V12.5C4 12.78 4.22 13 4.5 13H10.5C10.78 13 11 12.78 11 12.5V11.5C11 10.837 10.7366 10.2011 10.2678 9.73223C9.79893 9.26339 9.16304 9 8.5 9H6.5Z" fill="#808080"/>
                                        </svg>

                                    <input disabled type="number" class="w-full p-2 border-none rounded" name="" value="0">
                                </div>

                            </div>
                            <div>
                                <div class="flex items-center">
                                    <img src="{{asset('images/correct.png')}}" width="15" alt="">
                                    <label class="text-[#00A526] text-center ml-2">correct</label>
                                </div>
                                <div class="flex items-center border rounded pl-2">
                                    <svg width="17" height="18" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M7.5 7C6.83696 7 6.20107 6.73661 5.73223 6.26777C5.26339 5.79893 5 5.16304 5 4.5C5 3.83696 5.26339 3.20107 5.73223 2.73223C6.20107 2.26339 6.83696 2 7.5 2C8.16304 2 8.79893 2.26339 9.26777 2.73223C9.73661 3.20107 10 3.83696 10 4.5C10 5.16304 9.73661 5.79893 9.26777 6.26777C8.79893 6.73661 8.16304 7 7.5 7ZM7.5 3C6.67 3 6 3.67 6 4.5C6 5.33 6.67 6 7.5 6C8.33 6 9 5.33 9 4.5C9 3.67 8.33 3 7.5 3Z" fill="#808080"/>
                                        <path d="M13.5 11C13.22 11 13 10.78 13 10.5C13 10.22 13.22 10 13.5 10C13.78 10 14 9.78 14 9.5C14 8.83696 13.7366 8.20107 13.2678 7.73223C12.7989 7.26339 12.163 7 11.5 7H10.5C10.22 7 10 6.78 10 6.5C10 6.22 10.22 6 10.5 6C11.33 6 12 5.33 12 4.5C12 3.67 11.33 3 10.5 3C10.22 3 10 2.78 10 2.5C10 2.22 10.22 2 10.5 2C11.163 2 11.7989 2.26339 12.2678 2.73223C12.7366 3.20107 13 3.83696 13 4.5C13 5.12 12.78 5.68 12.4 6.12C13.89 6.52 15 7.88 15 9.5C15 10.33 14.33 11 13.5 11ZM1.5 11C0.67 11 0 10.33 0 9.5C0 7.88 1.1 6.52 2.6 6.12C2.23 5.68 2 5.12 2 4.5C2 3.83696 2.26339 3.20107 2.73223 2.73223C3.20107 2.26339 3.83696 2 4.5 2C4.78 2 5 2.22 5 2.5C5 2.78 4.78 3 4.5 3C3.67 3 3 3.67 3 4.5C3 5.33 3.67 6 4.5 6C4.78 6 5 6.22 5 6.5C5 6.78 4.78 7 4.5 7H3.5C2.83696 7 2.20107 7.26339 1.73223 7.73223C1.26339 8.20107 1 8.83696 1 9.5C1 9.78 1.22 10 1.5 10C1.78 10 2 10.22 2 10.5C2 10.78 1.78 11 1.5 11ZM10.5 14H4.5C3.67 14 3 13.33 3 12.5V11.5C3 9.57 4.57 8 6.5 8H8.5C10.43 8 12 9.57 12 11.5V12.5C12 13.33 11.33 14 10.5 14ZM6.5 9C5.83696 9 5.20107 9.26339 4.73223 9.73223C4.26339 10.2011 4 10.837 4 11.5V12.5C4 12.78 4.22 13 4.5 13H10.5C10.78 13 11 12.78 11 12.5V11.5C11 10.837 10.7366 10.2011 10.2678 9.73223C9.79893 9.26339 9.16304 9 8.5 9H6.5Z" fill="#808080"/>
                                        </svg>

                                    <input disabled type="number" class="w-full p-2 border-none rounded" name="" value="0">
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <p class="text-red-500">No questions available for this quiz.</p>
    @endif

    </div>




</x-app-layout>
