<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Quiz Completed</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white flex justify-center items-center h-screen">
    <div class="w-[500px] text-center bg-gray-800 p-6 rounded-lg shadow-lg">
        <h1 class="text-3xl font-bold mb-4">Quiz Selesai!</h1>
        <p class="text-lg mb-4">Selamat! Kamu telah menyelesaikan quiz.</p>

        <h2 class="text-2xl font-bold mb-3">Leaderboard</h2>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-700">
                    <th class="p-2">Rank</th>
                    <th class="p-2">User</th>
                    <th class="p-2">Score</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($leaderboard as $index => $attempt)
                    <tr class="border-b border-gray-600 {{ $index == 0 ? 'text-yellow-400 font-bold' : '' }}">
                        <td class="p-2">{{ $index + 1 }}</td>
                        <td class="p-2">{{$attempt['student_name']}}</td>
                        <td class="p-2">{{ $attempt['score'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <a href="{{ route('my.dashboard') }}" class="mt-4 inline-block bg-blue-500 px-4 py-2 rounded">Kembali ke Dashboard</a>
    </div>
</body>
</html>
