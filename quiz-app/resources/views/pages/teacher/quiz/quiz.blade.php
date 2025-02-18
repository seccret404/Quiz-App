<x-app-layout>
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

        <!-- Cards -->
        <div class="grid grid-cols-2 gap-4">
            <div class="border border-dashed border-[2px] rounded-[5px] border-[#4E73DF]">
                <x-drag/>
            </div>

            <div class="flex flex-col justify-between p-[40px]">
                <div class="">
                        <div class="text-[16px] font-bold">Total Questions</div>
                    <input type="number" class="w-full h-[53px] rounded">
                </div>
                <div class="">
                    <div class="text-[16px] font-bold">Questions Type</div>
                <select name="" id="" class="w-full h-[53px] rounded">
                    <option value="" selected>Select</option>
                    <option value="">Multiple Choice</option>
                    <option value="">Essay</option>
                </select>
                </div>
                <button class="p-[12px] bg-[#4E73DF] text-white rounded-[5px] cursor-pointer mt-4">Generate Questions</button>
            </div>
        </div>
    </div>
    <div class="bg-white rounded p-4 m-6">
        <div class="text-black font-bold">Quesion:</div>
        <div class="text-black">Lorem, ipsum dolor sit amet consectetur adipisicing elit. Optio voluptatem harum fugiat minus. Temporibus nam numquam laborum fugiat commodi itaque.</div>
        <div class="text-black font-bold">Option</div>
        <div class="grid grid-cols-2 ">
            <div class="grid grid-cols-2 gap-2">
                @for ($i = 0; $i < 4; $i++)
                    <div class="flex border rounded justify-between bg-[#4E72DF78]">
                        <div class="bg-[#4E73DF] w-[30px] pt-1 pb-1 flex items-center justify-center text-white rounded">A</div>
                        <div class="text-black pt-1 pl-3 pr-3 pb-1 flex items-center justify-center">Answer</div>
                        <div class="pt-1 pb-1 flex items-center justify-center mr-2"><input type="checkbox" class="rounded-[25px]"></div>
                    </div>

                @endfor
            </div>
            <div class="grid grid-cols-2 gap-2 pl-4 pr-4">
                <div class="">
                    <div class="">Time Limit</div>
                    <input type="time" class="w-full">
                </div>
                <div class="">
                    <div class="">Set Point</div>
                    <input type="number" class="w-full">
                </div>
            </div>
        </div>
        <div class="flex justify-end items-center">
            <label for="sleect">Select</label> &nbsp;
            <input id="sleect" type="checkbox">
        </div>
    </div>
</x-app-layout>
