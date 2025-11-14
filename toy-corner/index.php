<?php 
// FIXED INCLUDES - Check multiple possible paths
if(file_exists('includes/header.php')) {
    include 'includes/header.php';
} elseif(file_exists('header.php')) {
    include 'header.php';
} else {
    die('Header file not found!');
}

// Include database connection
if(file_exists('config/database.php')) {
    include 'config/database.php';
} else {
    // If no database, show products anyway
    $featured_products = [];
}

// SMART IMAGE DETECTION SYSTEM
if (!function_exists('getImagePath')) {
    function getImagePath($baseName) {
        $extensions = ['.png', '.jpg', '.jpeg', '.PNG', '.JPG', '.JPEG'];
        $basePath = "assets/images/";
        
        foreach ($extensions as $ext) {
            $fullPath = $basePath . $baseName . $ext;
            if (file_exists($fullPath)) {
                return $baseName . $ext;
            }
        }
        return 'default.jpg';
    }
}

if (!function_exists('getProductImage')) {
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
        return getImagePath($baseName);
    }
}

// Get featured products from database (limit to 3)
$featured_products = [];
if(isset($pdo)) {
    try {
        $stmt = $pdo->query("SELECT * FROM products WHERE quantity > 0 ORDER BY created_at DESC LIMIT 3");
        $featured_products = $stmt->fetchAll();
    } catch(Exception $e) {
        // Database might not be set up yet - use sample products
        $featured_products = getSampleProducts();
    }
} else {
    // No database connection - use sample products
    $featured_products = getSampleProducts();
}

// SAMPLE PRODUCTS FALLBACK
function getSampleProducts() {
    return [
        [
            'id' => 1,
            'name' => 'RX-93 Nu Gundam',
            'description' => 'Master Grade Ver.Ka with psycho-frame and fin funnel system.',
            'price' => 4500.00,
            'quantity' => 3,
            'category' => 'Gundam'
        ],
        [
            'id' => 2,
            'name' => 'Nendoroid Raiden Shogun',
            'description' => 'Genshin Impact Nendoroid with multiple face plates and accessories.',
            'price' => 3030.00,
            'quantity' => 8,
            'category' => 'Nendoroid'
        ],
        [
            'id' => 3,
            'name' => 'Sofvimates Chopper',
            'description' => 'One Piece Sofvimates Chopper Zou Version - super soft and huggable!',
            'price' => 750.00,
            'quantity' => 15,
            'category' => 'Plush'
        ]
    ];
}
?>

<!-- SIMPLE BANNER STYLES -->
<style>
/* SIMPLE BANNER STYLES */
.simple-banner {
    width: 100%;
    overflow: hidden;
    position: relative;
    margin-top: 80px;
    background: #000;
    height: 500px;
}

.simple-slides {
    display: flex;
    transition: transform 0.8s ease-in-out;
    height: 100%;
}

.simple-slide {
    width: 100%;
    flex-shrink: 0;
    position: relative;
    height: 100%;
}

.simple-slide img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.simple-banner-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(0, 0, 0, 0.7);
    color: #fff;
    padding: 25px 40px;
    border-radius: 15px;
    text-align: center;
    border: 3px solid #fff;
}

.simple-banner-text h2 {
    font-size: 2.5em;
    margin-bottom: 15px;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.8);
}

.simple-banner-text p {
    font-size: 1.2em;
    margin-bottom: 20px;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.8);
}

.simple-banner-btn {
    display: inline-block;
    background: #fff;
    color: #000;
    padding: 12px 30px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.3s ease;
    border: 2px solid #fff;
    margin: 5px;
}

.simple-banner-btn:hover {
    background: transparent;
    color: #fff;
    transform: translateY(-3px);
}

.simple-banner-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0,0,0,0.5);
    color: white;
    border: none;
    padding: 15px;
    font-size: 24px;
    cursor: pointer;
    z-index: 10;
    transition: all 0.3s ease;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.simple-banner-arrow:hover {
    background: rgba(0,0,0,0.8);
    transform: translateY(-50%) scale(1.1);
}

.simple-prev-arrow {
    left: 20px;
}

