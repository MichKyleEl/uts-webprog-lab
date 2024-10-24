<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center">

    <div class="absolute w-96 h-[520px]">
        <div class="absolute w-96 h-96 bg-gradient-to-br from-cyan-300 via-cyan-600 to-teal-600 rounded-full left-[-200px] top-[10px]"></div>
        <div class="absolute w-48 h-48 bg-gradient-to-r from-red-500 to-yellow-500 rounded-full right-[-80px] bottom-[-80px]"></div>
    </div>

    <form action="register_process.php" method="POST" class="relative bg-white/10 backdrop-blur-md p-8 rounded-lg border border-white/10 shadow-xl text-white w-96 z-10">
        <h3 class="text-center text-2xl font-extrabold mb-4">Register
            <span class="block text-lg font-light text-white-300">Create your account</span>    
        </h3>

        <label for="email" class="block text-lg font-medium mt-4">E-mail</label>
        <input type="email" placeholder="Type here" id="email" name="email" class="mt-2 p-3 w-full bg-white/10 rounded-lg text-sm text-white-300 placeholder-white focus:outline-none focus:ring-2 focus:ring-cyan-300" spellcheck="false" required>

        <label for="username" class="block text-lg font-medium mt-6">Username</label>
        <input type="text" placeholder="Type here" id="username" name="username" class="mt-2 p-3 w-full bg-white/10 rounded-lg text-sm text-white-300 placeholder-white focus:outline-none focus:ring-2 focus:ring-cyan-300" spellcheck="false" required>

        <!-- Menampilkan pesan kesalahan jika ada -->
        <?php 
        session_start(); // Mulai session di sini juga
        if (isset($_SESSION['errorMessage'])): ?>
            <div class="text-red-500 mt-2"><?= htmlspecialchars($_SESSION['errorMessage']) ?></div>
            <?php unset($_SESSION['errorMessage']); // Hapus pesan setelah ditampilkan ?>
        <?php endif; ?>

        <label for="password" class="block text-lg font-medium mt-6">Password</label>
        <input type="password" placeholder="Minimum 20 Characters" id="password" name="password" maxlength="20" class="mt-2 p-3 w-full bg-white/10 rounded-lg text-sm text-gray-300 placeholder-white focus:outline-none focus:ring-2 focus:ring-cyan-300" spellcheck="false" required>

        <button type="submit" class="mt-6 w-full py-3 bg-white text-gray-900 font-semibold rounded-lg hover:bg-white/30 hover:text-gray-300 transition-all duration-300">Register</button>

        <div class="mt-4 text-center text-gray-300">
            <p>Already have an account?</p>
            <a href="login.php" class="text-cyan-300 hover:underline">Sign In</a>
        </div>

    </form>
</body>
</html>
