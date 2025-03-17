<x-app-layout>
    {{-- style form file  --}}
    <style>
        * {
            font-family: Arial, sans-serif;
            box-sizing: border-box;
        }

        .upload-container {
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            width: 350px;
            margin: 50px auto;
        }

        .upload-icon {
            font-size: 40px;
            color: #4A90E2;
        }

        .upload-text {
            color: #777;
            margin: 10px 0;
            font-size: 18px;
        }

        .upload-btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 18px;
            background-color: #4E73DF;
            color: #fff;
            border: none;
            border-radius: 40px;
            cursor: pointer;
            font-weight: bold;
        }

        .upload-btn:hover {
            background-color: #357ABD;
        }

        .file-input {
            display: none;
        }

        .format-text {
            color: #777;
            font-size: 18px;
            margin-top: 10px;
        }
    </style>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif


    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Generate Question</h1>
            </div>

            <!-- Right: Actions -->
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <!-- Datepicker built with flatpickr -->
                <x-datepicker />
            </div>
        </div>

        {{-- loader componen  --}}
        <x-loader />

        <!-- Cards Form Generate Soal -->
        <form id="generateForm" action="{{ route('generate.quiz') }}" method="POST" enctype="multipart/form-data"
            class="grid grid-cols-2 gap-4">
            @csrf

            <div class="border border-dashed border-[2px] rounded-[5px] border-[#4E73DF]">
                <div class="upload-container" id="drop-zone">
                    <div class="upload-icon">📄</div>
                    <p class="upload-text">Drag and Drop file here</p>
                    <p>Or</p>
                    <label for="file-input" class="upload-btn">Browse File</label>
                    <input type="file" name="pdf" id="file-input" class="file-input" accept="application/pdf">
                    <p class="format-text">Formats: pdf</p>
                    <p id="file-name-display" class="text-gray-700 mt-2"></p>
                </div>
            </div>

            <div class="flex flex-col justify-between p-[40px]">
                <div class="">
                    <div class="text-[16px] font-bold">Total Questions</div>
                    <input type="number" name="total_questions" class="w-full h-[53px] rounded">
                </div>
                <div class="">
                    <div class="text-[16px] font-bold">Questions Type</div>
                    <select name="question_type" id="" class="w-full h-[53px] rounded">
                        <option selected>Select</option>
                        <option value="Multiple Choice">Multiple Choice</option>
                        <option value="Essay">Essay</option>
                        <option value="True False">True False</option>
                    </select>
                </div>
                <button type="submit" class="p-[12px] bg-[#4E73DF] text-white rounded-[5px] mt-4">Generate
                    Questions</button>
            </div>
        </form>
    </div>
    <hr>

    <form action="{{ route('save.quiz') }}" method="POST" id="quizForm" enctype="multipart/form-data">
        @csrf
        {{-- <input type="hidden" name="id_user" value="{{ auth()->user()->id }}"> --}}
        <input type="hidden" name="type_quiz" value="{{ session('question_type') }}">

        @if (isset($questions) && is_array($questions))
            @foreach ($questions as $qIndex => $question)
                <div class="bg-white rounded p-4 m-6 shadow-md">
                    <div class="text-black font-bold">Question {{ $qIndex + 1 }}:</div>
                    <textarea name="questions[{{ $qIndex }}][question]" class="w-full p-2 border rounded"> {{ $question['question'] ?? 'No question text' }} </textarea>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="col-span-2">
                            @if (session('question_type') == 'Multiple Choice')
                                <div class="text-black font-bold mt-2">Options</div>
                                <div class="grid grid-cols-2 gap-4">
                                    @if (isset($question['options']) && is_array($question['options']))
                                        @foreach ($question['options'] as $key => $option)
                                            <div class="flex items-center border border-[#4E73DF]">
                                                <div
                                                    class="bg-[#4E73DF] w-[30px] h-full flex items-center justify-center text-white">
                                                    {{ $key }}
                                                </div>
                                                <input type="text"
                                                    name="questions[{{ $qIndex }}][options][{{ $key }}]"
                                                    value="{{ $option ?? '' }}" class="w-full m-0 border-none">
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="grid grid-cols-3 gap-4 mt-4 timer col-span-1">
                            <div>
                                <label class="font-bold">Time Limit</label>
                                <input type="number" class="w-full p-2 border rounded"
                                    name="questions[{{ $qIndex }}][time_limit]" value="0">
                            </div>
                            <div>
                                <label class="font-bold">Set Point</label>
                                <input type="number" class="w-full p-2 border rounded"
                                    name="questions[{{ $qIndex }}][point]" value="0">
                            </div>
                            <div>
                                <label class="font-bold">Quiz Level</label>
                                <select name="questions[{{ $qIndex }}][level]" class="w-full p-2 border rounded">
                                    <option value="easy"
                                        {{ isset($question['level']) && $question['level'] == 'easy' ? 'selected' : '' }}>
                                        Easy</option>
                                    <option value="medium"
                                        {{ isset($question['level']) && $question['level'] == 'medium' ? 'selected' : '' }}>
                                        Medium</option>
                                    <option value="high"
                                        {{ isset($question['level']) && $question['level'] == 'high' ? 'selected' : '' }}>
                                        High</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="text-black">Correct Answer:</label> <br>
                        @if (session('question_type') == 'Essay')
                            <textarea name="questions[{{ $qIndex }}][answer]"
                                class="bg-[#28A745] w-full font-medium text-white border-none p-1 rounded">{{ $question['answer'] ?? '' }}</textarea>
                        @elseif (session('question_type') == 'True False')
                            <select name="questions[{{ $qIndex }}][answer]"
                                class="bg-[#28A745] w-[100px] font-medium text-white border-none p-1 rounded">
                                <option value="True"
                                    {{ isset($question['answer']) && $question['answer'] == 'True' ? 'selected' : '' }}>
                                    True</option>
                                <option value="False"
                                    {{ isset($question['answer']) && $question['answer'] == 'False' ? 'selected' : '' }}>
                                    False</option>
                            </select>
                        @else
                            <input type="text" name="questions[{{ $qIndex }}][answer]"
                                value="{{ $question['answer'] ?? '' }}"
                                class="bg-[#28A745] w-[30px] font-medium text-white text-center border-none p-1 rounded">
                        @endif
                    </div>

                    <div class="mt-4">
                        <label class="text-black font-bold">Feedback:</label>
                        <textarea name="questions[{{ $qIndex }}][feedback]" class="w-full p-2 border rounded"> {{ $question['feedback'] ?? '' }}</textarea>
                    </div>
                    <div class="flex justify-end items-center mt-2">
                        <label for="select-{{ $qIndex }}" class="mr-2">Select Question</label>
                        <input id="select-{{ $qIndex }}" type="checkbox"
                            name="questions[{{ $qIndex }}][select]" value="1">
                    </div>
                </div>
            @endforeach
        @else
            <p class="text-center text-[18px] font-bold mt-4">No questions found.</p>
        @endif

        @if ($questions != null)
            <!-- Tombol Generate Quiz -->
            <div class="text-right p-4">
                <button type="button" id="openModal"
                    class="bg-[#4E73DF] p-3 rounded w-[300px] text-white font-mediumc">
                    Generate Quiz
                </button>
            </div>
        @endif


        <!-- Modal Pop-up untuk Input Quiz -->
        <div id="quizModal" class="hidden fixed inset-0 bg-[#ffffff] shadow-lg flex justify-center items-center">
            <div class="bg-white shadow-lg p-[40px] rounded w-[380px]">
                <h2 class="text-xl font-bold text-[24px] text-center mb-4">Generate Quiz</h2>

                <label>Enter Quiz Name</label>
                <input type="text" name="nama_quiz" required class="w-full p-2 border rounded mb-2">

                <label>Enter Quiz Code</label>
                <input type="text" name="code_quiz" required class="w-full p-2 border rounded mb-2">

                <label>Quiz Start </label>
                <input type="datetime-local" name="start_time" required class="w-full p-2 border rounded mb-2">

                <label>Quiz End</label>
                <input type="datetime-local" name="end_time" required class="w-full p-2 border rounded mb-2">

                <div class="grid grid-cols-2 gap-2 mt-4">

                    <button type="button" id="closeModal"
                        class="px-6 py-3 bg-gray-400 text-white rounded  hover:bg-gray-500 transition-all">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-3 bg-[#4E73DF] text-white rounded">
                        Save Quiz
                    </button>
                </div>
            </div>
        </div>

    </form>
    <script>
        document.getElementById('openModal').addEventListener('click', function() {
            document.getElementById('quizModal').classList.remove('hidden');
        });

        document.getElementById('closeModal').addEventListener('click', function() {
            document.getElementById('quizModal').classList.add('hidden');
        });
    </script>

    {{-- script untuk loader  --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const generateForm = document.getElementById('generateForm');
            const loader = document.getElementById('loader');

            generateForm.addEventListener('submit', function() {
                // Tampilkan loader saat form di-submit
                loader.classList.remove('hidden');
            });
        });
    </script>

    {{-- script untuk control file pdf  --}}
    <script>
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('file-input');
        const fileNameDisplay = document.getElementById('file-name-display');

        // Saat drag di atas drop zone
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = "#357ABD";
        });

        // Saat drag keluar dari drop zone
        dropZone.addEventListener('dragleave', () => {
            dropZone.style.borderColor = "#4A90E2";
        });

        // Saat file di-drop ke drop zone
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = "#4A90E2";
            const files = e.dataTransfer.files;
            if (files.length > 0 && files[0].type === "application/pdf") {

                fileNameDisplay.textContent = "File uploaded: " + files[0].name;

                fileInput.files = files;
            } else {
                fileNameDisplay.textContent = "Only PDF files are allowed!";
            }
        });

        // select file dari btn
        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file && file.type === "application/pdf") {
                fileNameDisplay.textContent = "File uploaded: " + file.name;
            } else {
                fileNameDisplay.textContent = "Only PDF files are allowed!";
            }
        });
    </script>

</x-app-layout>
