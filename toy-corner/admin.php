<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToyRex Corner - Premium Products</title>
  
    <link rel="icon" type="image/x-icon" href="assets/images/logo1.png">
    <link rel="stylesheet" href="assets/css/style.css">

</head>

<?php
// ADMIN.PHP - COMPLETE VERSION WITH CONTACT MESSAGES
ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// STRICT SECURITY CHECK
if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin'){
    $_SESSION['error'] = "Access denied! Admin privileges required.";
    ob_end_clean();
    header("Location: index.php");
    exit();
}

// INCLUDE DATABASE
if(file_exists('config/database.php')) {
    include 'config/database.php';
} else {
    die('Database configuration not found!');
}

// Handle Contact Message Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mark_as_read']) && isset($_POST['message_id'])) {
        $message_id = intval($_POST['message_id']);
        try {
            $stmt = $pdo->prepare("UPDATE contact_messages SET status = 'read' WHERE id = ?");
            $stmt->execute([$message_id]);
            $_SESSION['success'] = "✅ Message marked as read!";
        } catch(PDOException $e) {
            $_SESSION['error'] = "❌ Error updating message: " . $e->getMessage();
        }
    }
    
    if (isset($_POST['mark_as_unread']) && isset($_POST['message_id'])) {
        $message_id = intval($_POST['message_id']);
        try {
            $stmt = $pdo->prepare("UPDATE contact_messages SET status = 'unread' WHERE id = ?");
            $stmt->execute([$message_id]);
            $_SESSION['success'] = "✅ Message marked as unread!";
        } catch(PDOException $e) {
            $_SESSION['error'] = "❌ Error updating message: " . $e->getMessage();
        }
    }
    
    if (isset($_POST['delete_message']) && isset($_POST['message_id'])) {
        $message_id = intval($_POST['message_id']);
        try {
            $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
            $stmt->execute([$message_id]);
            $_SESSION['success'] = "✅ Message deleted successfully!";
        } catch(PDOException $e) {
            $_SESSION['error'] = "❌ Error deleting message: " . $e->getMessage();
        }
    }
    
    // Handle order status update
    if (isset($_POST['update_order'])) {
        $order_id = intval($_POST['order_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        
        // Validate inputs
        if($order_id > 0 && in_array($status, ['pending', 'approved', 'shipped', 'delivered', 'cancelled'])) {
            try {
                $update_stmt = $pdo->prepare("UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?");
                if ($update_stmt->execute([$status, $order_id])) {
                    $_SESSION['success'] = "✅ Order #$order_id updated to " . ucfirst($status);
                } else {
                    $_SESSION['error'] = "❌ Failed to update order #$order_id";
                }
            } catch(PDOException $e) {
                $_SESSION['error'] = "❌ Database error: " . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = "❌ Invalid order data!";
        }
    }
    
    ob_end_clean();
    header("Location: admin.php");
    exit();
}

// Get admin profile
$user_id = $_SESSION['user_id'];
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $admin = $stmt->fetch();
    
    if(!$admin) {
        $_SESSION['error'] = "Admin profile not found!";
        ob_end_clean();
        header("Location: index.php");
        exit();
    }
} catch(PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Get statistics with error handling
try {
    $product_count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $client_count = $pdo->query("SELECT COUNT(*) FROM users WHERE user_type = 'client'")->fetchColumn();
    $pending_count = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
    $total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $total_revenue = $pdo->query("SELECT SUM(total_price) FROM orders WHERE status IN ('delivered', 'shipped')")->fetchColumn() ?: 0;
    
    // Additional stats
    $approved_count = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'approved'")->fetchColumn();
    $shipped_count = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'shipped'")->fetchColumn();
    $delivered_count = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'delivered'")->fetchColumn();
    
    // Contact messages stats
    $unread_messages = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'unread'")->fetchColumn();
    $total_messages = $pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
    
} catch(PDOException $e) {
    // Default values if query fails
    $product_count = $client_count = $pending_count = $total_orders = $total_revenue = 0;
    $approved_count = $shipped_count = $delivered_count = 0;
    $unread_messages = $total_messages = 0;
    error_log("Statistics query failed: " . $e->getMessage());
}

// Get all orders with user and product info
try {
    $order_stmt = $pdo->prepare("
        SELECT o.*, u.username, u.full_name, u.email, u.profile_picture, p.name as product_name, p.price, p.image, p.category
        FROM orders o 
        JOIN users u ON o.client_id = u.id 
        JOIN products p ON o.product_id = p.id 
        ORDER BY o.order_date DESC
    ");
    $order_stmt->execute();
    $all_orders = $order_stmt->fetchAll();
} catch(PDOException $e) {
    $all_orders = [];
    error_log("Orders query failed: " . $e->getMessage());
}

// Get contact messages
try {
    $contact_stmt = $pdo->prepare("SELECT * FROM contact_messages ORDER BY created_at DESC");
    $contact_stmt->execute();
    $contact_messages = $contact_stmt->fetchAll();
} catch(PDOException $e) {
    $contact_messages = [];
    error_log("Contact messages query failed: " . $e->getMessage());
}

// NOW INCLUDE HEADER
include 'includes/header.php';
?>

<!-- IMPROVED ADMIN STYLES -->
<style>
.admin-dashboard {
    margin-top: 100px;
    padding: 20px;
    max-width: 1400px;
    margin-left: auto;
    margin-right: auto;
}

.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    flex-wrap: wrap;
    gap: 20px;
}

.admin-profile {
    display: flex;
    align-items: center;
    gap: 20px;
}

.admin-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #000;
    background: #f0f0f0;
}

.admin-info h1 {
    margin-bottom: 5px;
    color: #000;
    font-size: 2.2em;
}

.admin-info p {
    color: #666;
    margin: 3px 0;
}

.admin-badge {
    display: inline-block;
    background: #000;
    color: #fff;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.7em;
    font-weight: bold;
    margin-left: 8px;
}

.admin-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.admin-btn {
    padding: 10px 20px;
    background: #000;
    color: #fff;
    border: 2px solid #000;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.3s ease;
    cursor: pointer;
}

.admin-btn:hover {
    background: #fff;
    color: #000;
    transform: translateY(-2px);
}

.admin-btn.secondary {
    background: #6c757d;
    border-color: #6c757d;
}

.admin-btn.secondary:hover {
    background: #fff;
    color: #6c757d;
}

.stat-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.stat-card {
    background: #fff;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    text-align: center;
    border: 2px solid #000;
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-card h3 {
    font-size: 2.5em;
    color: #000;
    margin-bottom: 10px;
}

.stat-card p {
    color: #666;
    font-size: 1.1em;
    font-weight: 500;
}

.revenue-card {
    background: linear-gradient(135deg, #000 0%, #333 100%);
    color: #fff;
    border: 2px solid #000;
}

.revenue-card h3,
.revenue-card p {
    color: #fff;
}

.messages-card {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: #fff;
    border: 2px solid #007bff;
}

.messages-card h3,
.messages-card p {
    color: #fff;
}

.orders-section {
    background: #fff;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    border: 2px solid #000;
    margin-bottom: 30px;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    flex-wrap: wrap;
    gap: 15px;
}

.section-header h2 {
    color: #000;
    margin: 0;
}

.orders-count {
    background: #000;
    color: #fff;
    padding: 5px 15px;
    border-radius: 20px;
    font-weight: bold;
}

.messages-count {
    background: #007bff;
    color: #fff;
    padding: 5px 15px;
    border-radius: 20px;
    font-weight: bold;
}

/* ✅ IMPROVED ORDER ITEM STYLES */
.order-item {
    display: flex;
    gap: 20px;
    padding: 25px;
    border-bottom: 1px solid #eee;
    transition: background-color 0.3s ease;
    align-items: flex-start;
}

.order-item:hover {
    background-color: #f9f9f9;
}

.order-item:last-child {
    border-bottom: none;
}

.client-avatar-small {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #000;
    background: #f0f0f0;
    flex-shrink: 0;
}

.product-image-small {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid #000;
    flex-shrink: 0;
}

.image-placeholder {
    width: 80px;
    height: 80px;
    background: #f8f9fa;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px dashed #ccc;
    font-size: 0.7em;
    color: #666;
    text-align: center;
    flex-shrink: 0;
}

.order-content {
    flex: 1;
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 20px;
    align-items: start;
}

.order-main-info {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.order-header-info {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 10px;
}

.order-id {
    font-size: 1.3em;
    font-weight: bold;
    color: #000;
}

.order-details-grid {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 8px 15px;
    align-items: start;
}

.order-label {
    font-weight: bold;
    color: #000;
    min-width: 100px;
}

.order-value {
    color: #666;
}

.order-meta {
    font-size: 0.85em;
    color: #888;
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #eee;
}

.order-actions {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 12px;
    min-width: 180px;
}

/* CONTACT MESSAGE STYLES */
.message-item {
    padding: 25px;
    border-bottom: 1px solid #eee;
    transition: background-color 0.3s ease;
}

.message-item:hover {
    background-color: #f9f9f9;
}

.message-item:last-child {
    border-bottom: none;
}

.message-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
    flex-wrap: wrap;
    gap: 15px;
}

.message-sender {
    flex: 1;
}

.message-sender h4 {
    color: #000;
    margin-bottom: 5px;
    font-size: 1.2em;
}

.message-sender p {
    color: #666;
    margin: 2px 0;
}

.message-subject {
    font-weight: bold;
    color: #000;
    margin-bottom: 10px;
}

.message-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    margin-bottom: 15px;
}

.message-content p {
    margin: 0;
    line-height: 1.6;
    color: #333;
}

.message-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    font-size: 0.85em;
    color: #666;
}

.message-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

/* STATUS BADGES */
.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: bold;
    text-transform: uppercase;
    font-size: 0.8em;
    border: 2px solid;
    text-align: center;
}

