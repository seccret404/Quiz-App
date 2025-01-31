<!doctype html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="h-screen w-screen bg-[url('assets/images/Backgroun_Login.png')] bg-cover bg-no-repeat bg-center">

    <!-- Container utama untuk pusatkan konten -->
    <div class="flex items-center justify-center h-full">
        <div class="bg-white bg-opacity-100 p-8 rounded-lg shadow-lg  w-[461px] h-[515px]">
            <h2 class="text-[47px] font-bold mt[50px] text-center text-black">Login</h2>
            <form action="/register" method="POST" class="mt-[50px]">
                @csrf

                <div class="mb-4">
                    <label class="block text-[#000000] text-[15px] font-medium mb-2">Name</label>
                    <input type="text" name="name" required
                        class="w-full border-b-[4px] border-black bg-transparent text-black focus:outline-none focus:ring-2 focus:ring-white">
                </div>

                <div class="mb-4">
                    <label class="block text-[#000000] text-[15px] font-medium mb-2">Email</label>
                    <input type="email" name="email" required
                        class="w-full border-b-[4px] border-black bg-transparent text-black focus:outline-none focus:ring-2 focus:ring-white">
                </div>

                <div class="mb-4">
                    <label class="block text-[#000000] text-[15px] font-medium mb-2">Password</label>
                    <input type="password" name="password" required
                        class="w-full border-b-[4px] border-black bg-transparent text-black focus:outline-none focus:ring-2 focus:ring-white">
                </div>

                <div class="mt-[80px] text-center">
                    <button type="submit"
                        class="w-[100px] bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-[15px] font-bold">Register</button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>
