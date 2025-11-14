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
// ✅ FIXED VERSION - COMPLETE CODE
ob_start();
if (session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'client'){
    ob_end_clean();
    header("Location: index.php");
    exit();
}

include 'config/database.php';

$user_id = $_SESSION['user_id'];

// Get client info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$client = $stmt->fetch();

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $username = trim($_POST['username']);
    
    try {
        // Check if username already exists (excluding current user)
        if ($username !== $client['username']) {
            $check_stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
            $check_stmt->execute([$username, $user_id]);
            if ($check_stmt->fetch()) {
                $_SESSION['error'] = "❌ Username already exists!";
                ob_end_clean();
                header("Location: client.php");
                exit();
            }
        }
        
        // Handle profile picture upload
        $profile_picture = $client['profile_picture'];
        if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            $upload_dir = 'uploads/';
            if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            
            $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
            $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
            
            if(in_array(strtolower($file_extension), $allowed_extensions)) {
                // Delete old profile picture if not default
                if($profile_picture != 'default.png' && file_exists($upload_dir . $profile_picture)) {
                    unlink($upload_dir . $profile_picture);
                }
                
                $profile_picture = uniqid() . '_' . $user_id . '.' . $file_extension;
                move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_dir . $profile_picture);
            }
        }
        
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, address = ?, phone = ?, profile_picture = ?, username = ? WHERE id = ?");
        $stmt->execute([$full_name, $email, $address, $phone, $profile_picture, $username, $user_id]);
        
        // Update session
        $_SESSION['full_name'] = $full_name;
        $_SESSION['email'] = $email;
        $_SESSION['username'] = $username;
        
        $_SESSION['success'] = "✅ Profile updated successfully!";
        ob_end_clean();
        header("Location: client.php");
        exit();
        
    } catch(PDOException $e) {
        $_SESSION['error'] = "❌ Error updating profile: " . $e->getMessage();
    }
}

// Handle Password Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    try {
        // Verify current password
        if (!password_verify($current_password, $client['password'])) {
            $_SESSION['error'] = "❌ Current password is incorrect!";
            ob_end_clean();
            header("Location: client.php");
            exit();
        }
        
        // Check if new password matches confirmation
        if ($new_password !== $confirm_password) {
            $_SESSION['error'] = "❌ New passwords do not match!";
            ob_end_clean();
            header("Location: client.php");
            exit();
        }
        
        // Check password length
        if (strlen($new_password) < 6) {
            $_SESSION['error'] = "❌ Password must be at least 6 characters long!";
            ob_end_clean();
            header("Location: client.php");
            exit();
        }
        
        // Hash new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $user_id]);
        
        $_SESSION['success'] = "✅ Password updated successfully!";
        ob_end_clean();
        header("Location: client.php");
        exit();
        
    } catch(PDOException $e) {
        $_SESSION['error'] = "❌ Error updating password: " . $e->getMessage();
    }
}

// Handle Order Cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) {
    $order_id = intval($_POST['order_id']);
    
    try {
        // Verify order belongs to user and is pending
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND client_id = ? AND status = 'pending'");
        $stmt->execute([$order_id, $user_id]);
        $order = $stmt->fetch();
        
        if($order) {
            // Update order status to cancelled
            $update_stmt = $pdo->prepare("UPDATE orders SET status = 'cancelled', updated_at = NOW() WHERE id = ?");
            if($update_stmt->execute([$order_id])) {
                // Restore product quantity
                $restore_stmt = $pdo->prepare("
                    UPDATE products p 
                    JOIN orders o ON p.id = o.product_id 
                    SET p.quantity = p.quantity + o.quantity 
                    WHERE o.id = ?
                ");
                $restore_stmt->execute([$order_id]);
                
                $_SESSION['success'] = "✅ Order #$order_id has been cancelled successfully!";
            } else {
                $_SESSION['error'] = "❌ Failed to cancel order #$order_id";
            }
        } else {
            $_SESSION['error'] = "❌ Order not found or cannot be cancelled";
        }
        
        ob_end_clean();
        header("Location: client.php");
        exit();
        
    } catch(PDOException $e) {
        $_SESSION['error'] = "❌ Database error: " . $e->getMessage();
    }
}

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
$cancelled_orders = 0;