.simple-next-arrow {
    right: 20px;
}

.simple-banner-dots {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 12px;
    z-index: 10;
}

.simple-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: rgba(255,255,255,0.5);
    cursor: pointer;
    transition: all 0.3s ease;
}

.simple-dot.active {
    background: #fff;
    transform: scale(1.2);
}

/* PRODUCT MODAL STYLES */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.9);
    justify-content: center;
    align-items: center;
    z-index: 2000;
}

.modal-overlay.active {
    display: flex !important;
}

.modal-content {
    background: #fff;
    padding: 30px;
    border-radius: 15px;
    width: 90%;
    max-width: 500px;
    position: relative;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
}

.close-modal {
    position: absolute;
    top: 15px;
    right: 20px;
    font-size: 30px;
    cursor: pointer;
    color: #000;
    background: none;
    border: none;
}

.product-modal-content {
    text-align: center;
}

.modal-product-image {
    margin-bottom: 20px;
}

.modal-product-image img {
    max-width: 100%;
    max-height: 300px;
    border-radius: 10px;
}

.modal-product-placeholder {
    width: 100%;
    height: 200px;
    background: #f0f0f0;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #666;
    font-weight: bold;
    border: 2px dashed #ccc;
}

.modal-product-info h2 {
    color: #000;
    margin-bottom: 15px;
    font-size: 1.8em;
}

.modal-product-info p {
    color: #666;
    margin-bottom: 10px;
    line-height: 1.6;
}

.modal-product-price {
    font-size: 1.8em;
    font-weight: bold;
    color: #000;
    margin: 15px 0;
}

.modal-actions {
    margin-top: 25px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.add-to-cart-modal, .login-to-buy {
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
}

.add-to-cart-modal {
    background: #000;
    color: #fff;
}

.login-to-buy {
    background: #666;
    color: #fff;
}

.view-all-products {
    display: block;
    text-align: center;
    padding: 10px;
    background: transparent;
    color: #000;
    border: 2px solid #000;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
}

/* ✅ UPDATED: AUTH BUTTONS STYLES */
.auth-buttons-container {
    text-align: center;
    margin-top: 25px;
}

.auth-btn-primary {
    display: inline-block;
    background: #fff;
    color: #000;
    padding: 12px 25px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.3s ease;
    border: 2px solid #fff;
    margin: 8px;
    font-size: 1.1em;
}

.auth-btn-secondary {
    display: inline-block;
    background: transparent;
    color: #fff;
    padding: 12px 25px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.3s ease;
    border: 2px solid #fff;
    margin: 8px;
    font-size: 1.1em;
}

.auth-btn-primary:hover, .auth-btn-secondary:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(255,255,255,0.3);
}

/* Responsive Design */
@media (max-width: 768px) {
    .simple-banner {
        height: 400px;
    }
    
    .simple-banner-text h2 {
        font-size: 1.8em;
    }
    
    .simple-banner-text p {
        font-size: 1em;
    }
    
    .simple-banner-text {
        padding: 20px;
        width: 90%;
    }
    
    .simple-banner-arrow {
        padding: 10px;
        width: 40px;
        height: 40px;
        font-size: 18px;
    }
    
    .modal-content {
        width: 95%;
        margin: 20px;
        padding: 20px;
    }
    
    .auth-btn-primary, .auth-btn-secondary {
        display: block;
        margin: 10px auto;
        width: 200px;
    }
}
</style>

