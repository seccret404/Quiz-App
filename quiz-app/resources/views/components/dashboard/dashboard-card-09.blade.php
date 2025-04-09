<div class="flex flex-col col-span-full sm:col-span-6 bg-white dark:bg-gray-800 shadow-xs rounded-xl">
    <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
        <h2 class="font-semibold text-gray-800 dark:text-gray-100">Score Distribution</h2>
    </header>
    <div class="px-5 py-3">
        <ul class="flex flex-wrap gap-x-4">
            <li class="inline-flex items-center">
                <span class="block w-3 h-3 mr-2 rounded-full bg-blue-500"></span>
                <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Participants</span>
            </li>
        </ul>
    </div>
    <div class="grow relative">
        <!-- Loading indicator -->
        <div id="chart-loading" class="absolute inset-0 flex items-center justify-center">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
        </div>
        <canvas id="dashboard-card-09" width="595" height="248"></canvas>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('dashboard-card-09').getContext('2d');
    const loadingElement = document.getElementById('chart-loading');

    // Initialize empty chart first
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['5-10', '15-20', '25-30', '35-40', '45-50', '55-60', '65-70', '75-80', '85-90', '95-100'],
            datasets: [{
                label: 'Participants',
                data: [],
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });

    // Show loading indicator
    loadingElement.style.display = 'flex';
    ctx.canvas.style.opacity = '0.5';

    // Fetch data from Laravel endpoint
    fetch('{{ route("quiz.attempts.data") }}')
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (!data.success) throw new Error(data.message || 'Failed to load data');

            const scores = data.scores;
            const scoreRanges = ['5-10', '15-20', '25-30', '35-40', '45-50', '55-60', '65-70', '75-80', '85-90', '95-100'];
            const bins = Array(scoreRanges.length).fill(0);

            scores.forEach(score => {
                const numScore = parseInt(score);
                if (numScore >= 5 && numScore <= 10) bins[0]++;
                else if (numScore >= 15 && numScore <= 20) bins[1]++;
                else if (numScore >= 25 && numScore <= 30) bins[2]++;
                else if (numScore >= 35 && numScore <= 40) bins[3]++;
                else if (numScore >= 45 && numScore <= 50) bins[4]++;
                else if (numScore >= 55 && numScore <= 60) bins[5]++;
                else if (numScore >= 65 && numScore <= 70) bins[6]++;
                else if (numScore >= 75 && numScore <= 80) bins[7]++;
                else if (numScore >= 85 && numScore <= 90) bins[8]++;
                else if (numScore >= 95 && numScore <= 100) bins[9]++;
            });

            // Update chart data
            chart.data.datasets[0].data = bins;
            chart.data.datasets[0].backgroundColor = scoreRanges.map((_, i) =>
                i === 4 ? 'rgba(255, 159, 64, 0.7)' : 'rgba(54, 162, 235, 0.7)'
            );
            chart.data.datasets[0].borderColor = scoreRanges.map((_, i) =>
                i === 4 ? 'rgba(255, 159, 64, 1)' : 'rgba(54, 162, 235, 1)'
            );

            // Update chart options
            chart.options = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${context.raw}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: Math.max(...bins) > 10 ? 5 : 1
                        }
                    }
                }
            };

            chart.update();
        })
        .catch(error => {
            console.error('Error:', error);
            // Optionally show error message to user
        })
        .finally(() => {
            loadingElement.style.display = 'none';
            ctx.canvas.style.opacity = '1';
        });
});
</script>
@endpush
