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
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(file_exists('config/database.php')) {
    include 'config/database.php';
} else {
    die('Database configuration not found!');
}

// Get products from database
$stmt = $pdo->query("SELECT * FROM products WHERE quantity > 0 ORDER BY created_at DESC");
$products = $stmt->fetchAll();

function getProductImage($productName) {
    $imageMap = [
        'RX-93 Nu Gundam' => 'RX-93',
        'OZ-13MS Gundam Epyon' => 'QZ-13', 
        'Metal Robot Spirits Hi-ν Gundam' => 'Hi-v',
        'Nendoroid Raiden Shogun' => 'Raiden',
        'Nendoroid Robocosan' => 'Robocosan',
        'Nendoroid Hashirama Senju' => 'Hashirama',
        'Nendoroid Eren Yeager' => 'Eren',
        'Nendoroid Loid Forger' => 'Loid',
        'Sofvimates Chopper' => 'Chopper'
    ];
    
    $baseName = $imageMap[$productName] ?? 'default';
    $extensions = ['.png', '.jpg', '.jpeg', '.PNG', '.JPG', '.JPEG'];
    foreach ($extensions as $ext) {
        $fullPath = "assets/images/" . $baseName . $ext;
        if (file_exists($fullPath)) {
            return $baseName . $ext;
        }
    }
    return 'default.jpg';
}

// ORDER SYSTEM - CREATE ORDER IN DATABASE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'client') {
        $_SESSION['error'] = "Please login as client to place orders";
        ob_end_clean();
        header("Location: products.php");
        exit();
    }
    
    $product_id = intval($_POST['product_id']);
    $client_id = $_SESSION['user_id'];
    $quantity = 1;
    
    try {
        // Verify product exists
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND quantity > 0");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        
        if (!$product) {
            $_SESSION['error'] = "❌ Product not available";
            ob_end_clean();
            header("Location: products.php");
            exit();
        }
        
        $total_price = $product['price'] * $quantity;
        
        // CREATE ORDER - ITO ANG IMPORTANTE!
        $stmt = $pdo->prepare("INSERT INTO orders (client_id, product_id, quantity, total_price, status) VALUES (?, ?, ?, ?, 'pending')");
        $stmt->execute([$client_id, $product_id, $quantity, $total_price]);
        
        $order_id = $pdo->lastInsertId(); // Get the new order ID
        
        // Update product stock
        $new_quantity = $product['quantity'] - $quantity;
        $stmt = $pdo->prepare("UPDATE products SET quantity = ? WHERE id = ?");
        $stmt->execute([$new_quantity, $product_id]);
        
        $_SESSION['success'] = "✅ Order #$order_id placed successfully! Status: PENDING";
        
    } catch(PDOException $e) {
        $_SESSION['error'] = "❌ Error: " . $e->getMessage();
    }
    
    ob_end_clean();
    header("Location: products.php");
    exit();
}

include 'includes/header.php';
?>

<!-- CLEAN BLACK & WHITE CSS STYLES -->
<style>
/* ===== MODERN PRODUCT GALLERY - BLACK & WHITE ===== */
.gallery-section {
    padding: 100px 0 60px;
    background: #ffffff;
    min-height: 100vh;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.gallery-section h2 {
    text-align: center;
    font-size: 2.8em;
    margin-bottom: 15px;
    color: #000000;
    text-transform: uppercase;
    letter-spacing: 3px;
    font-weight: 800;
}

.section-subtitle {
    text-align: center;
    color: #666666;
    margin-bottom: 60px;
    font-size: 1.2em;
    font-weight: 400;
}

.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 30px;
}

.gallery-item {
    background: #ffffff;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    border: 2px solid #000000;
}

.gallery-item:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}

.image-container {
    height: 250px;
    overflow: hidden;
    position: relative;
    background: #f8f9fa;
}

.gallery-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.gallery-item:hover .gallery-img {
    transform: scale(1.05);
}

.placeholder-image {
    width: 100%;
    height: 100%;
    background: #f8f9fa;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #666666;
    text-align: center;
    padding: 20px;
    font-weight: 600;
    border: 2px dashed #cccccc;
}

.placeholder-image small {
    font-size: 0.8em;
    margin-top: 10px;
    font-weight: 400;
    color: #999999;
}

.gallery-info {
    padding: 25px;
}

.gallery-info h3 {
    color: #000000;
    margin-bottom: 15px;
    font-size: 1.4em;
    font-weight: 700;
    line-height: 1.3;
}

.gallery-info p {
    color: #666666;
    margin-bottom: 12px;
    line-height: 1.5;
    font-size: 0.95em;
}

.price {
    font-size: 1.8em;
    font-weight: 800;
    color: #000000;
    margin: 15px 0;
}