.status-pending { 
    background: #fff3cd; 
    color: #856404; 
    border-color: #ffeaa7;
}

.status-approved { 
    background: #d1ecf1; 
    color: #0c5460; 
    border-color: #bee5eb;
}

.status-shipped { 
    background: #d4edda; 
    color: #155724; 
    border-color: #c3e6cb;
}

.status-delivered { 
    background: #28a745; 
    color: white; 
    border-color: #1e7e34;
}

.status-cancelled { 
    background: #f8d7da; 
    color: #721c24; 
    border-color: #f5c6cb;
}

.status-unread {
    background: #007bff;
    color: white;
    border-color: #0056b3;
}

.status-read {
    background: #6c757d;
    color: white;
    border-color: #545b62;
}

/* ACTION BUTTONS */
.action-buttons {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    justify-content: flex-end;
}

.action-btn {
    padding: 8px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.3s ease;
    font-size: 0.85em;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    min-width: 80px;
}

.action-btn.primary {
    background: #000;
    color: #fff;
    border: 2px solid #000;
}

.action-btn.primary:hover {
    background: #fff;
    color: #000;
    transform: translateY(-2px);
}

.action-btn.secondary {
    background: #6c757d;
    color: #fff;
    border: 2px solid #6c757d;
}

.action-btn.secondary:hover {
    background: #fff;
    color: #6c757d;
    transform: translateY(-2px);
}

