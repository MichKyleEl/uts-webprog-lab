<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.tailwindcss.com"></script> <!-- Include Tailwind CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
</body>
</html>

<?php
    session_start();
    
    function connectdatabase() {
        return mysqli_connect("localhost", "root", "", "uts_webprog");
    }    

    function loggedin() {
        return isset($_SESSION['username']);
    }

    if (isset($_POST['logout'])) {
        logout();
    }
    
    function logout() {
        session_unset();
        session_destroy();
        header('location:login.php');
        exit();
    }

    function spaces($n) {
        for($i=0; $i<$n; $i++)
            echo "&nbsp;";
    }

    function userexist($username) 
    {
        $conn = connectdatabase();
        $sql = "SELECT * FROM uts_webprog.user_info WHERE username = '".$username."'"; 
        $result = mysqli_query($conn,$sql);
        mysqli_close($conn);

        if(!$result || mysqli_num_rows($result) == 0) { 
           return false;
        }
        return true;
    }

    function validuser($username, $password) {
    $conn = connectdatabase();
    $sql = "SELECT * FROM user_info WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $hashed_password = $row['password'];

        if (password_verify($password, $hashed_password)) {
            $_SESSION["username"] = $username;
            $_SESSION["email"] = $row['email'];  // Store email in session
            $_SESSION["user_id"] = $row['id'];   // Also store user_id for good measure
            return true;
        }
    }

    mysqli_close($conn);
    return false;
}
    
    function error() 
    {
        if(isset($_SESSION['error'])) {
            echo $_SESSION['error'];
            unset($_SESSION['error']);
        }
    }

    function updatepassword($username, $password) {
        $conn = connectdatabase();
        $sql = "UPDATE uts_webprog.user_info SET password = '".$password."' WHERE username = '".$username."';";
        $result = mysqli_query($conn, $sql);

        $_SESSION['error'] = "<br> &nbsp; Password Updated !! ";
        header('location:index.php');
    }

    if (isset($_POST['deleteAccount'])) {
        $username = $_SESSION['username'];
        deleteAccount($username);
    }
    
    function deleteAccount($username) {
        $conn = connectdatabase();
        $sql = "DELETE FROM tasks WHERE id IN (SELECT id FROM user_info WHERE username = '".$username."')";
        mysqli_query($conn, $sql);
    
        $sql = "DELETE FROM user_info WHERE username = '".$username."'";
        $result = mysqli_query($conn, $sql);
    
        if ($result) {
            $_SESSION['error'] = "Account deleted successfully.";
            session_unset();
            session_destroy();
            header('location:login.php');
            exit();
        } else {
            $_SESSION['error'] = "Failed to delete account.";
        }
    
        mysqli_close($conn);
    }
    

    function createUser($username, $password, $email) {
        if (!userexist($username)) {
            $conn = connectdatabase();
    
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
            $sql = "INSERT INTO user_info (username, password, email) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $username, $hashed_password, $email);
            $stmt->execute();
    
            $_SESSION["username"] = $username;
            $_SESSION["email"] = $email;
            header('location:index.php');
        } else {
            $_SESSION['error'] = "&nbsp; Username already exists!";
            header('location:register.php');
        }
    }

    

    function isValid($username, $password, $usercaptcha)
    {
        $capcode = $_SESSION['captcha'];

        if(!strcmp($usercaptcha,$capcode))
        {
            if(validuser($username, $password))
            {
                $_SESSION["username"] = $username;
                header('location:index.php');
            }
            else
            {
                $_SESSION['error'] = "&nbsp; Invalid Username or Password !! ";
                header('location:login.php');
            }
            mysqli_close($conn);
        }
        else
        {
            $_SESSION['error'] = "&nbsp; Invalid captcha code !! ";
            header('location:login.php');
        }
    }
    
    

    function getTodoItems($username) {
        $conn = connectdatabase();
    
        // Get user ID
        $sql = "SELECT id FROM user_info WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $user_id = $row['id'];
    
            // Set up filter and search parameters
            $filter = isset($_POST['filter']) ? $_POST['filter'] : 'all';
            $search = isset($_POST['search']) ? $_POST['search'] : '';
    
            // Query to get tasks
            $sql = "SELECT * FROM tasks WHERE id = ?";
            if ($filter == 'done') {
                $sql .= " AND done = 1";
            } elseif ($filter == 'undone') {
                $sql .= " AND done = 0";
            }
            if (!empty($search)) {
                $sql .= " AND task LIKE ?";
            }
    
            // Prepare and execute query
            $stmt = $conn->prepare($sql);
            if (!empty($search)) {
                $search_param = "%" . $search . "%";
                $stmt->bind_param("is", $user_id, $search_param);
            } else {
                $stmt->bind_param("i", $user_id);
            }
            $stmt->execute();
            $result = $stmt->get_result();
    
            // Output delete confirmation modal
            echo '
            <!-- Delete Confirmation Modal -->
            <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4 shadow-xl">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        Delete Confirmation
                    </h3>
                    <p class="text-gray-600 mb-6">
                        Are you sure you want to delete this task? This action cannot be undone.
                    </p>
                    <div class="flex justify-end space-x-3">
                        <button onclick="closeDeleteModal()" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Cancel
                        </button>
                        <form method="POST" class="inline">
                            <input type="hidden" name="task_id" id="modalTaskId">
                            <button type="submit" name="deleteTask" 
                                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>';
    
            // Start main form
            echo "<form id='taskForm' action='' method='POST'>";
            echo "<div class='todo-container'>";
            
            // Fetch tasks
            $tasks = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $tasks[] = $row;
            }
    
            // Display tasks
            foreach ($tasks as $task) {
                $taskId = $task["taskid"];
                
                // Main todo item container with improved mobile spacing
                echo "<div class='todo-item flex justify-between items-center mb-2 p-2.5 bg-gray-800 rounded-lg shadow-sm'>";
                
                // Left side with checkbox and task text
                echo "<div class='flex items-center gap-2 flex-1 min-w-0'>";
                echo "<input type='checkbox' class='w-5 h-5 accent-green-500 cursor-pointer' 
                      name='check_list[]' value='" . $taskId . "'" . 
                      ($task['done'] ? ' checked' : '') . " onchange='handleCheckboxChange(this)'>";
                
                // Task text with ellipsis for overflow
                echo "<span id='task_" . $taskId . "' class='text-sm truncate max-w-[150px] sm:max-w-none'" . 
                     ($task['done'] ? ' style="text-decoration: line-through;"' : '') . ">" . 
                     htmlspecialchars($task["task"]) . "</span>";
                
                // Edit form with responsive sizing
                echo "<div id='edit_form_" . $taskId . "' style='display: none;' 
                      class='flex items-center gap-1 flex-1 min-w-0'>";
                echo "<input type='text' maxlength='15' placeholder='Max 15 chars' 
                      name='updated_task' value='" . htmlspecialchars($task["task"]) . "' 
                      class='flex-1 min-w-0 bg-gray-700 text-white rounded px-2 py-1 text-sm'>";
                echo "<button type='button' onclick='saveEdit(" . $taskId . ")' 
                      class='bg-green-500 text-white p-1.5 rounded-lg hover:bg-green-600 text-sm'>
                      <i class='fas fa-save'></i>
                      </button>";
                echo "<button type='button' onclick='cancelEdit(" . $taskId . ")' 
                      class='bg-gray-500 text-white p-1.5 rounded-lg hover:bg-gray-600 text-sm'>
                      <i class='fas fa-times'></i>
                      </button>";
                echo "</div>";
                echo "</div>";
            
                // Action buttons with consistent sizing
                echo "<div class='flex gap-1 ml-2'>";
                echo "<button type='button' onclick='toggleEdit(" . $taskId . ")' 
                      class='bg-blue-500 text-white p-1.5 rounded-lg hover:bg-blue-600 text-sm'>
                      <i class='fas fa-edit'></i>
                      </button>";
                echo "<button type='button' onclick='openDeleteModal(" . $taskId . ")' 
                      class='bg-red-500 text-white p-1.5 rounded-lg hover:bg-red-600 text-sm'>
                      <i class='fas fa-trash'></i>
                      </button>";
                echo "</div>";
            
                echo "</div>";
            }

            
            if (empty($tasks)) {
                echo "<p>No tasks found.</p>";
            }
            
            echo "</div>";
            
            echo "<button type='button' onclick='saveChanges()' class='bg-green-500 text-white px-2 py-1 rounded-lg hover:bg-green-600'>
                    <i class='fas fa-save'></i> Save
                  </button>";
            echo "</form>";
    
            // JavaScript
            echo "<script>
    let changedTasks = new Set();

    function handleCheckboxChange(checkbox) {
    changedTasks.add(checkbox.value);
}