.stock, .category {
    color: #666666;
    font-size: 0.9em;
    font-weight: 500;
    display: inline-block;
    padding: 6px 12px;
    background: #f8f9fa;
    border-radius: 15px;
    margin-right: 8px;
    border: 1px solid #e0e0e0;
}

/* ===== CLEAN BUTTON STYLES - BLACK & WHITE ===== */
.gallery-btn {
    width: 100%;
    padding: 15px;
    background: #000000;
    color: #ffffff;
    border: 2px solid #000000;
    border-radius: 10px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: block;
    text-align: center;
    margin-top: 20px;
    font-size: 1.1em;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.gallery-btn:hover:not(:disabled) {
    background: #ffffff;
    color: #000000;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.gallery-btn:disabled {
    background: #666666;
    color: #ffffff;
    border-color: #666666;
    cursor: not-allowed;
    transform: none;
}

/* SPECIAL STYLE FOR LOGIN BUTTON */
.gallery-btn.login-btn {
    background: #000000;
    border: 2px solid #000000;
}

.gallery-btn.login-btn:hover {
    background: #ffffff;
    color: #000000;
}

.add-to-cart-form {
    margin: 0;
}

/* ===== CLEAN ALERT STYLES ===== */
.alert {
    padding: 20px;
    margin-bottom: 30px;
    border-radius: 10px;
    font-weight: 600;
    text-align: center;
    transition: all 0.3s ease;
    border: 2px solid;
    font-size: 1.1em;
}

.alert-success {
    background: #f8f9fa;
    color: #000000;
    border-color: #000000;
}

.alert-error {
    background: #f8f9fa;
    color: #000000;
    border-color: #000000;
}

/* ===== CLEAN HERO SECTION ===== */
.hero-section {
    background: #000000;
    color: #ffffff;
    padding: 120px 0 80px;
    text-align: center;
    border-bottom: 3px solid #ffffff;
}

.hero-section h1 {
    font-size: 3em;
    margin-bottom: 20px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.hero-section p {
    font-size: 1.3em;
    margin-bottom: 30px;
    color: #cccccc;
}

/* ✅ UPDATED: HERO BUTTON STYLES */
.hero-auth-btn {
    display: inline-block;
    background: #ffffff;
    color: #000000;
    padding: 15px 30px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.3s ease;
    border: 2px solid #ffffff;
    margin: 10px;
    font-size: 1.1em;
}

.hero-auth-btn:hover {
    background: transparent;
    color: #ffffff;
    transform: translateY(-3px);
}

/* ===== NO PRODUCTS STYLE ===== */
.no-products {
    text-align: center;
    padding: 80px 20px;
    grid-column: 1 / -1;
}

.no-products h3 {
    color: #666666;
    font-size: 1.6em;
    margin-bottom: 20px;
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 768px) {
    .gallery-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .gallery-section h2 {
        font-size: 2.2em;
    }
    
    .hero-section h1 {
        font-size: 2.2em;
    }
    
    .image-container {
        height: 200px;
    }
    
    .hero-auth-btn {
        display: block;
        margin: 10px auto;
        width: 200px;
    }
}

/* Auto-hide alerts */
.alert {
    animation: fadeOut 6s forwards;
    animation-delay: 4s;
}

@keyframes fadeOut {
    from { opacity: 1; transform: translateY(0); }
    to { opacity: 0; transform: translateY(-20px); display: none; }
}

/* ===== FLOATING ACTION BUTTON ===== */
.floating-login-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background: #000000;
    color: #ffffff;
    padding: 15px 25px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 700;
    border: 2px solid #000000;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    z-index: 1000;
    transition: all 0.3s ease;
    display: none;
}

.floating-login-btn:hover {
    background: #ffffff;
    color: #000000;
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.4);
}

@media (max-width: 768px) {
    .floating-login-btn {
        display: block;
    }
}
</style>

<!-- CLEAN HERO SECTION -->
<section class="hero-section">
    <div class="container">
        <h1>Premium Toy Collection</h1>
        <p>Discover Exclusive Figures & Limited Editions</p>
        <?php if(!isset($_SESSION['user_id'])): ?>
            <!-- ✅ UPDATED: DIRECT LINKS TO AUTH.PHP -->
            <div style="text-align: center; margin-top: 25px;">
                <a href="auth.php?tab=login" class="hero-auth-btn">🔐 Login to Start Ordering</a>
                <a href="auth.php?tab=register" class="hero-auth-btn" style="background: transparent; color: #ffffff; border: 2px solid #ffffff;">🚀 Create Account</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- PRODUCTS SECTION -->
