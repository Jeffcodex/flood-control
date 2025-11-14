<?php
session_start();

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

if (!function_exists('getLogoImage')) {
    function getLogoImage($logoName) {
        return getImagePath($logoName);
    }
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToyRex Corner - Premium Toy Collections</title>
    <link rel="icon" type="image/x-icon" href="assets/images/<?php echo getLogoImage('logo1'); ?>">
	
    <link rel="stylesheet" href="assets/css/style.css">
	
	<!-- MODAL STYLES -->
	<style>
	.modal-overlay {
		display: none;
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background: rgba(0,0,0,0.8);
		justify-content: center;
		align-items: center;
		z-index: 9999;
	}

	.modal-overlay.active {
		display: flex !important;
		animation: fadeIn 0.3s ease;
	}

	@keyframes fadeIn {
		from { opacity: 0; }
		to { opacity: 1; }
	}

	.modal-content {
		background: #fff;
		padding: 40px;
		border-radius: 15px;
		width: 90%;
		max-width: 400px;
		position: relative;
		box-shadow: 0 10px 30px rgba(0,0,0,0.5);
		border: 2px solid #000;
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
		transition: all 0.3s ease;
	}

	.close-modal:hover {
		transform: scale(1.2);
		color: #666;
	}

	.modal-content h2 {
		text-align: center;
		color: #000;
		margin-bottom: 25px;
		font-size: 1.8em;
	}

	.modal-form {
		display: flex;
		flex-direction: column;
		gap: 15px;
	}

	.modal-form input {
		width: 100%;
		padding: 12px 15px;
		border: 2px solid #ddd;
		border-radius: 8px;
		font-size: 1em;
		transition: all 0.3s ease;
		box-sizing: border-box;
	}

	.modal-form input:focus {
		border-color: #000;
		outline: none;
		box-shadow: 0 0 0 3px rgba(0,0,0,0.1);
	}

	.modal-form button {
		background: #000;
		color: #fff;
		border: 2px solid #000;
		padding: 12px;
		border-radius: 8px;
		font-size: 1.1em;
		font-weight: bold;
		cursor: pointer;
		transition: all 0.3s ease;
		margin-top: 10px;
	}

	.modal-form button:hover {
		background: #fff;
		color: #000;
		transform: translateY(-2px);
	}

	.modal-switch {
		text-align: center;
		margin-top: 20px;
		padding-top: 20px;
		border-top: 1px solid #eee;
	}

	.modal-switch a {
		color: #000;
		text-decoration: none;
		font-weight: bold;
	}

	.modal-switch a:hover {
		text-decoration: underline;
	}

	/* Register form specific */
	#registerModal .modal-content {
		max-width: 450px;
	}

	#registerModal input[type="file"] {
		padding: 8px;
		border: 2px dashed #ddd;
		background: #f9f9f9;
	}

	#registerModal input[type="file"]:focus {
		border-color: #000;
		background: #fff;
	}
	</style>
	<style>
/* RESET MODAL STYLES - OVERRIDE EVERYTHING */
.modal-overlay {
    display: none !important;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    background: rgba(0,0,0,0.9) !important;
    justify-content: center !important;
    align-items: center !important;
    z-index: 99999 !important;
}

.modal-overlay.active {
    display: flex !important !important;
}

.modal-content {
    background: white !important;
    padding: 40px !important;
    border-radius: 15px !important;
    width: 90% !important;
    max-width: 400px !important;
    position: relative !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5) !important;
    border: 3px solid black !important;
    z-index: 100000 !important;
}

.close-modal {
    position: absolute !important;
    top: 15px !important;
    right: 20px !important;
    font-size: 30px !important;
    cursor: pointer !important;
    color: black !important;
    background: none !important;
    border: none !important;
}

/* HIDE OTHER MODALS THAT MIGHT BE CONFLICTING */
.modal, .popup, .overlay {
    display: none !important;
}
</style>
</head>
<body>
    <nav>
        <div class="nav-container">
            <div class="logo">
                <?php $logo = getLogoImage('logo'); ?>
                <?php if($logo != 'default.jpg'): ?>
                    <img src="assets/images/<?php echo $logo; ?>" alt="ToyRex Corner Logo" 
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                <?php endif; ?>
                <div class="logo-text">
                    <span class="logo-main">TOYREX</span>
                    <span class="logo-sub">CORNER</span>
                </div>
            </div>
            
            <div class="nav-links" id="navLinks">
                <a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Home</a>
                <a href="about.php" class="<?php echo $current_page == 'about.php' ? 'active' : ''; ?>">About Us</a>
                <a href="services.php" class="<?php echo $current_page == 'services.php' ? 'active' : ''; ?>">Services</a>
                <a href="products.php" class="<?php echo $current_page == 'products.php' ? 'active' : ''; ?>">Products</a>
                <a href="contact.php" class="<?php echo $current_page == 'contact.php' ? 'active' : ''; ?>">Contact Us</a>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <?php if($_SESSION['user_type'] == 'admin'): ?>
                        <a href="admin.php" class="<?php echo $current_page == 'admin.php' ? 'active' : ''; ?>">Admin Dashboard</a>
                    <?php else: ?>
                        <a href="client.php" class="<?php echo $current_page == 'client.php' ? 'active' : ''; ?>">My Account</a>
                    <?php endif; ?>
                    <a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a>
                <?php else: ?>
                    <!-- ✅ UPDATED: SIMPLE LINKS TO AUTH.PHP -->
                    <a href="auth.php?tab=login" class="<?php echo $current_page == 'auth.php' ? 'active' : ''; ?>">🔐 Login</a>
                    <a href="auth.php?tab=register" class="<?php echo $current_page == 'auth.php' ? 'active' : ''; ?>">🚀 Register</a>
                <?php endif; ?>
            </div>
            
            <div class="menu-toggle" id="menuToggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <!-- ✅ UPDATED: REMOVED MODALS SINCE MAY BAGONG AUTH.PHP NA -->
    
    <!-- SIMPLE JAVASCRIPT FOR MOBILE MENU -->
    <script>
    // WAIT FOR PAGE TO LOAD
    document.addEventListener('DOMContentLoaded', function() {
        console.log("🚀 Page loaded - Header initialized");
        
        // MOBILE MENU FUNCTIONALITY
        const menuToggle = document.getElementById('menuToggle');
        const navLinks = document.getElementById('navLinks');
        
        if(menuToggle && navLinks) {
            menuToggle.addEventListener('click', function() {
                navLinks.classList.toggle('active');
                menuToggle.classList.toggle('active');
            });
        }
        
        // CLOSE MOBILE MENU WHEN CLICKING ON LINK
        const navLinksList = document.querySelectorAll('.nav-links a');
        navLinksList.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    if(navLinks) navLinks.classList.remove('active');
                    if(menuToggle) menuToggle.classList.remove('active');
                }
            });
        });
    });
    </script>