.action-btn.danger {
    background: #dc3545;
    color: #fff;
    border: 2px solid #dc3545;
}

.action-btn.danger:hover {
    background: #fff;
    color: #dc3545;
    transform: translateY(-2px);
}

.action-btn.info {
    background: #007bff;
    color: #fff;
    border: 2px solid #007bff;
}

.action-btn.info:hover {
    background: #fff;
    color: #007bff;
    transform: translateY(-2px);
}

/* ALERT STYLES */
.alert {
    padding: 15px 20px;
    margin-bottom: 25px;
    border-radius: 8px;
    font-weight: bold;
    text-align: center;
    border: 2px solid;
}

.alert-success {
    background: #d1edf1;
    color: #0c5460;
    border-color: #bee5eb;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border-color: #f5c6cb;
}

.no-orders, .no-messages {
    text-align: center;
    padding: 40px;
    color: #666;
}

.no-orders h3, .no-messages h3 {
    margin-bottom: 10px;
}

/* RESPONSIVE DESIGN */
@media (max-width: 768px) {
    .admin-dashboard {
        margin-top: 80px;
        padding: 15px;
    }
    
    .admin-header {
        flex-direction: column;
        text-align: center;
    }
    
    .admin-profile {
        flex-direction: column;
        text-align: center;
    }
    
    .admin-info h1 {
        font-size: 1.8em;
    }
    
    .stat-cards {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .order-item {
        flex-direction: column;
        gap: 15px;
    }
    
    .order-content {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .order-actions {
        align-items: stretch;
        min-width: auto;
    }
    
    .action-buttons {
        justify-content: center;
    }
    
    .section-header {
        flex-direction: column;
        text-align: center;
    }
    
    .order-header-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .message-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .message-meta {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .message-actions {
        justify-content: flex-start;
        width: 100%;
    }
}

@media (max-width: 480px) {
    .stat-cards {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .admin-actions {
        flex-direction: column;
        width: 100%;
    }
    
    .admin-btn {
        text-align: center;
    }
    
    .order-details-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="admin-dashboard">
    <!-- ADMIN PROFILE HEADER -->
    <div class="admin-header">
        <div class="admin-profile">
            <?php
            $profile_pic = 'uploads/' . $admin['profile_picture'];
            $logo_pic = 'assets/images/logo2.png';
            $default_pic = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 80 80"><rect width="80" height="80" fill="#f0f0f0"/><text x="40" y="40" font-family="Arial" font-size="12" fill="#666" text-anchor="middle" dy=".3em">A</text></svg>');
            
            if(file_exists($profile_pic) && $admin['profile_picture'] != 'default.png') {
                $display_pic = $profile_pic;
            } elseif(file_exists($logo_pic)) {
                $display_pic = $logo_pic;
            } else {
                $display_pic = $default_pic;
            }
            ?>
            <img src="<?php echo $display_pic; ?>" 
                 alt="Admin Profile" class="admin-avatar"
                 onerror="this.src='<?php echo $default_pic; ?>'">
            <div class="admin-info">
                <h1>ToyRex Admin</h1>
                <p>@<?php echo htmlspecialchars($admin['username']); ?></p>
                <p>📧 <?php echo htmlspecialchars($admin['email']); ?></p>
            </div>
        </div>
        <div class="admin-actions">
            <a href="products.php" class="admin-btn">Manage Products</a>
            <a href="index.php" class="admin-btn secondary">View Site</a>
        </div>
    </div>
    
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <!-- Statistics Cards -->
    <div class="stat-cards">
        <div class="stat-card">
            <h3><?php echo $client_count; ?></h3>
            <p>Total Clients</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $product_count; ?></h3>
            <p>Products</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $pending_count; ?></h3>
            <p>Pending Orders</p>
        </div>
        <div class="stat-card revenue-card">
            <h3>₱<?php echo number_format($total_revenue, 2); ?></h3>
            <p>Total Revenue</p>
        </div>
        <div class="stat-card messages-card">
            <h3><?php echo $unread_messages; ?></h3>
            <p>Unread Messages</p>
        </div>
        <?php if($approved_count > 0): ?>
        <div class="stat-card">
            <h3><?php echo $approved_count; ?></h3>
            <p>Approved Orders</p>
        </div>
        <?php endif; ?>
        <?php if($shipped_count > 0): ?>
        <div class="stat-card">
            <h3><?php echo $shipped_count; ?></h3>
            <p>Shipped Orders</p>
        </div>
        <?php endif; ?>
        <?php if($delivered_count > 0): ?>
        <div class="stat-card">
            <h3><?php echo $delivered_count; ?></h3>
            <p>Delivered Orders</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- All Orders - IMPROVED LAYOUT -->
    <div class="orders-section">
        <div class="section-header">
            <h2>All Orders 📋</h2>
            <span class="orders-count"><?php echo count($all_orders); ?> Orders</span>
        </div>
        
        <?php if($all_orders && count($all_orders) > 0): ?>
            <?php foreach($all_orders as $order): ?>
            <div class="order-item">
                <!-- Client Profile Picture -->
                <?php
                $client_profile_pic = 'uploads/' . $order['profile_picture'];
                $default_client_pic = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 60 60"><rect width="60" height="60" fill="#f0f0f0"/><text x="30" y="30" font-family="Arial" font-size="10" fill="#666" text-anchor="middle" dy=".3em">👤</text></svg>');
                
                if(file_exists($client_profile_pic) && $order['profile_picture'] != 'default.png') {
                    $client_display_pic = $client_profile_pic;
                } else {
                    $client_display_pic = $default_client_pic;
                }
                ?>
                <img src="<?php echo $client_display_pic; ?>" 
                     alt="Client Profile" class="client-avatar-small"
                     onerror="this.src='<?php echo $default_client_pic; ?>'">
                
                <!-- Product Image -->
                <?php
                $productName = $order['product_name'];
                $productImagePath = "";
                $productImageExists = false;
                
                // Map product names to image files
                $imageMap = [
                    'rx-93 nu gundam' => 'RX-93',
                    'oz-13ms gundam epyon' => 'QZ-13',
                    'metal robot spirits hi-ν gundam' => 'Hi-v',
                    'nendoroid raiden shogun' => 'Raiden',
                    'nendoroid robocosan' => 'Robocosan',
                    'nendoroid hashirama senju' => 'Hashirama',
                    'nendoroid eren yeager' => 'Eren',
                    'nendoroid loid forger' => 'Loid',
                    'sofvimates chopper' => 'Chopper'
                ];
                
                $lowerProductName = strtolower($productName);
                
                if (isset($imageMap[$lowerProductName])) {
                    $baseName = $imageMap[$lowerProductName];
                    $extensions = ['.jpg', '.JPG', '.jpeg', '.JPEG', '.png', '.PNG'];
                    
                    foreach ($extensions as $ext) {
                        $testPath = "assets/images/" . $baseName . $ext;
                        if (file_exists($testPath)) {
                            $productImagePath = $testPath;
                            $productImageExists = true;
                            break;
                        }
                    }
                }
                ?>
                
                <?php if($productImageExists): ?>
                    <img src="<?php echo $productImagePath; ?>" 
                         alt="<?php echo htmlspecialchars($order['product_name']); ?>"
                         class="product-image-small">
                <?php else: ?>
                    <div class="image-placeholder">
                        <small>Product Image</small>
                    </div>
                <?php endif; ?>
                
                <div class="order-content">
                    <div class="order-main-info">
                        <div class="order-header-info">
                            <span class="order-id">Order #<?php echo $order['id']; ?></span>
                            <span class="status-badge status-<?php echo $order['status']; ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </div>
                        
                        <div class="order-details-grid">
                            <span class="order-label">Client Name:</span>
                            <span class="order-value"><?php echo htmlspecialchars($order['full_name']); ?></span>
                            
                            <span class="order-label">Username:</span>
                            <span class="order-value">@<?php echo htmlspecialchars($order['username']); ?></span>
                            
                            <span class="order-label">Product:</span>
                            <span class="order-value"><?php echo htmlspecialchars($order['product_name']); ?></span>
                            
                            <span class="order-label">Category:</span>
                            <span class="order-value"><?php echo htmlspecialchars($order['category']); ?></span>
                            
                            <span class="order-label">Quantity:</span>
                            <span class="order-value"><?php echo $order['quantity']; ?> pcs</span>
                            
                            <span class="order-label">Unit Price:</span>
                            <span class="order-value">₱<?php echo number_format($order['price'], 2); ?></span>
                            
                            <span class="order-label">Total Amount:</span>
                            <span class="order-value" style="font-weight: bold; color: #000;">
                                ₱<?php echo number_format($order['total_price'], 2); ?>
                            </span>
                        </div>
                        
                        <div class="order-meta">
                            <small>📅 Order Date: <?php echo date('M d, Y h:i A', strtotime($order['order_date'])); ?></small>
                            <?php if($order['updated_at'] != $order['order_date']): ?>
                                <br><small>🔄 Last Updated: <?php echo date('M d, Y h:i A', strtotime($order['updated_at'])); ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="order-actions">
                        <form method="POST" class="action-buttons">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <input type="hidden" name="update_order" value="1">
                            
                            <?php if($order['status'] == 'pending'): ?>
                                <button type="submit" name="status" value="approved" class="action-btn primary">Approve</button>
                                <button type="submit" name="status" value="cancelled" class="action-btn danger">Cancel</button>
                            <?php elseif($order['status'] == 'approved'): ?>
                                <button type="submit" name="status" value="shipped" class="action-btn primary">Ship</button>
                                <button type="submit" name="status" value="cancelled" class="action-btn danger">Cancel</button>
                            <?php elseif($order['status'] == 'shipped'): ?>
                                <button type="submit" name="status" value="delivered" class="action-btn primary">Deliver</button>
                            <?php elseif($order['status'] == 'delivered'): ?>
                                <span class="action-btn" style="background: #28a745; color: white; cursor: default; text-align: center;">Completed</span>
                            <?php elseif($order['status'] == 'cancelled'): ?>
                                <span class="action-btn" style="background: #dc3545; color: white; cursor: default; text-align: center;">Cancelled</span>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-orders">
                <h3>📭 No Orders Yet</h3>
                <p>There are no orders in the system yet.</p>
                <p>Orders will appear here when clients make purchases.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Contact Messages Section -->
    <div class="orders-section">
        <div class="section-header">
            <h2>📧 Contact Messages</h2>
            <span class="messages-count"><?php echo $unread_messages; ?> Unread / <?php echo $total_messages; ?> Total</span>
        </div>
        
        <?php if($contact_messages && count($contact_messages) > 0): ?>
            <?php foreach($contact_messages as $message): ?>
            <div class="message-item">
                <div class="message-header">
                    <div class="message-sender">
                        <h4><?php echo htmlspecialchars($message['name']); ?></h4>
                        <p>📧 <?php echo htmlspecialchars($message['email']); ?></p>
                        <div class="message-subject">
                            Subject: <?php echo htmlspecialchars($message['subject']); ?>
                        </div>
                    </div>
                    <span class="status-badge status-<?php echo $message['status']; ?>">
                        <?php echo ucfirst($message['status']); ?>
                    </span>
                </div>
                
                <div class="message-content">
                    <p><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
                </div>
                
                <div class="message-meta">
                    <span>📅 Received: <?php echo date('M d, Y h:i A', strtotime($message['created_at'])); ?></span>
                    <div class="message-actions">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                            <?php if($message['status'] == 'unread'): ?>
                                <button type="submit" name="mark_as_read" class="action-btn primary">Mark as Read</button>
                            <?php else: ?>
                                <button type="submit" name="mark_as_unread" class="action-btn secondary">Mark as Unread</button>
                            <?php endif; ?>
                            <button type="submit" name="delete_message" class="action-btn danger" onclick="return confirm('Are you sure you want to delete this message?')">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-messages">
                <h3>📭 No Messages Yet</h3>
                <p>No contact messages have been received yet.</p>
                <p>Messages will appear here when customers contact you through the contact form.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php 
include 'includes/footer.php'; 
ob_end_flush();
?>
