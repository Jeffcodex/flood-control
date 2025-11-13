<?php 
// FIXED INCLUDES
if(file_exists('includes/header.php')) {
    include 'includes/header.php';
} elseif(file_exists('header.php')) {
    include 'header.php';
} else {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>AboutUs - ToyRex Corner</title>
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
    <body>
    <nav style="background: #000; color: white; padding: 15px; text-align: center;">
        <h2>ToyRex Corner</h2>
        <a href="index.php" style="color: white; margin: 0 10px;">Home</a>
        <a href="about.php" style="color: white; margin: 0 10px;">About</a>
        <a href="products.php" style="color: white; margin: 0 10px;">Products</a>
    </nav>
    <?php
}
?>

<section class="about-section">
    <div class="container">
        <h2>About ToyRex Corner</h2>
        <div class="about-content">
            <!-- ABOUT TEXT CONTENT -->
            <div class="about-text">
                <h3>Welcome to ToyRex Corner 🎮</h3>
                <p>At ToyRex Corner, we're passionate about bringing joy to collectors and fans of all ages. From detailed action figures to cute chibi collectibles, our store is your go-to destination for high-quality toys and model kits inspired by your favorite anime, games, and movies.</p>
                
                <h3>Our Mission 🎯</h3>
                <p>To spark happiness and creativity by providing high-quality collectibles and toys that bring imagination to life. We aim to build a fun and friendly community for collectors, fans, and dreamers.</p>

                <h3>Our Vision 🌟</h3>
                <p>We envision ToyRex Corner as the most trusted and loved destination for toy enthusiasts — a place where innovation meets nostalgia, and where every visit inspires joy, creativity, and connection through the world of collectibles.</p>

               
            </div>

            <!-- IMAGE SLIDER -->
            <div class="about-slider">
                <div class="container4">
                    <div class="images1 active">
                        <img src="assets/images/banner1.png" alt="Toy Collection 1" 
                             onerror="this.src='https://via.placeholder.com/400x300/000/fff?text=ToyRex+Corner'">
                    </div>
                    <div class="images1">
                        <img src="assets/images/banner2.png" alt="Toy Collection 2"
                             onerror="this.src='https://via.placeholder.com/400x300/333/fff?text=Premium+Toys'">
                    </div>
                    <div class="images1">
                        <img src="assets/images/banner4.jpg" alt="Toy Collection 3"
                             onerror="this.src='https://via.placeholder.com/400x300/666/fff?text=Gundam+Collection'">
                    </div>
                    <div class="images1">
                        <img src="assets/images/banner7.jpg" alt="Toy Collection 4"
                             onerror="this.src='https://via.placeholder.com/400x300/999/fff?text=Special+Offers'">
                    </div>
                </div>
                
                <!-- SLIDER NAVIGATION DOTS -->
                <div class="slider-nav">
                    <button class="slider-dot active" data-slide="0"></button>
                    <button class="slider-dot" data-slide="1"></button>
                    <button class="slider-dot" data-slide="2"></button>
                    <button class="slider-dot" data-slide="3"></button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ENHANCED CSS STYLES -->
<style>
.about-section {
    padding: 80px 0;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    min-height: 80vh;
}

.about-section h2 {
    text-align: center;
    font-size: 2.8em;
    margin-bottom: 50px;
    color: #000;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.about-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 50px;
    align-items: start;
    max-width: 1200px;
    margin: 0 auto;
}

/* ABOUT TEXT STYLES */
.about-text h3 {
    color: #000;
    margin: 25px 0 15px;
    font-size: 1.6em;
    display: flex;
    align-items: center;
    gap: 10px;
}

.about-text h3:first-child {
    margin-top: 0;
}

.about-text p {
    color: #555;
    line-height: 1.7;
    margin-bottom: 20px;
    font-size: 1.05em;
}

