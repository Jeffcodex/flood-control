<?php
session_start();
include 'includes/header.php';
include 'includes/cart_functions.php';

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_quantity'])) {
        $product_id = $_POST['product_id'];
        $quantity = intval($_POST['quantity']);
        updateCartQuantity($product_id, $quantity);
    } elseif (isset($_POST['remove_item'])) {
        $product_id = $_POST['product_id'];
        removeFromCart($product_id);
    } elseif (isset($_POST['clear_cart'])) {
        clearCart();
    }
}

$cart_total = getCartTotal();
$cart_count = getCartItemCount();
?>

<div class="container" style="margin-top: 100px;">
    <h1>🛒 Shopping Cart</h1>
    
    <?php if($cart_count > 0): ?>
        <div class="cart-summary">
            <p>Total Items: <?php echo $cart_count; ?> | Total Amount: ₱<?php echo number_format($cart_total, 2); ?></p>
        </div>
        
        <div class="cart-items">
            <?php foreach($_SESSION['cart'] as $product_id => $item): ?>
            <div class="cart-item">
                <div class="item-image">
                    <?php 
                    $imagePath = "assets/images/" . $item['image'];
                    $imageExists = file_exists($imagePath);
                    ?>
                    <?php if($imageExists): ?>
                        <img src="<?php echo $imagePath; ?>" alt="<?php echo $item['name']; ?>">
                    <?php else: ?>
                        <div class="placeholder-image" style="width: 80px; height: 80px;">
                            <span><?php echo substr($item['name'], 0, 2); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="item-details">
                    <h3><?php echo $item['name']; ?></h3>
                    <p class="price">₱<?php echo number_format($item['price'], 2); ?> each</p>
                </div>
                
                <div class="item-quantity">
                    <form method="POST" style="display: flex; gap: 10px; align-items: center;">
                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" style="width: 60px; padding: 5px;">
                        <button type="submit" name="update_quantity" class="action-btn primary">Update</button>
                    </form>
                </div>
                
                <div class="item-total">
                    <p>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                </div>
                
                <div class="item-actions">
                    <form method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                        <button type="submit" name="remove_item" class="action-btn secondary">Remove</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="cart-actions" style="display: flex; gap: 15px; margin-top: 30px;">
            <form method="POST">
                <button type="submit" name="clear_cart" class="gallery-btn" style="background: #dc3545;">Clear Cart</button>
            </form>
            <a href="checkout.php" class="gallery-btn">Proceed to Checkout</a>
            <a href="products.php" class="gallery-btn" style="background: #6c757d;">Continue Shopping</a>
        </div>
        
    <?php else: ?>
        <div class="no-orders">
            <div class="no-orders-icon">🛒</div>
            <h3>Your Cart is Empty</h3>
            <p>Looks like you haven't added any items to your cart yet.</p>
            <a href="products.php" class="shop-now-btn">🛍️ Start Shopping</a>
        </div>
    <?php endif; ?>
</div>

<style>
.cart-item {
    display: grid;
    grid-template-columns: 80px 1fr auto auto auto;
    gap: 20px;
    align-items: center;
    padding: 20px;
    background: var(--off-white);
    border-radius: 10px;
    margin-bottom: 15px;
    border: 1px solid var(--light-gray);
}

.item-image img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
}

.cart-summary {
    background: var(--pure-black);
    color: var(--pure-white);
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    text-align: center;
    font-weight: bold;
}

@media (max-width: 768px) {
    .cart-item {
        grid-template-columns: 1fr;
        text-align: center;
        gap: 10px;
    }
}
</style>

<?php include 'includes/footer.php'; ?>