<!-- WORKING BANNER SECTION -->
<section class="simple-banner">
    <div class="simple-slides" id="simpleBannerSlides">
        <!-- SLIDE 1 - banner1.png -->
        <div class="simple-slide">
            <img src="assets/images/banner1.png" alt="Welcome to ToyRex Corner">
            <div class="simple-banner-text">
                <h2>Welcome to ToyRex Corner! 🎮</h2>
                <p>Your ultimate destination for premium toy collections!</p>
                <a href="products.php" class="simple-banner-btn">Shop Now 🛒</a>
                
                <!-- ✅ UPDATED: AUTH BUTTONS IN BANNER -->
                <?php if(!isset($_SESSION['user_id'])): ?>
                    <div class="auth-buttons-container">
                        <a href="auth.php?tab=login" class="auth-btn-primary">🔐 Login to Account</a>
                        <a href="auth.php?tab=register" class="auth-btn-secondary">🚀 Create Account</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- SLIDE 2 - banner2.png -->
        <div class="simple-slide">
            <img src="assets/images/banner2.png" alt="Premium Collections">
            <div class="simple-banner-text">
                <h2>Premium Collections ✨</h2>
                <p>Discover exclusive figures and limited editions</p>
                <a href="products.php" class="simple-banner-btn">Explore Collections</a>
                
                <?php if(!isset($_SESSION['user_id'])): ?>
                    <div class="auth-buttons-container">
                        <a href="auth.php?tab=login" class="auth-btn-primary">🔐 Login to Account</a>
                        <a href="auth.php?tab=register" class="auth-btn-secondary">🚀 Create Account</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- SLIDE 3 - banner4.jpg -->
        <div class="simple-slide">
            <img src="assets/images/banner4.jpg" alt="Limited Editions">
            <div class="simple-banner-text">
                <h2>Limited Editions 🎯</h2>
                <p>Exclusive items that won't last long!</p>
                <a href="products.php" class="simple-banner-btn">Grab Yours</a>
                
                <?php if(!isset($_SESSION['user_id'])): ?>
                    <div class="auth-buttons-container">
                        <a href="auth.php?tab=login" class="auth-btn-primary">🔐 Login to Account</a>
                        <a href="auth.php?tab=register" class="auth-btn-secondary">🚀 Create Account</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- SLIDE 4 - banner7.jpg -->
        <div class="simple-slide">
            <img src="assets/images/banner7.jpg" alt="Special Offers">
            <div class="simple-banner-text">
                <h2>Special Offers 🔥</h2>
                <p>Limited time discounts and promotions</p>
                <a href="products.php" class="simple-banner-btn">View Deals</a>
                
                <?php if(!isset($_SESSION['user_id'])): ?>
                    <div class="auth-buttons-container">
                        <a href="auth.php?tab=login" class="auth-btn-primary">🔐 Login to Account</a>
                        <a href="auth.php?tab=register" class="auth-btn-secondary">🚀 Create Account</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Navigation Arrows -->
    <button class="simple-banner-arrow simple-prev-arrow" id="simplePrevBtn">❮</button>
    <button class="simple-banner-arrow simple-next-arrow" id="simpleNextBtn">❯</button>
    
    <!-- Navigation Dots -->
    <div class="simple-banner-dots" id="simpleBannerDots"></div>
</section>

<!-- WELCOME SECTION -->
<section class="welcome-section">
    <div class="container">
        <div class="welcome-content">
            <h2>Welcome to ToyRex Corner! 🎮</h2>
            <p>Your ultimate destination for premium toy collections! From action figures to adorable chibis, we bring your favorite characters to life. 🚀</p>
            
            <!-- ✅ UPDATED: ADDED AUTH BUTTONS IN WELCOME SECTION -->
            <?php if(!isset($_SESSION['user_id'])): ?>
                <div style="text-align: center; margin: 30px 0;">
                    <a href="auth.php?tab=login" class="auth-btn-primary" style="background: #000; color: #fff; border-color: #000;">🔐 Login to Account</a>
                    <a href="auth.php?tab=register" class="auth-btn-secondary" style="color: #000; border-color: #000;">🚀 Create Account</a>
                </div>
            <?php endif; ?>
            
            <div class="features">
                <div class="feature-item">
                    <div class="feature-icon">🛒</div>
                    <h3>Easy Ordering</h3>
                    <p>Simple and secure online ordering system</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">🚚</div>
                    <h3>Fast Delivery</h3>
                    <p>Nationwide shipping with real-time tracking</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">⭐</div>
                    <h3>Premium Quality</h3>
                    <p>100% authentic and high-quality products</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURED PRODUCTS SECTION -->
