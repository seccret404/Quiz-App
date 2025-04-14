<x-student-layout>
    <div class="min-h-screen bg-gradient-to-b from-indigo-50 to-white overflow-hidden">
        <!-- Animated Game Elements -->
        <div class="fixed top-0 left-0 w-full h-full pointer-events-none z-0">
            <!-- Floating game characters -->
            <img src="https://cdn-icons-png.flaticon.com/512/686/686589.png"
                 class="absolute top-1/5 left-1/4 w-16 h-16 opacity-70 animate-float1">
            <img src="https://cdn-icons-png.flaticon.com/512/686/686606.png"
                 class="absolute top-1/3 right-1/4 w-14 h-14 opacity-70 animate-float2">
            <img src="https://cdn-icons-png.flaticon.com/512/686/686613.png"
                 class="absolute bottom-1/4 left-1/5 w-20 h-20 opacity-70 animate-float3">

            <!-- Floating coins -->
            <div class="absolute bottom-1/3 right-1/4 animate-coin-spin">
                <img src="https://cdn-icons-png.flaticon.com/512/3132/3132693.png" class="w-12 h-12">
            </div>

            <!-- Floating stars -->
            <div class="absolute top-1/4 right-1/5 animate-star-pulse">
                <img src="https://cdn-icons-png.flaticon.com/512/1828/1828884.png" class="w-8 h-8">
            </div>
        </div>

        <div class="flex flex-col justify-center items-center mt-[100px] relative z-10">
            <!-- Join Quiz Card with game-style animation -->
            <div class="bg-white shadow-lg flex items-center w-[400px] p-2 rounded-[10px] transform transition-all hover:scale-105 hover:shadow-xl duration-300 border-l-4 border-indigo-500 relative overflow-hidden">
                <!-- Animated border effect -->
                <div class="absolute inset-0 rounded-[10px] overflow-hidden">
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-400 to-purple-400 animate-progress-bar"></div>
                </div>

                <form action="{{ route('quiz.join') }}" method="POST" class="flex w-full relative z-10">
                    @csrf
                    <input type="text" name="code_quiz" placeholder="Enter quiz code"
                           class="border-none w-3/4 h-[50px] text-[18px] rounded px-2 focus:ring-2 focus:ring-indigo-300" required>
                    <button type="submit" class="ml-1 bg-[#3D17AE] text-white w-1/4 h-[50px] rounded-[10px] shadow hover:bg-[#2E1199] transition-colors duration-300 flex items-center justify-center relative overflow-hidden group">
                        <span class="mr-1 relative z-10">Join</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 relative z-10" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                        <!-- Button shine effect -->
                        <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                    </button>
                </form>
            </div>

            <!-- Error Modal -->
            @if (session('error'))
                <div x-data="{ showModal: true }"
                    x-init="setTimeout(() => showModal = false, 3000)"
                    x-show="showModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="fixed inset-0 flex items-center justify-center z-50 bg-[#11111164] backdrop-blur-sm">

                    <div class="bg-white p-6 rounded-lg shadow-lg w-1/3 border-l-4 border-red-500 transform transition-all">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <img src="https://cdn-icons-png.flaticon.com/512/4635/4635855.png" class="h-10 w-10">
                            </div>
                            <div class="ml-4">
                                <h2 class="text-xl font-semibold text-red-600">Oops!</h2>
                                <p class="mt-2 text-gray-700">{{ session('error') }}</p>
                                <div class="mt-4 flex justify-end">
                                    <button @click="showModal = false" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors duration-300 flex items-center">
                                        <span>Close</span>
                                        <img src="https://cdn-icons-png.flaticon.com/512/1828/1828778.png" class="h-4 w-4 ml-1">
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quiz Cards Section -->
            <div class="w-full max-w-6xl px-4 mt-[70px]">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <img src="https://cdn-icons-png.flaticon.com/512/2936/2936886.png" class="h-8 w-8 mr-2">
                    Your Available Quizzes
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @for ($i = 0; $i < 5; $i++)
                        <div class="border bg-white shadow-md rounded-lg text-[14px] text-gray-600 p-6 transform transition-all hover:scale-105 hover:shadow-lg duration-300 relative overflow-hidden group">
                            <!-- Ribbon for featured quizzes -->
                            @if($i === 0)
                                <div class="absolute top-0 right-0 bg-yellow-400 text-yellow-900 text-xs font-bold px-3 py-1 transform rotate-45 translate-x-8 -translate-y-1 w-32 text-center shadow-md animate-ribbon-wave">
                                    <img src="https://cdn-icons-png.flaticon.com/512/2583/2583344.png" class="h-4 w-4 inline-block mr-1">
                                    New!
                                </div>
                            @endif

                            <div class="flex justify-center mb-4">
                                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center shadow-inner relative overflow-hidden">
                                    <img src="https://cdn-icons-png.flaticon.com/512/3132/3132693.png" class="absolute top-0 left-0 w-full h-full object-cover opacity-10">
                                    <img src="https://cdn-icons-png.flaticon.com/512/3132/3132739.png" class="h-10 w-10 animate-bounce-slow">
                                </div>
                            </div>

                            <h2 class="text-[18px] text-gray-800 font-semibold text-center mb-2">Quiz {{ $i+1 }}</h2>
                            <div class="space-y-2 mb-4">
                                <p class="flex items-center">
                                    <img src="https://cdn-icons-png.flaticon.com/512/3159/3159070.png" class="h-5 w-5 mr-2">
                                    Questions: 12
                                </p>
                                <p class="flex items-center">
                                    <img src="https://cdn-icons-png.flaticon.com/512/2088/2088617.png" class="h-5 w-5 mr-2">
                                    Duration: 30 min
                                </p>
                                <p class="flex items-center">
                                    <img src="https://cdn-icons-png.flaticon.com/512/747/747310.png" class="h-5 w-5 mr-2">
                                    Ends: Tomorrow
                                </p>
                            </div>

                            <button class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-2 rounded-md mt-2 shadow-md hover:from-indigo-700 hover:to-purple-700 transition-all duration-300 flex items-center justify-center group-hover:animate-pulse">
                                <img src="https://cdn-icons-png.flaticon.com/512/1828/1828509.png" class="h-5 w-5 mr-2">
                                <span>Start Quiz</span>
                                <img src="https://cdn-icons-png.flaticon.com/512/271/271226.png" class="h-4 w-4 ml-2 transform group-hover:translate-x-1 transition-transform duration-300">
                            </button>

                            <!-- XP badge that appears on hover -->
                            <div class="absolute bottom-2 right-2 bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center">
                                <img src="https://cdn-icons-png.flaticon.com/512/3132/3132692.png" class="h-3 w-3 mr-1">
                                +50 XP
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Character animations */
        @keyframes float1 {
            0%, 100% { transform: translateY(0) rotate(0deg) scale(1); }
            50% { transform: translateY(-20px) rotate(5deg) scale(1.1); }
        }
        @keyframes float2 {
            0%, 100% { transform: translateY(0) rotate(0deg) scale(1); }
            50% { transform: translateY(-15px) rotate(-3deg) scale(1.05); }
        }
        @keyframes float3 {
            0%, 100% { transform: translateY(0) rotate(0deg) scale(1); }
            50% { transform: translateY(-25px) rotate(7deg) scale(1.15); }
        }

        /* Coin spin animation */
        @keyframes coin-spin {
            0% { transform: rotateY(0deg) scale(1); }
            50% { transform: rotateY(180deg) scale(1.1); }
            100% { transform: rotateY(360deg) scale(1); }
        }

        /* Star pulse animation */
        @keyframes star-pulse {
            0%, 100% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.3); opacity: 1; }
        }

        /* Progress bar animation */
        @keyframes progress-bar {
            0% { background-position: 0% 50%; }
            100% { background-position: 100% 50%; }
        }

        /* Bounce animation for icons */
        @keyframes bounce-slow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        /* Ribbon wave animation */
        @keyframes ribbon-wave {
            0%, 100% { transform: rotate(45deg) translateX(8px) translateY(-1px); }
            50% { transform: rotate(45deg) translateX(8px) translateY(-3px); }
        }

        /* Button pulse animation */
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        /* Apply animations */
        .animate-float1 { animation: float1 6s ease-in-out infinite; }
        .animate-float2 { animation: float2 7s ease-in-out infinite; }
        .animate-float3 { animation: float3 5s ease-in-out infinite; }

        .animate-coin-spin { animation: coin-spin 3s linear infinite; }
        .animate-star-pulse { animation: star-pulse 2s ease-in-out infinite; }
        .animate-progress-bar {
            background: linear-gradient(90deg, #818cf8, #a78bfa, #c084fc, #e879f9);
            background-size: 300% 100%;
            animation: progress-bar 3s linear infinite;
        }
        .animate-bounce-slow { animation: bounce-slow 3s ease-in-out infinite; }
        .animate-ribbon-wave { animation: ribbon-wave 2s ease-in-out infinite; }
        .animate-pulse { animation: pulse 1s ease-in-out infinite; }
    </style>
</x-student-layout>
