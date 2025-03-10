<x-student-layout>
    <div class="">
        <div class="flex flex-col justify-center items-center mt-[150px]">
            <div class="bg-white shadow flex items-center w-[400px] p-2 rounded-[10px]">
                <form action="{{ route('quiz.join') }}" method="POST" class="flex w-full">
                    @csrf
                    <input type="text" name="code_quiz" placeholder="Enter quiz code"
                           class="border-none w-3/4 h-[50px] text-[18px] rounded px-2" required>
                    <button type="submit" class="ml-1 bg-[#3D17AE] text-white w-1/4 h-[50px] rounded-[10px] shadow">
                        Join
                    </button>
                </form>
            </div>

            @if (session('error'))
                <div x-data="{ showModal: true }"
                    x-init="setTimeout(() => showModal = false, 1000)"
                    x-show="showModal"
                    class="fixed inset-0 flex items-center justify-center z-50 bg-[#11111164] backdrop-blur-sm transition-opacity duration-300"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-300"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0">

                    <div class="bg-white p-6 rounded-lg shadow-lg w-1/3 border-l-4 border-red-500">
                        <h2 class="text-xl font-semibold text-red-600">Error!</h2>
                        <p class="mt-2 text-gray-700">{{ session('error') }}</p>
                        <div class="mt-4 flex justify-end">
                            <button @click="showModal = false" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            @endif


            <div class="grid grid-cols-4 gap-6 mt-[70px]">
                @for ($i = 0; $i < 5; $i++)
                    <div class="border bg-white shadow rounded-lg text-[12px] text-[#808080] text-center p-[25px]">
                        <h2 class="text-[18px] text-black font-semibold">Quiz Name</h2>
                        <p class="m-4">Total Questions:  12</p>
                        <p>Quiz start:  </p>
                        <p>Quiz end:  </p>

                        <button class="bg-[#3D17AE] text-white px-4 py-2 cursor-pointer rounded mt-4 w-full">Start Quiz</button>
                    </div>
                @endfor
            </div>
        </div>


    </div>
</x-student-layout>
