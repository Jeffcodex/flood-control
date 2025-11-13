<?php
// ADMIN.PHP - FIXED VERSION
ob_start(); // ✅ ADD OUTPUT BUFFERING

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// STRICT SECURITY CHECK
if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin'){
    $_SESSION['error'] = "Access denied! Admin privileges required.";
    ob_end_clean(); // ✅ CLEAN BUFFER BEFORE REDIRECT
    header("Location: index.php");
    exit();
}

// INCLUDE DATABASE
if(file_exists('config/database.php')) {
    include 'config/database.php';
} else {
    die('Database configuration not found!');
}

// Handle order status update - MOVED BEFORE ANY HTML OUTPUT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
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
    
    ob_end_clean(); // ✅ CLEAN BUFFER BEFORE REDIRECT
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
    
} catch(PDOException $e) {
    // Default values if query fails
    $product_count = $client_count = $pending_count = $total_orders = $total_revenue = 0;
    $approved_count = $shipped_count = $delivered_count = 0;
    error_log("Statistics query failed: " . $e->getMessage());
}

// Get all orders with user and product info
try {
    $order_stmt = $pdo->prepare("
        SELECT o.*, u.username, u.full_name, u.email, p.name as product_name, p.price, p.image, p.category
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

// NOW INCLUDE HEADER - HTML STARTS HERE
include 'includes/header.php';
?>

<!-- ADMIN DASHBOARD STYLES -->
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

.orders-section {
    background: #fff;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    border: 2px solid #000;
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

.order-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 20px;
    border-bottom: 1px solid #eee;
    transition: background-color 0.3s ease;
}

.order-item:hover {
    background-color: #f9f9f9;
}

.order-item:last-child {
    border-bottom: none;
}

.order-info {
    flex: 1;
}

.order-info strong {
    color: #000;
    font-size: 1.1em;
    display: block;
    margin-bottom: 8px;
}

.order-details {
    color: #666;
    margin-bottom: 5px;
}

.order-meta {
    font-size: 0.9em;
    color: #888;
    margin-top: 8px;
}

.order-actions {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 10px;
    min-width: 200px;
}

/* STATUS BADGES */
.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: bold;
    text-transform: uppercase;
    font-size: 0.8em;
    border: 2px solid;
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

/* ACTION BUTTONS */
.action-buttons {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
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

.no-orders {
    text-align: center;
    padding: 40px;
    color: #666;
}

.no-orders h3 {
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
}
</style>

<div class="admin-dashboard">
    <!-- ADMIN PROFILE HEADER -->
    <div class="admin-header">
        <div class="admin-profile">
            <?php
            // Profile picture handling
            $profile_pic = 'uploads/' . $admin['profile_picture'];
            $logo_pic = 'assets/images/logo2.png'; // Gamitin ang logo2.png
            $default_pic = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 80 80"><rect width="80" height="80" fill="#f0f0f0"/><text x="40" y="40" font-family="Arial" font-size="12" fill="#666" text-anchor="middle" dy=".3em">A</text></svg>');
            
            // Check if profile picture exists, if not use logo2.png, if logo2 doesn't exist use default
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
                <!-- CHANGED: Name to "ToyRex Admin" -->
                <h1>ToyRex Admin </h1>
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

    <!-- All Orders -->
    <div class="orders-section">
        <div class="section-header">
            <h2>All Orders 📋</h2>
            <span class="orders-count"><?php echo count($all_orders); ?> Orders</span>
        </div>
        
        <?php if($all_orders && count($all_orders) > 0): ?>
            <?php foreach($all_orders as $order): ?>
            <div class="order-item">
                <div class="order-info">
                    <strong>Order #<?php echo $order['id']; ?></strong>
                    <div class="order-details">
                        <p><strong>Client:</strong> <?php echo htmlspecialchars($order['full_name']); ?> (<?php echo htmlspecialchars($order['username']); ?>)</p>
                        <p><strong>Product:</strong> <?php echo htmlspecialchars($order['product_name']); ?> - <?php echo htmlspecialchars($order['category']); ?></p>
                        <p><strong>Quantity:</strong> <?php echo $order['quantity']; ?> | <strong>Unit Price:</strong> ₱<?php echo number_format($order['price'], 2); ?></p>
                        <p><strong>Total:</strong> ₱<?php echo number_format($order['total_price'], 2); ?></p>
                    </div>
                    <div class="order-meta">
                        <small>Ordered: <?php echo date('M d, Y h:i A', strtotime($order['order_date'])); ?></small>
                        <?php if($order['updated_at'] != $order['order_date']): ?>
                            <br><small>Last updated: <?php echo date('M d, Y h:i A', strtotime($order['updated_at'])); ?></small>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="order-actions">
                    <span class="status-badge status-<?php echo $order['status']; ?>">
                        <?php echo ucfirst($order['status']); ?>
                    </span>
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
                            <span class="action-btn" style="background: #28a745; color: white; cursor: default;">Completed</span>
                        <?php elseif($order['status'] == 'cancelled'): ?>
                            <span class="action-btn" style="background: #dc3545; color: white; cursor: default;">Cancelled</span>
                        <?php endif; ?>
                    </form>
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
</div>

<?php 
// FOOTER WITH ERROR HANDLING
if(file_exists('includes/footer.php')) {
    include 'includes/footer.php';
} else {
    echo '</body></html>';
}
?>