<section class="gallery-section">
    <div class="container">
        <h2>Featured Products</h2>
        <p class="section-subtitle">Limited Stock Available - Order Now!</p>
        
        <!-- SUCCESS/ERROR MESSAGES -->
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
        
        <div class="gallery-grid">
            <?php if($products): ?>
                <?php foreach($products as $product): ?>
                <div class="gallery-item">
                    <?php 
                    $imageFile = getProductImage($product['name']);
                    $imagePath = "assets/images/" . $imageFile;
                    $imageExists = file_exists($imagePath);
                    ?>
                    
                    <div class="image-container">
                        <?php if($imageExists): ?>
                            <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="gallery-img">
                        <?php else: ?>
                            <div class="placeholder-image">
                                <span><?php echo $product['name']; ?></span>
                                <small>Product Image</small>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="gallery-info">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p><?php echo htmlspecialchars($product['description']); ?></p>
                        <p class="price">₱<?php echo number_format($product['price'], 2); ?></p>
                        <p>
                            <span class="stock">Stock: <?php echo $product['quantity']; ?></span>
                            <span class="category"><?php echo $product['category']; ?></span>
                        </p>
                        
                        <?php if(isset($_SESSION['user_id']) && $_SESSION['user_type'] == 'client'): ?>
                            <form method="POST" action="" class="add-to-cart-form">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="add_to_cart" value="1">
                                <button type="submit" class="gallery-btn">
                                    Order Now
                                </button>
                            </form>
                        <?php elseif(!isset($_SESSION['user_id'])): ?>
                            <!-- ✅ UPDATED: DIRECT LINK TO AUTH.PHP -->
                            <a href="auth.php?tab=login" class="gallery-btn login-btn">
                                🔐 Login to Order
                            </a>
                        <?php else: ?>
                            <button class="gallery-btn" disabled>
                                Admin Account
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-products">
                    <h3>No products available yet</h3>
                    <p>We're restocking our collection! Check back soon for amazing toys!</p>
                    <a href="index.php" class="gallery-btn">Return to Home</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ORDER HISTORY SECTION - ONLY SHOW IF LOGGED IN AS CLIENT -->
<?php if(isset($_SESSION['user_id']) && $_SESSION['user_type'] == 'client'): ?>
<?php
    $client_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("
        SELECT o.*, p.name as product_name, p.price, p.image, p.category 
        FROM orders o 
        JOIN products p ON o.product_id = p.id 
        WHERE o.client_id = ? 
        ORDER BY o.order_date DESC
    ");
    $stmt->execute([$client_id]);
    $user_orders = $stmt->fetchAll();
?>
    
<?php if($user_orders): ?>
<section style="padding: 60px 0; background: #f8f9fa; border-top: 3px solid #000000;">
    <div class="container">
        <h2 style="text-align: center; font-size: 2.5em; margin-bottom: 40px; color: #000000; text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">Order History</h2>
        
        <div style="max-width: 800px; margin: 0 auto;">
            <?php foreach($user_orders as $order): ?>
            <div style="background: #ffffff; border: 2px solid #000000; padding: 20px; margin-bottom: 15px; border-radius: 10px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 20px;">
                    <div style="flex: 1;">
                        <h4 style="margin: 0 0 10px 0; color: #000000; font-size: 1.2em; font-weight: 700;"><?php echo htmlspecialchars($order['product_name']); ?></h4>
                        <p style="margin: 5px 0; color: #666666;"><strong>Order #:</strong> <?php echo $order['id']; ?></p>
                        <p style="margin: 5px 0; color: #666666;"><strong>Quantity:</strong> <?php echo $order['quantity']; ?></p>
                        <p style="margin: 5px 0; color: #666666;"><strong>Total:</strong> ₱<?php echo number_format($order['total_price'], 2); ?></p>
                        <p style="margin: 5px 0; color: #666666;"><strong>Order Date:</strong> <?php echo date('M d, Y h:i A', strtotime($order['order_date'])); ?></p>
                    </div>
                    <span style="padding: 8px 16px; border-radius: 20px; font-weight: 700; text-transform: uppercase; font-size: 0.8em; letter-spacing: 1px; border: 2px solid #000000; background: #000000; color: #ffffff;">
                        <?php echo ucfirst($order['status']); ?>
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
<?php endif; ?>

<!-- ✅ UPDATED: FLOATING LOGIN BUTTON FOR MOBILE -->
<?php if(!isset($_SESSION['user_id'])): ?>
    <a href="auth.php?tab=login" class="floating-login-btn">
        🔐 Login
    </a>
<?php endif; ?>

<script>
// ✅ UPDATED: REMOVED openLoginModal FUNCTION SINCE DIRECT LINK NA

// Add smooth scrolling
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                if (alert.parentElement) {
                    alert.style.display = 'none';
                }
            }, 300);
        });
    }, 5000);
});
</script>

<?php 
include 'includes/footer.php';
ob_end_flush();
?>
