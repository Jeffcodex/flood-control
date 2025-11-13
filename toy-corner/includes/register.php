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

if($_POST){
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    
    // Simple validation
    $errors = [];
    
    if(empty($full_name)) $errors[] = "Full name is required!";
    if(empty($email)) $errors[] = "Email is required!";
    if(empty($username)) $errors[] = "Username is required!";
    if(empty($password)) $errors[] = "Password is required!";
    if(strlen($password) < 6) $errors[] = "Password must be at least 6 characters!";
    
    if(empty($errors)) {
        // Check if username or email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if($stmt->rowCount() > 0) {
            $_SESSION['error'] = "Username or email already exists!";
            header("Location: ../index.php");
            exit();
        }
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $profile_picture = 'default.png';
        
        // Handle profile picture upload
        if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            $upload_dir = '../uploads/';
            if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            
            $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
            $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
            
            if(in_array(strtolower($file_extension), $allowed_extensions)) {
                $profile_picture = uniqid() . '_' . $username . '.' . $file_extension;
                move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_dir . $profile_picture);
            }
        }
        
        try {
            // Insert user as client - ADDED user_type
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, username, password, profile_picture, address, phone, user_type) VALUES (?, ?, ?, ?, ?, ?, ?, 'client')");
            $stmt->execute([$full_name, $email, $username, $hashed_password, $profile_picture, $address, $phone]);
            
            $_SESSION['success'] = "🎉 Registration successful! Please login.";
            header("Location: ../index.php");
            exit();
            
        } catch(PDOException $e) {
            $_SESSION['error'] = "❌ Registration failed: " . $e->getMessage();
            header("Location: ../index.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "❌ " . implode("<br>❌ ", $errors);
        header("Location: ../index.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Invalid request method!";
    header("Location: ../index.php");
    exit();
}
?>