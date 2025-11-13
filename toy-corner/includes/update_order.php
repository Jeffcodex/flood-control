<?php
session_start();
include '../config/database.php';

// Check if user is admin
if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $order_id = intval($input['order_id']);
    $status = $input['status'];
    
    $allowed_statuses = ['pending', 'approved', 'shipped', 'delivered', 'cancelled'];
    
    if(!in_array($status, $allowed_statuses)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        exit();
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $order_id]);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Order updated successfully']);
        
    } catch(PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>