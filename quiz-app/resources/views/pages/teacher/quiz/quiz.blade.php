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
        <x-loader/>

        <!-- Cards Form Generate Soal -->
            <form id="generateForm" action="{{route('generate.quiz')}}" method="POST" enctype="multipart/form-data" class="grid grid-cols-2 gap-4">
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
                        <option   selected>Select</option>
                        <option value="Multiple Choice">Multiple Choice</option>
                        <option value="Essay">Essay</option>
                    </select>
                    </div>
                    <button type="submit" class="p-[12px] bg-[#4E73DF] text-white rounded-[5px] mt-4">Generate Questions</button>
                </div>
            </form>
    </div>
    <hr>

    <form action="">
        @if(isset($questions) && is_array($questions))
        @foreach($questions as $qIndex => $question)
        <div class="bg-white rounded p-4 m-6">

            <div class="text-black font-bold">Question {{ $qIndex+1 }}:</div>
            <div class="text-black">
                {{ $question['question'] ?? 'No question text' }}
            </div>

            {{-- menampilkan jawaban diisni --}}
            <div class="text-black font-bold mt-2">Options</div>
            <div class="grid grid-cols-2">
                <div class="grid grid-cols-2 gap-2">
                    @if(isset($question['options']) && is_array($question['options']))
                        @foreach($question['options'] as $index => $option)
                        <div class="flex border rounded bg-[#4E72DF78]">
                            <!-- Label A, B, C, D -->
                            <div class="bg-[#4E73DF] w-[30px] pt-1 pb-1 flex items-center justify-center text-white rounded">
                                {{ chr(65 + $index) }}
                            </div>
                            <!-- Isi opsi -->
                            <div class="text-black text-start pt-1 pl-3 pr-3 pb-1 flex items-center justify-center">
                                {{ $option }}
                            </div>
                            <!-- Checkbox -->
                        </div>
                        @endforeach
                    @endif
                </div>

                <!-- Bagian Time Limit & Set Point -->
                <div class="grid grid-cols-2 gap-2 pl-4 pr-4">
                    <div class="">
                        <div class="">Time Limit</div>
                        <input type="time" class="w-full" name="time_limit[{{ $qIndex }}]">
                    </div>
                    <div class="">
                        <div class="">Set Point</div>
                        <input type="number" class="w-full" name="point[{{ $qIndex }}]">
                    </div>
                </div>
            </div>

            <!-- Select (opsional) -->
            <div class="flex justify-end items-center mt-2">
                <label for="select-{{ $qIndex }}">Select</label> &nbsp;
                <input id="select-{{ $qIndex }}" type="checkbox" name="select_question[{{ $qIndex }}]">
            </div>
        </div>
        @endforeach
    @else
    <p class="text-center text-[18px] font-bold mt-4">No questions found.</p>
    @endif
    </form>

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
