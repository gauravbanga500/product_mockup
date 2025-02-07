<?php
session_start();
include 'db_connection.php';
include 'admin_sidebar.php';

// Start output buffering to prevent accidental output before headers
ob_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $user_role = $_POST['user_role'];

    // Prepare and execute the SQL statement 
    $sql = "INSERT INTO users (name, email, password, user_role) VALUES (?, ?, ?, ?)"; 
    $stmt = $conn->prepare($sql); 
    $stmt->bind_param("ssss", $name, $email, $password, $user_role);
    $stmt->execute(); 

    // Check if the query was successful
    if ($stmt->affected_rows > 0) { 
        // Store success message in session
        $_SESSION['message'] = "User created successfully";

        // Redirect to user.php
        header('Location: user.php');
        echo '<script>window.location.href="user.php";</script>'; // JavaScript fallback
        exit();
    } else {
        // Error handling
        echo "Error: Unable to create user.";
    }
}

// End output buffering
ob_end_flush();
?>



<!DOCTYPE html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
    <!-- Include Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-4xl bg-white rounded-lg shadow-lg p-10">
        <h1 class="text-4xl font-bold text-gray-800 mb-8 text-center">Create User</h1>
        <form action="" method="POST" class="space-y-8">
            <!-- Row 1: Name and Email -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Name Input -->
                <div>
                    <label for="name" class="block text-lg font-semibold text-gray-700 mb-2">Name</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        required 
                        class="block w-full rounded-lg border-gray-300 shadow-md focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-3"
                        placeholder="Enter the name"
                    >
                </div>

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-lg font-semibold text-gray-700 mb-2">Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        required 
                        class="block w-full rounded-lg border-gray-300 shadow-md focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-3"
                        placeholder="Enter the email address"
                    >
                </div>
            </div>

            <!-- Row 2: Password and Role -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-lg font-semibold text-gray-700 mb-2">Password</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required 
                        class="block w-full rounded-lg border-gray-300 shadow-md focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-3"
                        placeholder="Enter the password"
                    >
                </div>

                <!-- Role Selection -->
                <div>
                    <label for="user_role" class="block text-lg font-semibold text-gray-700 mb-2">Role</label>
                    <select 
                        name="user_role" 
                        id="user_role" 
                        required 
                        class="block w-full rounded-lg border-gray-300 shadow-md focus:border-indigo-500 focus:ring-indigo-500 sm:text-base p-3 bg-white"
                    >
                        <option value="sub_admin">Sub Admin</option>
                        <option value="employee">Employee</option>
                    </select>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-center">
                <button 
                    type="submit" 
                    class="w-full md:w-1/2 lg:w-[53%] bg-indigo-600 text-white py-4 px-6 rounded-lg shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:ring-offset-2 text-lg font-medium"
                >
                    Create User
                </button>
            </div>
        </form>

        <!-- Back Link -->
        <div class="mt-8 text-center">
            <a 
                href="user.php" 
                class="text-indigo-600 hover:underline text-lg"
            >
                Go Back
            </a>
        </div>
    </div>
</body>
</html>


    </div>
</body>
</html>
