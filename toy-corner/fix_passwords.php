<?php
// fix_passwords.php - SAVE IN ROOT FOLDER
session_start();
echo "<h2>🔧 Fixing Passwords...</h2>";

include 'config/database.php';

$passwords = [
    'admin' => 'admin123',
    'client' => 'client123'
];

foreach($passwords as $username => $plain_password) {
    $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
    
    if($stmt->execute([$hashed_password, $username])) {
        echo "<p>✅ Updated password for: <strong>$username</strong> → $plain_password</p>";
    } else {
        echo "<p>❌ Failed to update: $username</p>";
    }
}

echo "<h3>🎉 PASSWORDS FIXED!</h3>";
echo "<div style='background:#000; color:#fff; padding:20px; border-radius:10px; margin:20px 0;'>";
echo "<h4>USE THESE CREDENTIALS:</h4>";
echo "<p>👑 <strong>ADMIN:</strong> username 'admin' | password 'admin123'</p>";
echo "<p>👤 <strong>CLIENT:</strong> username 'client' | password 'client123'</p>";
echo "</div>";

echo "<a href='index.php' style='display:inline-block; padding:10px 20px; background:#000; color:#fff; text-decoration:none; border-radius:5px;'>🚀 Go to Login</a>";
?>