function toggleEdit(taskId) {
    const taskSpan = document.getElementById('task_' + taskId);
    const editForm = document.getElementById('edit_form_' + taskId);
    
    if (taskSpan && editForm) {
        taskSpan.style.display = 'none';
        editForm.style.display = 'inline-flex';
    }
}

function cancelEdit(taskId) {
    const taskSpan = document.getElementById('task_' + taskId);
    const editForm = document.getElementById('edit_form_' + taskId);
    
    if (taskSpan && editForm) {
        taskSpan.style.display = 'inline';
        editForm.style.display = 'none';
    }
}

function saveEdit(taskId) {
    const editForm = document.getElementById('edit_form_' + taskId);
    const editInput = editForm.querySelector('input[name=\"updated_task\"]');
    const taskSpan = document.getElementById('task_' + taskId);
    
    if (!editInput || !taskSpan) return;

    const formData = new FormData();
    formData.append('updateTask', '1');
    formData.append('task_id', taskId);
    formData.append('updated_task', editInput.value);

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(() => {
        taskSpan.textContent = editInput.value;
        cancelEdit(taskId);
        location.reload(); // Add page reload to ensure we get fresh data
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating task');
    });
}

function saveChanges() {
    const form = document.getElementById('taskForm');
    const formData = new FormData(form);
    formData.append('Save', '1');

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.ok) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving changes');
    });
}

