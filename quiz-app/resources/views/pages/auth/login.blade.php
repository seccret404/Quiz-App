<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="bg-white shadow-lg rounded-lg flex flex-wrap w-[997px] h-auto">

        <div class="w-1/2 w-[399px] flex items-center justify-center rounded-l-lg">
            <img src="assets/images/image_login.png" alt="Login Image" class="w-full h-full object-cover rounded-l-lg">
        </div>

        <div class="w-3/4 pt-12 pl-12 pr-12 flex flex-col item-center w-[598px]">
            <div class="flex flex-col items-center mb-6">
                <img src="assets/images/logo_kuis.png" alt="QuizVerse Logo" class="h-[80px] w-[120px]">
            </div>
            <div class="grid">
                <h2 class="text-[32px]  text-center text-black">
                    Welcome to
                    <span class="block">QuizVerse!</span>
                </h2>
            </div>

            <form action="/login" method="POST" class="mt-6">
                @csrf
                @if ($errors->has('email'))
                    <div class="mb-4 p-3 bg-red-100 text-red-600 rounded">
                        <span>{{ $errors->first('email') }}</span>
                    </div>
                @endif

                @if ($errors->has('password'))
                    <div class="mb-4 p-3 bg-red-100 text-red-600 rounded">
                        <span>{{ $errors->first('password') }}</span>
                    </div>
                @endif

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

                <div class="flex justify-between items-center mb-4 text-sm">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="mr-2">
                        <span class="text-gray-600">Remember Me</span>
                    </label>
                </div>

                <div class="text-center">
                    <button type="submit"
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white py-3 rounded-[50px] font-bold">
                        Login
                    </button>
                </div>
            </form>

            <div class="text-center mt-9 mb-9 text-sm">
                <div>
                    <a href="#" class="text-blue-500 hover:underline">Forgot Your Password?</a>
                </div>
                <div>
                    <span>Don't have an account yet? </span>
                    <a href="{{route('register')}}" class="text-blue-500 hover:underline">Register Account!</a>
                </div>
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
