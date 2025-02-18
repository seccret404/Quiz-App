<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="bg-white shadow-lg rounded-lg flex w-[997px] h-[609px]">

        <div class="w-1/2 w-[399px] flex items-center justify-center rounded-l-lg">
            <img src="assets/images/image_login.png" alt="Register Image"
                class="w-full h-full object-cover rounded-l-lg">
        </div>


        <div class="w-3/4 pt-12 pl-12 pr-12 flex flex-col w-[598px]">
            <div class="flex flex-col items-center mb-6">
                <img src="assets/images/logo_kuis.png" alt="QuizVerse Logo" class="h-[80px] w-[120px]">
            </div>
            <div class="grid">
                <h2 class="text-[32px] text-center text-black">
                    Join <span class="block">QuizVerse!</span>
                </h2>
            </div>

            <form action="/register" method="POST" class="mt-6">
                @csrf

                <div class="mb-4">
                    <input type="text" name="name" placeholder="Enter Your Name"
                        class="w-full p-3 border rounded-[10px]" required>
                </div>


                <div class="mb-4">
                    <input type="email" name="email" placeholder="Enter Email Address..."
                        class="w-full p-3 border rounded-[10px]" required>
                </div>


                <div class="mb-4 relative">
                    <input type="password" id="password" name="password" placeholder="Password"
                        class="w-full p-3 border rounded-[10px] pr-10" required>
                    <button type="button" onclick="togglePassword()" class="absolute right-3 top-3 text-gray-500">
                        <i id="toggleIcon" class="bi bi-eye"></i>
                    </button>
                </div>


                <div class="text-center">
                    <button type="submit"
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white py-3 rounded-[50px] font-bold">
                        Register
                    </button>
                </div>
            </form>

            <div class="text-center mt-6 text-sm">
                <a href="{{route('login')}}" class="text-blue-500 hover:underline">Forgot Your Password?</a>
            </div>

            <div class="text-center mt-2 text-sm">
                <span>Already have an account? </span>
                <a href="/" class="text-blue-500 hover:underline">Login here!</a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            let passwordField = document.getElementById("password");
            let toggleIcon = document.getElementById("toggleIcon");

            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleIcon.classList.remove("bi-eye");
                toggleIcon.classList.add("bi-eye-slash");
            } else {
                passwordField.type = "password";
                toggleIcon.classList.remove("bi-eye-slash");
                toggleIcon.classList.add("bi-eye");
            }
        }
    </script>

</body>

</html>
