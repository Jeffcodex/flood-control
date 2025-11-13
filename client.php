<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'client'){
    header("Location: index.php");
    exit();
}

include 'config/database.php';
include 'includes/header.php';

$user_id = $_SESSION['user_id'];

// Get client info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$client = $stmt->fetch();

// Get client orders with product info
$stmt = $pdo->prepare("
    SELECT o.*, p.name as product_name, p.price, p.image, p.category 
    FROM orders o 
    JOIN products p ON o.product_id = p.id 
    WHERE o.client_id = ? 
    ORDER BY o.order_date DESC
");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

// Calculate order stats
$total_orders = count($orders);
$pending_orders = 0;
$approved_orders = 0;
$delivered_orders = 0;
foreach($orders as $order) {
    if($order['status'] == 'pending') $pending_orders++;
    if($order['status'] == 'approved') $approved_orders++;
    if($order['status'] == 'delivered') $delivered_orders++;
}
?>

<!-- ADD CLIENT PROFILE STYLES -->
<style>
.client-dashboard {
    margin-top: 100px;
    padding: 20px;
    max-width: 1200px;
    margin-left: auto;
    margin-right: auto;
}

.client-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    flex-wrap: wrap;
    gap: 20px;
    background: #fff;
    padding: 30px;
    border-radius: 15px;
    border: 2px solid #000;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.client-profile {
    display: flex;
    align-items: center;
    gap: 20px;
}

.client-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #000;
    background: #f0f0f0;
}

.client-info h1 {
    margin-bottom: 5px;
    color: #000;
    font-size: 2.2em;
}

.client-info p {
    color: #666;
    margin: 3px 0;
}

.client-badge {
    display: inline-block;
    background: #000;
    color: #fff;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.7em;
    font-weight: bold;
    margin-left: 8px;
}

.client-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.client-btn {
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

.client-btn:hover {
    background: #fff;
    color: #000;
    transform: translateY(-2px);
}

.client-btn.secondary {
    background: #6c757d;
    border-color: #6c757d;
}

.client-btn.secondary:hover {
    background: #fff;
    color: #6c757d;
}

.stat-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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

.order-card {
    padding: 20px;
    border-bottom: 1px solid #eee;
    transition: background-color 0.3s ease;
}

.order-card:hover {
    background-color: #f9f9f9;
}

.order-card:last-child {
    border-bottom: none;
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
}

.order-product {
    flex: 1;
}

.order-product h4 {
    color: #000;
    margin-bottom: 10px;
    font-size: 1.2em;
}

.order-details {
    color: #666;
    margin-bottom: 5px;
}

.order-meta {
    color: #888;
    font-size: 0.9em;
    margin-top: 10px;
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
    .client-header {
        flex-direction: column;
        text-align: center;
    }
    
    .client-profile {
        flex-direction: column;
        text-align: center;
    }
    
    .client-info h1 {
        font-size: 1.8em;
    }
    
    .order-header {
        flex-direction: column;
        gap: 15px;
    }
    
    .section-header {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<div class="client-dashboard">
    <!-- CLIENT PROFILE HEADER -->
    <div class="client-header">
        <div class="client-profile">
            <?php
            // Profile picture handling - Gamitin ang logo1.png para sa client
            $profile_pic = 'uploads/' . $client['profile_picture'];
            $logo_pic = 'assets/images/logo1.png'; // Gamitin ang logo1.png
            $default_pic = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 80 80"><rect width="80" height="80" fill="#f0f0f0"/><text x="40" y="40" font-family="Arial" font-size="12" fill="#666" text-anchor="middle" dy=".3em">C</text></svg>');
            
            // Check if profile picture exists, if not use logo1.png, if logo1 doesn't exist use default
            if(file_exists($profile_pic) && $client['profile_picture'] != 'default.png') {
                $display_pic = $profile_pic;
            } elseif(file_exists($logo_pic)) {
                $display_pic = $logo_pic;
            } else {
                $display_pic = $default_pic;
            }
            ?>
            <img src="<?php echo $display_pic; ?>" 
                 alt="Client Profile" class="client-avatar"
                 onerror="this.src='<?php echo $default_pic; ?>'">
            <div class="client-info">
                <!-- CHANGED: Name to "ToyRex Client" -->
                <h1>ToyRex Client <spn </span></h1>
                <p>👤 @<?php echo htmlspecialchars($client['username']); ?></p>
                <p>📧 <?php echo htmlspecialchars($client['email']); ?></p>
                <p>📍 <?php echo htmlspecialchars($client['address'] ?: 'Address not set'); ?></p>
                <p>📞 <?php echo htmlspecialchars($client['phone'] ?: 'Phone not set'); ?></p>
            </div>
        </div>
        <div class="client-actions">
            <a href="products.php" class="client-btn">🛍️ Shop More</a>
            <a href="index.php" class="client-btn secondary">🏠 View Site</a>
        </div>
    </div>
    
    <!-- Order Statistics -->
    <div class="stat-cards">
        <div class="stat-card">
            <h3><?php echo $total_orders; ?></h3>
            <p>Total Orders</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $pending_orders; ?></h3>
            <p>Pending Orders</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $approved_orders; ?></h3>
            <p>Approved Orders</p>
        </div>
        <?php if($delivered_orders > 0): ?>
        <div class="stat-card">
            <h3><?php echo $delivered_orders; ?></h3>
            <p>Delivered Orders</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Order History -->
    <div class="orders-section">
        <div class="section-header">
            <h2>📋 My Order History</h2>
            <span class="orders-count"><?php echo count($orders); ?> Orders</span>
        </div>
        
        <?php if($orders): ?>
            <?php foreach($orders as $order): ?>
            <div class="order-card">
                <div class="order-header">
                    <div class="order-product">
                        <h4><?php echo htmlspecialchars($order['product_name']); ?></h4>
                        <div class="order-details">
                            <p><strong>Order #:</strong> <?php echo $order['id']; ?></p>
                            <p><strong>Quantity:</strong> <?php echo $order['quantity']; ?></p>
                            <p><strong>Price:</strong> ₱<?php echo number_format($order['price'], 2); ?> each</p>
                            <p><strong>Total:</strong> ₱<?php echo number_format($order['total_price'], 2); ?></p>
                            <p><strong>Category:</strong> <?php echo htmlspecialchars($order['category']); ?></p>
                        </div>
                        <div class="order-meta">
                            <small>📅 Ordered: <?php echo date('M d, Y h:i A', strtotime($order['order_date'])); ?></small>
                            <?php if($order['updated_at'] != $order['order_date']): ?>
                                <br><small>🔄 Updated: <?php echo date('M d, Y h:i A', strtotime($order['updated_at'])); ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <span class="status-badge status-<?php echo $order['status']; ?>">
                        <?php echo ucfirst($order['status']); ?>
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-orders">
                <h3>📭 No Orders Yet</h3>
                <p>You haven't placed any orders yet.</p>
                <p><a href="products.php" class="client-btn" style="display: inline-block; margin-top: 15px;">🛍️ Start Shopping Now!</a></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>