<section class="featured-products-section">
    <div class="container">
        <h2>Featured Products 🌟</h2>
        <p class="section-subtitle">Check out our hottest items this week!</p>
        
        <div class="featured-products-grid">
            <?php if($featured_products && count($featured_products) > 0): ?>
                <?php foreach($featured_products as $product): ?>
                <div class="featured-product-item">
                    <div class="product-image">
                        <?php 
                        $imageFile = getProductImage($product['name']);
                        $imagePath = "assets/images/" . $imageFile;
                        $imageExists = ($imageFile != 'default.jpg' && file_exists($imagePath));
                        ?>
                        
                        <div class="image-container">
                            <?php if($imageExists): ?>
                                <img src="<?php echo $imagePath; ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                     class="product-img">
                            <?php else: ?>
                                <div class="placeholder-image">
                                    <span><?php echo $product['name']; ?></span>
                                    <small>Featured Product</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-price">₱<?php echo number_format($product['price'], 2); ?></p>
                        <p class="product-stock">Only <?php echo $product['quantity']; ?> left!</p>
                        
                        <!-- FIXED: WORKING QUICK VIEW BUTTON -->
                        <button class="view-product-btn" 	
                                onclick="openProductModal(<?php echo htmlspecialchars(json_encode($product)); ?>, '<?php echo $imageExists ? $imagePath : ''; ?>')">
                            Quick View 👀
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-products">
                    <p>No featured products available yet. Check our <a href="products.php">products page</a> for more items!</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="view-all-container">
            <a href="products.php" class="view-all-btn">View All Products →</a>
        </div>
    </div>
</section>

<!-- PRODUCT DETAIL MODAL -->
<div class="modal-overlay" id="productModal">
    <div class="modal-content">
        <span class="close-modal" id="closeProduct">&times;</span>
        <div class="product-modal-content">
            <div class="modal-product-image">
                <div id="modalImageContainer">
                    <!-- Image will be inserted here by JavaScript -->
                </div>
            </div>
            <div class="modal-product-info">
                <h2 id="modalProductName"></h2>
                <p id="modalProductDescription"></p>
                <p class="modal-product-price" id="modalProductPrice"></p>
                <p class="modal-product-stock" id="modalProductStock"></p>
                <p class="modal-product-category" id="modalProductCategory"></p>
                
                <div class="modal-actions">
                    <?php if(isset($_SESSION['user_id']) && $_SESSION['user_type'] == 'client'): ?>
                        <button class="add-to-cart-modal" id="addToCartModal">Add to Cart 🛒</button>
                    <?php elseif(!isset($_SESSION['user_id'])): ?>
                        <!-- ✅ UPDATED: LINK TO AUTH.PHP INSTEAD OF MODAL -->
                        <a href="auth.php?tab=login" class="login-to-buy" style="display: block; text-decoration: none; text-align: center;">Login to Purchase 🔐</a>
                    <?php endif; ?>
                    <a href="products.php" class="view-all-products">View All Products</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SIMPLE BANNER JAVASCRIPT -->
<script>
// SIMPLE BANNER SLIDER - GUARANTEED TO WORK
class SimpleBannerSlider {
    constructor() {
        this.slides = document.getElementById('simpleBannerSlides');
        this.dotsContainer = document.getElementById('simpleBannerDots');
        this.prevBtn = document.getElementById('simplePrevBtn');
        this.nextBtn = document.getElementById('simpleNextBtn');
        this.currentSlide = 0;
        this.totalSlides = 0;
        this.slideInterval = null;
        
        console.log("🚀 Simple Banner Initializing...");
        this.init();
    }
    
    init() {
        if (!this.slides) {
            console.error("❌ Banner slides not found!");
            return;
        }
        
        this.totalSlides = this.slides.querySelectorAll('.simple-slide').length;
        console.log(`📊 Found ${this.totalSlides} slides`);
        
        if (this.totalSlides === 0) {
            console.error("❌ No slides found!");
            return;
        }
        
        this.createDots();
        this.setupEventListeners();
        this.startAutoSlide();
        this.updateSlider();
        
        console.log("✅ Simple Banner Ready!");
    }
    
