<x-student-layout>
    <div class="">
        <div class="flex flex-col justify-center items-center mt-[150px]">
            <div class="bg-white shadow flex items-center w-[400px] p-2 rounded-[10px]">
                <input type="text" placeholder="Enter quiz code" class="border-none w-3/4 h-[50px] text-[18px] rounded">
                <button class="ml-1 bg-[#3D17AE] text-white w-1/4 h-[50px] rounded-[10px] shadow">Join</button>
            </div>
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
