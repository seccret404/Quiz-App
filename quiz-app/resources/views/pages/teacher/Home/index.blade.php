<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard Header -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Dashboard</h1>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <x-datepicker />
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-12 gap-6 mb-6">
            <!-- Quiz Completed Card -->
            <div class="flex flex-col col-span-full sm:col-span-6 xl:col-span-4 bg-white dark:bg-gray-800 shadow-lg rounded-xl">
                <div class="px-5 pt-5">
                    <header class="flex justify-between items-start mb-2">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Quiz Completed</h2>
                    </header>
                    <div class="flex items-start mb-6">
                        <div class="text-3xl font-bold text-gray-800 dark:text-gray-100 mr-2">
                            {{ $completed }}
                        </div>
                        <div class="text-sm font-medium text-green-700 px-1.5 bg-green-500/20 rounded-full">Done</div>
                    </div>
                </div>
            </div>

            <!-- Quiz Ongoing Card -->
            <div class="flex flex-col col-span-full sm:col-span-6 xl:col-span-4 bg-white dark:bg-gray-800 shadow-lg rounded-xl">
                <div class="px-5 pt-5">
                    <header class="flex justify-between items-start mb-2">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Quiz Ongoing</h2>
                    </header>
                    <div class="flex items-start mb-6">
                        <div class="text-3xl font-bold text-gray-800 dark:text-gray-100 mr-2">
                            {{ $ongoing }}
                        </div>
                        <div class="text-sm font-medium text-orange-700 px-1.5 bg-orange-500/20 rounded-full">Ongoing</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Score Distribution Chart -->
        <div class="flex flex-col col-span-full bg-white dark:bg-gray-800 shadow-xs rounded-xl">
            <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                <div class="flex justify-between items-center">
                    <h2 class="font-semibold text-gray-800 dark:text-gray-100">Score Distribution</h2>
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        Total Participants: {{ $totalParticipants }}
                    </span>
                </div>
            </header>
            <div class="px-5 py-3">
                <ul class="flex flex-wrap gap-x-4">
                    <li class="inline-flex items-center">
                        <span class="block w-3 h-3 mr-2 rounded-full bg-blue-500"></span>
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Participants</span>
                    </li>
                    <li class="inline-flex items-center">
                        <span class="block w-3 h-3 mr-2 rounded-full bg-orange-500"></span>
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Highlight Range (45-50)</span>
                    </li>
                </ul>
            </div>
            <div class="grow relative min-h-[300px] p-4">
                <canvas id="scoreDistributionChart"></canvas>
            </div>
        </div>
       <!-- Leaderboard Table -->
    <div class="mt-8 flex flex-col col-span-full bg-white dark:bg-gray-800 shadow-lg rounded-xl">
        <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
            <h2 class="font-semibold text-gray-800 dark:text-gray-100">Top Participants</h2>
        </header>
        <div class="p-3">
            <div class="overflow-x-auto">
                <table class="table-auto w-full">
                    <thead class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-700/60">
                        <tr>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">Rank</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">Student</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">Average</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">Quizzes Taken</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">Highest Score</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">Total Score</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($topParticipants as $index => $participant)
                        <tr>
                            <td class="p-2 whitespace-nowrap">
                                <div class="text-left font-medium text-gray-800 dark:text-gray-100">
                                    #{{ $index + 1 }}
                                </div>
                            </td>
                            <td class="p-2 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="font-medium text-gray-800 dark:text-gray-100">{{ $participant['name'] }}</div>
                                </div>
                            </td>
                            <td class="p-2 whitespace-nowrap">
                                <div class="text-left text-gray-800 dark:text-gray-100">{{ $participant['average_score'] }}</div>
                            </td>
                            <td class="p-2 whitespace-nowrap">
                                <div class="text-left text-gray-800 dark:text-gray-100">{{ $participant['attempt_count'] }}</div>
                            </td>
                            <td class="p-2 whitespace-nowrap">
                                <div class="text-left text-gray-800 dark:text-gray-100">{{ $participant['highest_score'] }}</div>
                            </td>
                            <td class="p-2 whitespace-nowrap">
                                <div class="text-left font-bold text-gray-800 dark:text-gray-100">{{ $participant['total_score'] }}</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-4 text-center text-gray-500 dark:text-gray-400">
                                No participants data available
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data dari controller
            const scoreRanges = @json($scoreRanges);
            const scoreData = @json($scoreDistribution);

            // Konfigurasi warna
            const backgroundColors = scoreRanges.map((range, index) => {
                return range === '45-50' ? 'rgba(255, 159, 64, 0.7)' : 'rgba(54, 162, 235, 0.7)';
            });

            const borderColors = scoreRanges.map((range, index) => {
                return range === '45-50' ? 'rgba(255, 159, 64, 1)' : 'rgba(54, 162, 235, 1)';
            });

            // Dapatkan context canvas
            const ctx = document.getElementById('scoreDistributionChart').getContext('2d');

            // Buat chart
            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: scoreRanges,
                    datasets: [{
                        label: 'Number of Participants',
                        data: scoreData,
                        backgroundColor: backgroundColors,
                        borderColor: borderColors,
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.raw}`;
                                },
                                title: function(context) {
                                    return `Score Range: ${context[0].label}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: Math.max(...scoreData) > 10 ? 5 : 1,
                                precision: 0
                            },
                            title: {
                                display: true,
                                text: 'Number of Participants'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Score Ranges'
                            }
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeInOutQuad'
                    }
                }
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                chart.resize();
            });
        });
        </script>

    </div>
</x-app-layout>