foreach($orders as $order) {
    if($order['status'] == 'pending') $pending_orders++;
    if($order['status'] == 'approved') $approved_orders++;
    if($order['status'] == 'delivered') $delivered_orders++;
    if($order['status'] == 'cancelled') $cancelled_orders++;
}

// ✅ NOW INCLUDE HEADER - AFTER ALL PROCESSING
include 'includes/header.php';
?>

<style>
/* UPDATED STYLES - BLACK THEME FOR EDIT BUTTONS */
.edit-profile-btn {
    background: #000000 !important;
    color: white !important;
    border: 2px solid #000000 !important;
}

.edit-profile-btn:hover {
    background: #333333 !important;
    border-color: #333333 !important;
    transform: translateY(-2px);
}

.change-password-btn {
    background: #000000 !important;
    color: white !important;
    border: 2px solid #000000 !important;
    margin-top: 10px !important;
}

.change-password-btn:hover {
    background: #333333 !important;
    border-color: #333333 !important;
    transform: translateY(-2px);
}

.cancel-order-btn {
    background: #dc3545 !important;
    color: white !important;
    padding: 5px 10px !important;
    border-radius: 4px !important;
    border: 1px solid #dc3545 !important;
    font-size: 0.8em !important;
    cursor: pointer !important;
    margin-top: 5px !important;
}

.cancel-order-btn:hover {
    background: #c82333 !important;
    border-color: #c82333 !important;
}

.cancel-order-btn:disabled {
    background: #6c757d !important;
    border-color: #6c757d !important;
    cursor: not-allowed !important;
}

/* MODAL STYLES */
.profile-modal .modal-content {
    max-width: 500px !important;
}

.password-modal .modal-content {
    max-width: 450px !important;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #000;
}

.form-group input, .form-group textarea {
    width: 100%;
    padding: 10px;
    border: 2px solid #ddd;
    border-radius: 5px;
    font-size: 1em;
    transition: all 0.3s ease;
}

.form-group input:focus, .form-group textarea:focus {
    border-color: #000;
    outline: none;
    box-shadow: 0 0 5px rgba(0,0,0,0.2);
}

.password-strength {
    margin-top: 5px;
    font-size: 0.8em;
}

