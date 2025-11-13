<?php
session_start();

// Include database with error handling
if(file_exists('../config/database.php')) {
    include '../config/database.php';
} else {
    $_SESSION['error'] = "Database configuration not found!";
    header("Location: ../index.php");
    exit();
}

// Check if form was submitted
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize inputs
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    // Validate inputs
    if(empty($username) || empty($password)) {
        $_SESSION['error'] = "Please enter both username and password!";
        header("Location: ../index.php");
        exit();
    }
    
    try {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if($user) {
            // Verify password
            if(password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['profile_picture'] = $user['profile_picture'];
                
                // DEBUG: Log successful login
                error_log("LOGIN SUCCESS: User " . $user['username'] . " logged in as " . $user['user_type']);
                
                // Redirect based on user type
                if($user['user_type'] == 'admin') {
                    header("Location: ../admin.php");
                } else {
                    header("Location: ../client.php");
                }
                exit();
                
            } else {
                $_SESSION['error'] = "Invalid password!";
                header("Location: ../index.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "User not found!";
            header("Location: ../index.php");
            exit();
        }
        
    } catch(PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header("Location: ../index.php");
        exit();
    }
} else {
    // If not POST request, redirect to index
    $_SESSION['error'] = "Invalid request method!";
    header("Location: ../index.php");
    exit();
}
?>