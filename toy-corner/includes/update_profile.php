<?php
session_start();
include '../config/database.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    
    try {
        // Check if new profile picture is uploaded
        if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            $upload_dir = '../uploads/';
            if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            
            $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
            $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
            
            if(in_array(strtolower($file_extension), $allowed_extensions)) {
                // Delete old profile picture if it exists and not default
                $stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();
                
                if($user['profile_picture'] != 'default.png' && file_exists($upload_dir . $user['profile_picture'])) {
                    unlink($upload_dir . $user['profile_picture']);
                }
                
                // Upload new profile picture
                $profile_picture = uniqid() . '_' . $user_id . '.' . $file_extension;
                move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_dir . $profile_picture);
                
                // Update with new profile picture
                $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, profile_picture = ? WHERE id = ?");
                $stmt->execute([$full_name, $email, $profile_picture, $user_id]);
            } else {
                $_SESSION['error'] = "Invalid file type. Please upload JPG, JPEG, PNG, or GIF files only.";
                header("Location: ../client.php");
                exit();
            }
        } else {
            // Update without changing profile picture
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
            $stmt->execute([$full_name, $email, $user_id]);
        }
        
        // Update session data
        $_SESSION['full_name'] = $full_name;
        $_SESSION['email'] = $email;
        
        $_SESSION['success'] = "Profile updated successfully!";
        header("Location: ../client.php");
        
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error updating profile: " . $e->getMessage();
        header("Location: ../client.php");
    }
} else {
    header("Location: ../client.php");
}
?>