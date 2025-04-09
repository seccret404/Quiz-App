<x-app-layout>
    <style>
        /* Untuk efek hover yang lebih halus */
        .bg-gray-50:hover {
            background-color: #f3f4f6;
        }

        /* Untuk warna ranking 1 */
        .bg-yellow-100 {
            background-color: #fef9c3;
        }
        .text-yellow-600 {
            color: #ca8a04;
        }
    </style>
    <div class="container mx-auto px-4 py-8 max-w-3xl">
        <h1 class="text-3xl font-bold text-center mb-8">Leaderboard</h1>

        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-semibold mb-6 text-center">RANKINGS</h2>

            <!-- Top 3 Participants -->
            <div class="flex justify-center space-x-8 mb-8">
                @if(isset($leaderboard[1])) <!-- Peringkat 2 -->
                <div class="text-center">
                    <div class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-2">
                        <span class="text-2xl font-bold text-gray-600">2</span>
                    </div>
                    <p class="text-lg font-semibold">{{ $leaderboard[1]['user_name'] }}</p>
                    <p class="text-sm text-gray-500">SCORE {{ $leaderboard[1]['score'] }}</p>
                </div>
                @endif

                @if(isset($leaderboard[0])) <!-- Peringkat 1 -->
                <div class="text-center">
                    <div class="bg-yellow-100 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-2">
                        <span class="text-3xl font-bold text-yellow-600">1</span>
                    </div>
                    <p class="text-lg font-semibold">{{ $leaderboard[0]['user_name'] }}</p>
                    <p class="text-sm text-gray-500">SCORE {{ $leaderboard[0]['score'] }}</p>
                </div>
                @endif

                @if(isset($leaderboard[2])) <!-- Peringkat 3 -->
                <div class="text-center">
                    <div class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-2">
                        <span class="text-2xl font-bold text-gray-600">3</span>
                    </div>
                    <p class="text-lg font-semibold">{{ $leaderboard[2]['user_name'] }}</p>
                    <p class="text-sm text-gray-500">SCORE {{ $leaderboard[2]['score'] }}</p>
                </div>
                @endif
            </div>

            <hr class="my-6 border-gray-200">

            <!-- Other Participants -->
            <div class="space-y-4">
                @foreach($leaderboard as $index => $entry)
                    @if($index >= 3) <!-- Mulai dari peringkat 4 -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <span class="text-xl font-bold w-8">{{ $entry['rank'] }}</span>
                                <p class="text-lg font-semibold ml-4">{{ $entry['user_name'] }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">SCORE</p>
                                <p class="text-lg font-bold">{{ $entry['score'] }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