    createDots() {
        if (!this.dotsContainer) return;
        
        this.dotsContainer.innerHTML = '';
        
        for (let i = 0; i < this.totalSlides; i++) {
            const dot = document.createElement('div');
            dot.className = `simple-dot ${i === 0 ? 'active' : ''}`;
            dot.setAttribute('data-slide', i);
            
            dot.addEventListener('click', () => {
                this.goToSlide(i);
            });
            
            this.dotsContainer.appendChild(dot);
        }
    }
    
    setupEventListeners() {
        if (this.prevBtn) {
            this.prevBtn.addEventListener('click', () => {
                this.prevSlide();
            });
        }
        
        if (this.nextBtn) {
            this.nextBtn.addEventListener('click', () => {
                this.nextSlide();
            });
        }
        
        // Pause on hover
        const banner = document.querySelector('.simple-banner');
        if (banner) {
            banner.addEventListener('mouseenter', () => {
                this.stopAutoSlide();
            });
            
            banner.addEventListener('mouseleave', () => {
                this.startAutoSlide();
            });
        }
    }
    
    goToSlide(slideIndex) {
        this.currentSlide = slideIndex;
        this.updateSlider();
        this.resetAutoSlide();
    }
    
    nextSlide() {
        this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
        this.updateSlider();
    }
    
    prevSlide() {
        this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
        this.updateSlider();
    }
    
    updateSlider() {
        const translateX = -(this.currentSlide * 100);
        if (this.slides) {
            this.slides.style.transform = `translateX(${translateX}%)`;
        }
        
        // Update dots
        const dots = document.querySelectorAll('.simple-dot');
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === this.currentSlide);
        });
    }
    
    startAutoSlide() {
        this.stopAutoSlide();
        this.slideInterval = setInterval(() => {
            this.nextSlide();
        }, 4000);
    }
    
    stopAutoSlide() {
        if (this.slideInterval) {
            clearInterval(this.slideInterval);
            this.slideInterval = null;
        }
    }
    
    resetAutoSlide() {
        this.stopAutoSlide();
        this.startAutoSlide();
    }
}

// PRODUCT MODAL FUNCTIONS - FIXED VERSION (NO EXTERNAL IMAGES)
function openProductModal(product, imagePath) {
    console.log("Opening product modal:", product);
    
    // Set modal content
    document.getElementById('modalProductName').textContent = product.name;
    document.getElementById('modalProductDescription').textContent = product.description;
    document.getElementById('modalProductPrice').textContent = '₱' + parseFloat(product.price).toFixed(2);
    document.getElementById('modalProductStock').textContent = 'Stock: ' + product.quantity + ' left';
    document.getElementById('modalProductCategory').textContent = 'Category: ' + product.category;
    
    // Set product image - FIXED: No external images
    const imageContainer = document.getElementById('modalImageContainer');
    if (imagePath && imagePath !== '') {
        imageContainer.innerHTML = `<img src="${imagePath}" alt="${product.name}" style="max-width: 100%; max-height: 300px; border-radius: 10px;">`;
    } else {
        imageContainer.innerHTML = `
            <div class="modal-product-placeholder">
                ${product.name}<br>
                <small>Image Not Available</small>
            </div>
        `;
    }
    
    // Set add to cart button data
    const addToCartBtn = document.getElementById('addToCartModal');
    if (addToCartBtn) {
        addToCartBtn.setAttribute('data-product-id', product.id);
        addToCartBtn.setAttribute('data-product-name', product.name);
    }
    
    // Show modal
    document.getElementById('productModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log("🎯 Page loaded, starting banner...");
    new SimpleBannerSlider();
    
    // Close product modal
    document.getElementById('closeProduct')?.addEventListener('click', function() {
        document.getElementById('productModal').classList.remove('active');
        document.body.style.overflow = 'auto';
    });
    
    // Add to cart functionality
    document.getElementById('addToCartModal')?.addEventListener('click', function() {
        const productId = this.getAttribute('data-product-id');
        const productName = this.getAttribute('data-product-name');
        
        alert('Added to cart: ' + productName);
        document.getElementById('productModal').classList.remove('active');
        document.body.style.overflow = 'auto';
    });
    
    // Close modal when clicking outside
    document.getElementById('productModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