/* FEATURES LIST */
.features-list {
    margin-top: 30px;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.feature-item:hover {
    transform: translateX(5px);
}

.feature-icon {
    font-size: 2em;
    width: 50px;
    text-align: center;
}

.feature-text h4 {
    color: #000;
    margin-bottom: 5px;
    font-size: 1.1em;
}

.feature-text p {
    color: #666;
    margin: 0;
    font-size: 0.9em;
}

/* SLIDER STYLES */
.about-slider {
    position: sticky;
    top: 100px;
}

.container4 {
    position: relative;
    width: 100%;
    height: 400px;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.images1 {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    transition: opacity 0.8s ease-in-out;
}

.images1.active {
    opacity: 1;
}

.images1 img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

/* SLIDER NAVIGATION */
.slider-nav {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 20px;
}

.slider-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #000;
    background: transparent;
    cursor: pointer;
    transition: all 0.3s ease;
}

.slider-dot.active {
    background: #000;
    transform: scale(1.2);
}

.slider-dot:hover {
    background: #333;
}

/* RESPONSIVE DESIGN */
@media (max-width: 968px) {
    .about-content {
        grid-template-columns: 1fr;
        gap: 40px;
    }
    
    .about-slider {
        order: -1;
        position: static;
    }
    
    .container4 {
        height: 300px;
    }
}

@media (max-width: 768px) {
    .about-section h2 {
        font-size: 2.2em;
    }
    
    .about-text h3 {
        font-size: 1.4em;
    }
    
    .feature-item {
        flex-direction: column;
        text-align: center;
        gap: 10px;
    }
    
    .feature-icon {
        font-size: 1.8em;
    }
}

/* ANIMATIONS */
.about-text h3 {
    animation: fadeInUp 0.6s ease forwards;
}

.about-text p {
    animation: fadeInUp 0.6s ease 0.2s forwards;
    opacity: 0;
    transform: translateY(20px);
}

.feature-item {
    animation: fadeInUp 0.6s ease forwards;
    opacity: 0;
    transform: translateY(20px);
}

.feature-item:nth-child(1) { animation-delay: 0.4s; }
.feature-item:nth-child(2) { animation-delay: 0.5s; }
.feature-item:nth-child(3) { animation-delay: 0.6s; }

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<!-- ENHANCED JAVASCRIPT -->
<script>
// SLIDER FUNCTIONALITY
const slides2 = document.querySelectorAll('.images1');
const dots = document.querySelectorAll('.slider-dot');
let index2 = 0;
let slideInterval;

function showSlide() {
    // Remove active class from all slides and dots
    slides2.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));
    
    // Add active class to current slide and dot
    slides2[index2].classList.add('active');
    dots[index2].classList.add('active');
    
    // Move to next slide
    index2 = (index2 + 1) % slides2.length;
}

// MANUAL SLIDE CONTROL
dots.forEach((dot, i) => {
    dot.addEventListener('click', () => {
        // Stop auto-slide when manually navigating
        clearInterval(slideInterval);
        
        // Update index and show selected slide
        index2 = i;
        showSlide();
        
        // Restart auto-slide after 5 seconds
        setTimeout(startAutoSlide, 5000);
    });
});

function startAutoSlide() {
    slideInterval = setInterval(showSlide, 3000);
}

// Initialize slider
document.addEventListener('DOMContentLoaded', function() {
    showSlide();
    startAutoSlide();
    
    // Pause slider on hover
    const container4 = document.querySelector('.container4');
    container4.addEventListener('mouseenter', () => {
        clearInterval(slideInterval);
    });
    
    container4.addEventListener('mouseleave', () => {
        startAutoSlide();
    });
});
</script>

<?php 
// FIXED FOOTER INCLUDE
if(file_exists('includes/footer.php')) {
    include 'includes/footer.php';
} elseif(file_exists('footer.php')) {
    include 'footer.php';
} else {
    ?>
    <footer style="background: #000; color: white; text-align: center; padding: 20px; margin-top: 50px;">
        <p>&copy; 2024 ToyRex Corner. All rights reserved.</p>
    </footer>
    </body>
    </html>
    <?php
}
?>