.weak { color: #dc3545; }
.medium { color: #ffc107; }
.strong { color: #28a745; }

/* EXISTING STYLES REMAIN THE SAME */
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
    width: 100px;
    height: 100px;
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
    flex-direction: column;
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
    text-align: center;
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

.order-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid #000;
}

.order-image-placeholder {
    width: 80px;
    height: 80px;
    background: #f8f9fa;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px dashed #ccc;
    font-size: 0.8em;
    color: #666;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: bold;
    text-transform: uppercase;
    font-size: 0.8em;
    border: 2px solid;
}

.status-pending { background: #fff3cd; color: #856404; border-color: #ffeaa7; }
.status-approved { background: #d1ecf1; color: #0c5460; border-color: #bee5eb; }
.status-shipped { background: #d4edda; color: #155724; border-color: #c3e6cb; }
.status-delivered { background: #28a745; color: white; border-color: #1e7e34; }
.status-cancelled { background: #f8d7da; color: #721c24; border-color: #f5c6cb; }

.no-orders {
    text-align: center;
    padding: 40px;
    color: #666;
}

@media (max-width: 768px) {
    .client-header { flex-direction: column; text-align: center; }
    .client-profile { flex-direction: column; text-align: center; }
    .client-info h1 { font-size: 1.8em; }
    .order-header { flex-direction: column; gap: 15px; }
    .section-header { flex-direction: column; text-align: center; }
    .client-actions { flex-direction: column; }
}
</style>

<div class="client-dashboard">
    <!-- CLIENT PROFILE HEADER -->
    <div class="client-header">
        <div class="client-profile">
            <?php
            $profile_pic = 'uploads/' . $client['profile_picture'];
            $logo_pic = 'assets/images/logo1.png';
            $default_pic = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect width="100" height="100" fill="#f0f0f0"/><text x="50" y="50" font-family="Arial" font-size="20" fill="#666" text-anchor="middle" dy=".3em">👤</text></svg>');
            
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
                <h1><?php echo htmlspecialchars($client['full_name'] ?: 'ToyRex Client'); ?> <span class="client-badge">CLIENT</span></h1>
                <p>👤 @<?php echo htmlspecialchars($client['username']); ?></p>
                <p>📧 <?php echo htmlspecialchars($client['email']); ?></p>
                <p>📍 <?php echo htmlspecialchars($client['address'] ?: 'Address not set'); ?></p>
                <p>📞 <?php echo htmlspecialchars($client['phone'] ?: 'Phone not set'); ?></p>
            </div>
        </div>
        <div class="client-actions">
            <button class="client-btn edit-profile-btn" onclick="openEditProfileModal()">✏️ Edit Profile</button>
            <button class="client-btn change-password-btn" onclick="openChangePasswordModal()">🔒 Change Password</button>
            <a href="products.php" class="client-btn">🛍️ Shop More</a>
            <a href="index.php" class="client-btn secondary">🏠 View Site</a>
        </div>
    </div>

    <!-- SUCCESS/ERROR MESSAGES -->
    <?php if(isset($_SESSION['success'])): ?>
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 2px solid #c3e6cb;">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['error'])): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 2px solid #f5c6cb;">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
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
        <?php if($cancelled_orders > 0): ?>
        <div class="stat-card">
            <h3><?php echo $cancelled_orders; ?></h3>
            <p>Cancelled Orders</p>
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
                        <div style="display: flex; gap: 15px; align-items: flex-start;">
                            <?php
                            // ✅ FIXED IMAGE DETECTION - WORKS FOR ALL PRODUCTS
                            $productName = $order['product_name'];
                            $imagePath = "";
                            $imageExists = false;
                            
                            // Map product names to image files (CASE INSENSITIVE)
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
                            
                            // Check if product exists in our map
                            if (isset($imageMap[$lowerProductName])) {
                                $baseName = $imageMap[$lowerProductName];
                                $extensions = ['.jpg', '.JPG', '.jpeg', '.JPEG', '.png', '.PNG'];
                                
                                foreach ($extensions as $ext) {
                                    $testPath = "assets/images/" . $baseName . $ext;
                                    if (file_exists($testPath)) {
                                        $imagePath = $testPath;
                                        $imageExists = true;
                                        break;
                                    }
                                }
                            }
                            
                            // If still not found, try the image from database
                            if (!$imageExists && !empty($order['image'])) {
                                $dbImage = $order['image'];
                                $baseName = pathinfo($dbImage, PATHINFO_FILENAME);
                                $extensions = ['.jpg', '.JPG', '.jpeg', '.JPEG', '.png', '.PNG'];
                                
                                foreach ($extensions as $ext) {
                                    $testPath = "assets/images/" . $baseName . $ext;
                                    if (file_exists($testPath)) {
                                        $imagePath = $testPath;
                                        $imageExists = true;
                                        break;
                                    }
                                }
                            }
                            ?>
                            
                            <?php if($imageExists): ?>
                                <img src="<?php echo $imagePath; ?>" 
                                     alt="<?php echo htmlspecialchars($order['product_name']); ?>"
                                     class="order-image"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="order-image-placeholder" style="display: none;">
                                    <small><?php echo substr($order['product_name'], 0, 12); ?></small>
                                </div>
                            <?php else: ?>
                                <div class="order-image-placeholder">
                                    <small><?php echo substr($order['product_name'], 0, 12); ?></small>
                                </div>
                            <?php endif; ?>
                            
                            <div>
                                <h4><?php echo htmlspecialchars($order['product_name']); ?></h4>
                                <div class="order-details">
                                    <p><strong>Order #:</strong> <?php echo $order['id']; ?></p>
                                    <p><strong>Quantity:</strong> <?php echo $order['quantity']; ?></p>
                                    <p><strong>Price:</strong> ₱<?php echo number_format($order['price'], 2); ?> each</p>
                                    <p><strong>Total:</strong> ₱<?php echo number_format($order['total_price'], 2); ?></p>
                                    <p><strong>Category:</strong> <?php echo htmlspecialchars($order['category']); ?></p>
                                </div>
                                
                                <!-- CANCEL ORDER BUTTON -->
                                <?php if($order['status'] == 'pending'): ?>
                                    <form method="POST" style="margin-top: 10px;">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <button type="submit" name="cancel_order" class="cancel-order-btn" 
                                                onclick="return confirm('Are you sure you want to cancel Order #<?php echo $order['id']; ?>?')">
                                            ❌ Cancel Order
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
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

<!-- EDIT PROFILE MODAL -->
<div class="modal-overlay" id="editProfileModal">
    <div class="modal-content profile-modal">
        <span class="close-modal" onclick="closeEditProfileModal()">&times;</span>
        <h2>✏️ Edit Profile</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="update_profile" value="1">
            
            <div class="form-group">
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($client['full_name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($client['username']); ?>" required>
                <small style="color: #666;">Username must be unique</small>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($client['email']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($client['address']); ?>">
            </div>
            
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($client['phone']); ?>">
            </div>
            
            <div class="form-group">
                <label for="profile_picture">Profile Picture:</label>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
                <small style="color: #666;">Current: <?php echo $client['profile_picture']; ?> (Leave empty to keep current)</small>
            </div>
            
            <button type="submit" class="client-btn" style="width: 100%; margin-top: 20px;">💾 Save Changes</button>
        </form>
    </div>
</div>

<!-- CHANGE PASSWORD MODAL -->
<div class="modal-overlay" id="changePasswordModal">
    <div class="modal-content password-modal">
        <span class="close-modal" onclick="closeChangePasswordModal()">&times;</span>
        <h2>🔒 Change Password</h2>
        <form method="POST">
            <input type="hidden" name="update_password" value="1">
            
            <div class="form-group">
                <label for="current_password">Current Password:</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required minlength="6">
                <div id="password-strength" class="password-strength"></div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                <div id="password-match" class="password-strength"></div>
            </div>
            
            <button type="submit" class="client-btn" style="width: 100%; margin-top: 20px;">🔑 Update Password</button>
        </form>
    </div>
</div>

<script>
function openEditProfileModal() {
    document.getElementById('editProfileModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeEditProfileModal() {
    document.getElementById('editProfileModal').classList.remove('active');
    document.body.style.overflow = 'auto';
}

function openChangePasswordModal() {
    document.getElementById('changePasswordModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeChangePasswordModal() {
    document.getElementById('changePasswordModal').classList.remove('active');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.querySelectorAll('.modal-overlay').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            if (this.id === 'editProfileModal') closeEditProfileModal();
            if (this.id === 'changePasswordModal') closeChangePasswordModal();
        }
    });
});

// Close with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEditProfileModal();
        closeChangePasswordModal();
    }
});

// Password strength checker
document.getElementById('new_password').addEventListener('input', function() {
    const password = this.value;
    const strengthText = document.getElementById('password-strength');
    
    if (password.length === 0) {
        strengthText.textContent = '';
        return;
    }
    
    let strength = 'weak';
    if (password.length >= 8) strength = 'medium';
    if (password.length >= 10 && /[A-Z]/.test(password) && /[0-9]/.test(password)) strength = 'strong';
    
    strengthText.textContent = `Password strength: ${strength}`;
    strengthText.className = `password-strength ${strength}`;
});

// Password match checker
document.getElementById('confirm_password').addEventListener('input', function() {
    const confirmPassword = this.value;
    const newPassword = document.getElementById('new_password').value;
    const matchText = document.getElementById('password-match');
    
    if (confirmPassword.length === 0) {
        matchText.textContent = '';
        return;
    }
    
    if (confirmPassword === newPassword) {
        matchText.textContent = '✓ Passwords match';
        matchText.className = 'password-strength strong';
    } else {
        matchText.textContent = '✗ Passwords do not match';
        matchText.className = 'password-strength weak';
    }
});
</script>

<?php include 'includes/footer.php'; ob_end_flush(); ?>
