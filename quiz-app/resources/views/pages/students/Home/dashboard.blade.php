<x-student-layout>
    <div class="">
        <div class="ml-[100px] mr-[100px]">
            <div class="grid grid-cols-2 gap-4 mt-[60px]">
                <div class="border shadow flex justify-center items-center rounded-[10px] p-[50px]">
                    <div class="bg-white shadow flex items-center w-[400px] p-2 rounded-[10px]">
                        <input type="text" placeholder="Enter quiz code" class="border-none w-3/4 h-[50px] text-[18px] rounded">
                        <button class="ml-1 bg-[#3D17AE] text-white w-1/4 h-[50px] rounded-[10px] shadow">Join</button>
                    </div>
                </div>
                 @php
                    $user = session('user');
                @endphp
                <div class="bg-gradient-to-br from-white to-purple-700 border shadow p-[30px] rounded-[10px]">
                    <div class="grid grid-cols2 gap-2">
                        <div class="text-[18px] font-bold">Helo, {{ $user['name'] ?? 'Dashboard' }}</div>
                        <div class="">
                            <img src="" alt="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-4 gap-6 mt-[70px]">
                @foreach ($quizData as $quiz)
                    <div class="border bg-white shadow rounded-lg text-[12px] text-[#808080] text-center p-[25px]">
                        <h2 class="text-[18px] text-black font-semibold">{{ $quiz['nama_quiz'] }}</h2>
                        <p class="m-4">Total Questions: {{ $quiz['total_questions'] }}</p>
                        <p>Total Contribution: {{ $quiz['total_contribution'] }}</p>

                        <div class="grid grid-cols-2 gap-2">
                            <a href="{{ route('quiz.questions', $quiz['id']) }}" class="bg-[#3D17AE] text-white p-2 text-center cursor-pointer rounded mt-4 w-full flex items-center">
                                <img width="15" height="15" class="mr-2" src="{{asset('images/eye.png')}}" alt=""/>Questions
                            </a>
                            <a href="{{ route('quiz.leaderboard', $quiz['id']) }}" class="bg-[#3D17AE] text-white p-2 text-center cursor-pointer rounded mt-4 w-full flex items-center">
                                <img width="15" height="15" class="mr-2" src="{{asset('images/eye.png')}}" alt=""/>Leaderboard
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-student-layout>
