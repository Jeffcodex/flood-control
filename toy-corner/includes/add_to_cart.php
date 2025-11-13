<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'client'){
    $_SESSION['error'] = "Please login as client to add items to cart!";
    header("Location: ../products.php");
    exit();
}

// Include database
include '../config/database.php';

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['product_id']);
    $client_id = $_SESSION['user_id'];
    
    try {
        // Check if product exists and has stock
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND quantity > 0");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        
        if(!$product) {
            $_SESSION['error'] = "Product not available or out of stock!";
            header("Location: ../products.php");
            exit();
        }
        
        // Create order
        $quantity = 1;
        $total_price = $product['price'] * $quantity;
        
        $stmt = $pdo->prepare("INSERT INTO orders (client_id, product_id, quantity, total_price, status) VALUES (?, ?, ?, ?, 'pending')");
        $stmt->execute([$client_id, $product_id, $quantity, $total_price]);
        
        $_SESSION['success'] = "✅ " . $product['name'] . " added to cart successfully!";
        header("Location: ../products.php");
        exit();
        
    } catch(PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header("Location: ../products.php");
        exit();
    }
} else {
    header("Location: ../products.php");
    exit();
}
?>