function openDeleteModal(taskId) {
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('modalTaskId').value = taskId;
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

    document.addEventListener('click', function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target === modal) {
            closeDeleteModal();
        }
    });
    </script>";

    
        } else {
            echo "<p>User not found.</p>";
        }
    
        mysqli_close($conn);
    }
    
    // Save handler function
    function processTodoSave() {
        if(isset($_POST['Save'])) {
            $conn = connectdatabase();
            
            // Get user ID
            $username = $_SESSION['username'];
            $sql = "SELECT id FROM user_info WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = mysqli_fetch_assoc($result);
            $user_id = $row['id'];
            
            // First, set all tasks to undone
            $sql = "UPDATE tasks SET done = 0 WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            
            // Then update checked tasks to done
            if(!empty($_POST['check_list'])) {
                $sql = "UPDATE tasks SET done = 1 WHERE taskid = ? AND id = ?";
                $stmt = $conn->prepare($sql);
                
                foreach($_POST['check_list'] as $taskId) {
                    $stmt->bind_param("ii", $taskId, $user_id);
                    $stmt->execute();
                }
            }
            
            mysqli_close($conn);
            
            // Use JavaScript for redirect instead of header()
            echo "<script>window.location.href = '" . $_SERVER['PHP_SELF'] . "';</script>";
            exit();
        }
    }
    
    // Move all action handling to the top
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['logout'])) {
            logout();
        }
        
        if (isset($_POST['deleteAccount'])) {
            deleteAccount($_SESSION['username']);
        }
        
        if (isset($_POST['updateProfile'])) {
            $new_username = $_POST['new_username'];
            $new_email = $_POST['new_email'];
            $new_password = $_POST['new_password'];
            $user_id = $_SESSION['user_id'];
            updateUserProfile($user_id, $new_username, $new_email, $new_password);
        }
        
        // Add other POST handlers here
    }
    
    // Updated save handling code
    function handleSave() {
        if(isset($_POST['Save'])) {
            $conn = connectdatabase();
            
            // Get all tasks
            $all_tasks = isset($_POST['all_tasks']) ? $_POST['all_tasks'] : array();
            // Get checked tasks
            $checked_tasks = isset($_POST['check_list']) ? $_POST['check_list'] : array();
            
            // First, mark all tasks as undone
            foreach($all_tasks as $taskId) {
                updateDone($taskId, 0); // Mark as undone
            }
            
            // Then, mark checked tasks as done
            foreach($checked_tasks as $taskId) {
                updateDone($taskId, 1); // Mark as done
            }
            
            mysqli_close($conn);
        }
    }
    
    // Helper function to update task status
    function updateDone($taskId, $status) {
        $conn = connectdatabase();
        $sql = "UPDATE tasks SET done = ? WHERE taskid = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $status, $taskId);
        $stmt->execute();
        mysqli_close($conn);
    }
    
    
    
    
    
    
    

    if (isset($_POST['deleteTask'])) {
        $task_id = $_POST['task_id'];
        deleteTodoItemById($task_id);
    }
    
    function deleteTodoItemById($task_id) {
        $conn = connectdatabase();
        $sql = "DELETE FROM tasks WHERE taskid = '".$task_id."'";
        mysqli_query($conn, $sql);
        mysqli_close($conn);
    }
    
    if (isset($_POST['deleteTask'])) {
        $task_id = $_POST['task_id'];
        deleteTodoItemById($task_id);
    }
    
    if (isset($_POST['updateTask'])) {
        $task_id = $_POST['task_id'];
        $updated_task = $_POST['updated_task'];
        updateTask($task_id, $updated_task);
    }
    
    function updateTask($task_id, $updated_task) {
        $conn = connectdatabase();
        $sql = "UPDATE tasks SET task = '".$updated_task."' WHERE taskid = '".$task_id."'";
        mysqli_query($conn, $sql);
        mysqli_close($conn);
    }
    
    if (isset($_POST['updateProfile'])) {
        $new_username = $_POST['new_username'];
        $new_email = $_POST['new_email'];
        $new_password = $_POST['new_password']; 
    
        $user_id = $_SESSION['user_id'];

        updateUserProfile($user_id, $new_username, $new_email, $new_password);
    }

    function isUsernameExists($new_username, $user_id) {
        $conn = connectdatabase();
        $sql = "SELECT id FROM user_info WHERE username = ? AND id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $new_username, $user_id);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0; 
        
        mysqli_close($conn);
        return $exists;
    }
    

    function updateUserProfile($user_id, $new_username, $new_email, $new_password) {
        $conn = connectdatabase();
    
        if (isUsernameExists($new_username, $user_id)) {
            $_SESSION['error'] = "Username sudah dipakai, silakan pilih username lain.";
            header('location:index.php');
            exit(); 
        }
    
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
    
            $sql = "UPDATE user_info SET username = ?, email = ?, password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $new_username, $new_email, $hashed_password, $user_id);
            $stmt->execute();
        } else {
            $sql = "UPDATE user_info SET username = ?, email = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $new_username, $new_email, $user_id);
            $stmt->execute();
        }
    
        mysqli_close($conn);
    
        $_SESSION["username"] = $new_username;
        $_SESSION["email"] = $new_email;
        $_SESSION['success'] = "Profil berhasil diperbarui!";
        header('location:index.php');
        exit();
    }
    
    if (isset($_POST['logout'])) {
        logout();
    }
    

    function addTodoItem($username, $todo_text) {
    $conn = connectdatabase();
    $sql = "SELECT id FROM user_info WHERE username = '".$username."'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $user_id = $row['id'];
        $sql = "INSERT INTO tasks (id, task, done) VALUES ('".$user_id."', '".$todo_text."', 0)";
        mysqli_query($conn, $sql);
    }

    mysqli_close($conn);
}

    
function deleteTodoItem($username, $task_id) {
    $conn = connectdatabase();
    $sql = "SELECT id FROM user_info WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $user_id = $row['id'];
        $sql = "DELETE FROM tasks WHERE taskid = ? AND id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $task_id, $user_id);
        $stmt->execute();
    }
    mysqli_close($conn);
}


?>