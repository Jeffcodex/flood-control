<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'client'){
    header("Location: ../index.php");
    exit();
}

include 'config/database.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $user_id = $_SESSION['user_id'];
    
    // Verify order belongs to user and is pending
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND client_id = ? AND status = 'pending'");
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch();
    
    if($order) {
        // Update order status to cancelled
        $update_stmt = $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ?");
        if($update_stmt->execute([$order_id])) {
            // Restore product quantity
            $restore_stmt = $pdo->prepare("
                UPDATE products p 
                JOIN orders o ON p.id = o.product_id 
                SET p.quantity = p.quantity + o.quantity 
                WHERE o.id = ?
            ");
            $restore_stmt->execute([$order_id]);
            
            $_SESSION['success'] = "Order #$order_id has been cancelled successfully!";
        } else {
            $_SESSION['error'] = "Failed to cancel order #$order_id";
        }
    } else {
        $_SESSION['error'] = "Order not found or cannot be cancelled";
    }
    
    header("Location: ../client.php");
    exit();
}
?>