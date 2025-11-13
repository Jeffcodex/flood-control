<?php
session_start();
include '../config/database.php';

if($_POST) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['profile_picture'] = $user['profile_picture'];
            
            // Redirect based on user type
            if($user['user_type'] == 'admin') {
                header("Location: ../admin.php");
            } else {
                header("Location: ../client.php");
            }
            exit();
        } else {
            $_SESSION['error'] = "Invalid username or password!";
            header("Location: ../index.php");
        }
    } catch(PDOException $e) {
        $_SESSION['error'] = "Database error. Please try again.";
        header("Location: ../index.php");
    }
} else {
    header("Location: ../index.php");
}
?>