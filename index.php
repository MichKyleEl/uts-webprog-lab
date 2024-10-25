<?php
include('database.php');

if(!isset($_SESSION['username']) && !isset($_SESSION['user_id'])){
    echo '<div class="flex flex-col items-center justify-center mt-20">';
    echo '<p class="text-red-600 text-lg font-semibold">You don\'t have access to this page</p>';
    echo '<a href="login.php" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-300">Login</a>';
    echo '</div>';
    exit();
} else {
    if(isset($_POST['addtask']))
    {
        if(!empty($_POST['description']))
        {
            addTodoItem($_SESSION['username'], $_POST['description']);
            header("Refresh:0");   
        }
    }
}

// Koneksi ke database
$dsn = "mysql:host=localhost;dbname=uts_webprog";
$kunci = new PDO($dsn, "root", "");
$kunci->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Mengambil data mahasiswa dari database
$sql = "SELECT * FROM user_info";
$stmt = $kunci->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TODO</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900">
    <!-- Main container with scrolling -->
    <div class="min-h-screen p-4 md:p-8">
        <!-- Flexbox container for both boxes -->
        <div class="max-w-6xl mx-auto flex flex-col lg:flex-row gap-8">
            
            <!-- Manage Profile Box -->
            <div class="bg-white/10 h-[50%] backdrop-blur-md p-8 rounded-lg border border-white/10 shadow-xl text-white w-full lg:w-1/2">
                <h2 class="text-2xl lg:text-3xl font-bold text-center mb-6">Manage Profile</h2>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="text-red-500 mb-4"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="text-green-500 mb-4"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>

                <form method="POST" id="profileForm" class="space-y-4">
                    <div>
                        <label class="block text-lg font-medium">Username</label>
                        <input type="text" name="new_username" value="<?php echo $_SESSION['username']; ?>" required class="w-full p-2 bg-white/10 border border-gray-300 rounded-lg text-white placeholder-white focus:outline-none focus:ring-2 focus:ring-cyan-300"/>
                    </div>

                    <div>
                        <label class="block text-lg font-medium">Email</label>
                        <input type="email" 
                            name="new_email" 
                            value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>"
                            class="w-full p-2 bg-white/10 border border-gray-300 rounded-lg text-white placeholder-white focus:outline-none focus:ring-2 focus:ring-cyan-300"/>
                    </div>

                    <div>
                        <label class="block text-lg font-medium">Password</label>
                        <input type="password" maxlength="20" name="new_password" placeholder="Enter new password (Max 20 Characters)" class="w-full p-2 bg-white/10 border border-gray-300 rounded-lg text-white placeholder-white focus:outline-none focus:ring-2 focus:ring-cyan-300"/>
                    </div>

                    <div class="flex flex-col lg:flex-row gap-4">
                        <button type="submit" name="updateProfile" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                            Update Profile
                        </button>
                        <button type="submit" name="logout" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                            Logout
                        </button>
                        <button type="button" id="deleteAccountBtn" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                            Delete Account
                        </button>
                    </div>
                </form>
            </div>

            <!-- To Do List Box with scrollable content -->
            <div class="bg-[#1e222a] rounded-lg border border-gray-700 shadow-xl text-white w-full lg:w-1/2 flex flex-col h-[calc(100vh-2rem)]">
                <div class="p-6 space-y-6">
                    <h3 class="text-2xl font-bold">Your Tasks</h3>
                    
                    <!-- Add Task Form -->
                    <div>
                        <form action="index.php" method="POST" class="flex gap-2">
                            <input type="text" 
                                maxlength="15" 
                                placeholder="Task Description (Max 15 characters)" 
                                name="description" 
                                class="flex-1 p-2 bg-[#2b303b] border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                autocomplete="off"
                            />    
                            <button type="submit" 
                                name="addtask" 
                                class="bg-emerald-500 text-white px-6 py-2 rounded-lg hover:bg-emerald-600 whitespace-nowrap"
                            >
                                Add
                            </button>
                        </form>
                    </div>

                    <!-- Filter Section -->
                    <div>
                        <form action="index.php" method="POST" class="flex items-center gap-2">
                            <label for="filter" class="text-gray-300 whitespace-nowrap">Filter:</label>
                            <select name="filter" 
                                id="filter" 
                                class="flex-1 p-2 bg-[#2b303b] border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="all" class="bg-[#2b303b]" <?php if (!isset($_POST['filter']) || $_POST['filter'] == 'all') echo 'selected';?>>All</option>
                                <option value="done" class="bg-[#2b303b]" <?php if (isset($_POST['filter']) && $_POST['filter'] == 'done') echo 'selected';?>>Done</option>
                                <option value="undone" class="bg-[#2b303b]" <?php if (isset($_POST['filter']) && $_POST['filter'] == 'undone') echo 'selected';?>>Undone</option>
                            </select>
                            <button type="submit" 
                                name="applyFilter" 
                                class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 whitespace-nowrap"
                            >
                                Apply Filter
                            </button>
                        </form>
                    </div>

                    <!-- Search Section -->
                    <div>
                        <form action="index.php" method="POST" class="flex items-center gap-2">
                            <label for="search" class="text-gray-300 whitespace-nowrap">Search:</label>
                            <input type="text" 
                                name="search" 
                                id="search" 
                                placeholder="Search tasks..." 
                                value="<?php echo isset($_POST['search']) ? $_POST['search'] : ''; ?>" 
                                class="flex-1 p-2 bg-[#2b303b] border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <button type="submit" 
                                name="applySearch" 
                                class="bg-yellow-500 text-white px-2 py-2 rounded-lg hover:bg-yellow-600 whitespace-nowrap"
                            >
                            <i class='fas fa-search'></i>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Scrollable Todo Items Container -->
                <div class="flex-1 overflow-y-auto p-6">
                    <div class="bg-[#2b303b] p-4 rounded-lg space-y-3">
                        <?php 
                        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                            processTodoSave();
                        }
                        getTodoItems($_SESSION['username']); 
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Account Modal -->
        <div id="deleteConfirmModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center">
            <div class="bg-gray-900 p-6 rounded-lg border border-white/10 shadow-xl max-w-md w-full mx-4">
                <h3 class="text-xl font-bold text-white mb-4">Delete Account</h3>
                <p class="text-gray-300 mb-6">Are you sure you want to delete your account? This action is irreversible.</p>
                <div class="flex justify-end space-x-4">
                    <button type="button" id="cancelDeleteBtn" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                        Cancel
                    </button>
                    <button type="submit" name="deleteAccount" form="profileForm" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                        Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Get modal elements
        const deleteModal = document.getElementById('deleteConfirmModal');
        const deleteBtn = document.getElementById('deleteAccountBtn');
        const cancelBtn = document.getElementById('cancelDeleteBtn');

        // Show modal when delete button is clicked
        deleteBtn.addEventListener('click', () => {
            deleteModal.classList.remove('hidden');
        });

        // Hide modal when cancel is clicked
        cancelBtn.addEventListener('click', () => {
            deleteModal.classList.add('hidden');
        });

        // Hide modal when clicking outside
        deleteModal.addEventListener('click', (e) => {
            if (e.target === deleteModal) {
                deleteModal.classList.add('hidden');
            }
        });

        // Handle escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !deleteModal.classList.contains('hidden')) {
                deleteModal.classList.add('hidden');
            }
        });
    </script>
</body>
</html>