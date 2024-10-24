<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center relative">

    <!-- Background Shapes -->
    <div class="absolute w-full h-full flex items-center justify-center">
        <div class="relative w-96 h-[520px]">
            <div class="absolute w-96 h-96 bg-gradient-to-br from-cyan-300 via-cyan-600 to-teal-600 rounded-full left-[-200px] top-[10px]"></div>
            <div class="absolute w-48 h-48 bg-gradient-to-r from-red-500 to-yellow-500 rounded-full right-[-80px] bottom-[-80px]"></div>
        </div>
    </div>

    <!-- Login Form -->
    <form action="login_process.php" method="post" class="relative z-10 bg-white/10 backdrop-blur-md p-8 rounded-lg border border-white/10 shadow-xl text-white w-96">
        <h3 class="text-center text-2xl font-extrabold mb-4">Login
            <span class="block text-lg font-light text-gray-300">Welcome Back!</span>    
        </h3>
        <label for="username" class="block text-lg font-medium mt-4">Username</label>
        <input type="text" placeholder="Type here" id="username" name="username" class="mt-2 p-3 w-full bg-white/10 rounded-lg text-sm text-white-300 placeholder-white focus:outline-none focus:ring-2 focus:ring-cyan-300" spellcheck="false">

        <label for="password" class="block text-lg font-medium mt-6">Password</label>
        <input type="password" placeholder="Minimum 20 Characters" id="password" name="password" maxlength="20" required class="mt-2 p-3 w-full bg-white/10 rounded-lg text-sm text-white-300 placeholder-white focus:outline-none focus:ring-2 focus:ring-cyan-300" spellcheck="false">

        <div class="mt-6">
            <div class="g-recaptcha" data-sitekey="6LfJa2gqAAAAAKTmtctz1RwhmcQqoFmZBEzb4OLD" data-theme="dark"></div>
        </div>
        <div class="alert mt-4 text-red-500 text-sm"></div>
        <button type="submit" class="mt-6 w-full py-3 bg-white text-gray-900 font-semibold rounded-lg hover:bg-white/30 hover:text-gray-300 transition-all duration-300">Sign In</button>

        <div class="mt-4 text-center text-gray-300">
            <p>Don't have an account yet?</p>
            <a href="register.php" class="text-cyan-300 hover:underline">Sign Up</a>
        </div>
    </form>

    <script>
        document.querySelector("form").addEventListener("submit", function (event) {
            var response = grecaptcha.getResponse();
            if (response.length == 0) {
                event.preventDefault();  // Prevent form submission
                var alertDiv = document.querySelector(".alert");
                alertDiv.innerHTML = "Please verify that you are not a robot.";
            }
        });
    </script>

</body>
</html>
