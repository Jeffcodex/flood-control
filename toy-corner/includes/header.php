<?php

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

/* ADD THESE STYLES TO YOUR assets/css/style.css */

/* LOGOUT BUTTON STYLES */
.logout-btn {
    background: #dc3545 !important;
    color: white !important;
    padding: 10px 20px !important;
    border-radius: 6px !important;
    text-decoration: none !important;
    font-weight: 500 !important;
    transition: all 0.3s ease !important;
    border: 2px solid #dc3545 !important;
    font-size: 0.9em !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
}

.logout-btn:hover {
    background: #c82333 !important;
    border-color: #c82333 !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3) !important;
}

/* AUTH BUTTONS STYLES */
.auth-buttons {
    display: flex;
    gap: 12px;
    margin-left: 15px;
}

.login-btn, .register-btn {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.85em;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.login-btn {
    background: transparent;
    color: var(--pure-white);
    border: 2px solid var(--pure-white);
}

.login-btn:hover {
    background: var(--pure-white);
    color: var(--pure-black);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255,255,255,0.2);
}

.register-btn {
    background: var(--pure-white);
    color: var(--pure-black);
    border: 2px solid var(--pure-white);
}

.register-btn:hover {
    background: var(--pure-black);
    color: var(--pure-white);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255,255,255,0.2);
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
                        <a href="client.php" class="<?php echo $current_page == 'client.php' ? 'active' : ''; ?>">Client Dashboard</a>
                    <?php endif; ?>
                    <a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a>
                <?php else: ?>
                    <div class="auth-buttons">
                        <button class="login-btn" id="loginBtn">Login</button>
                        <button class="register-btn" id="registerBtn">Register</button>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="menu-toggle" id="menuToggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <!-- LOGIN MODAL -->
    <div class="modal-overlay" id="loginModal">
        <div class="modal-content">
            <span class="close-modal" id="closeLogin">&times;</span>
            <h2>Member Login 🔐</h2>
            <form class="modal-form" action="includes/login.php" method="POST">
                <input type="text" name="username" placeholder="Username or Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login to Account</button>
            </form>
            <div class="modal-switch">
                <p>Don't have an account? <a href="#" id="showRegister">Register here</a></p>
            </div>
        </div>
    </div>

    <!-- REGISTER MODAL -->
    <div class="modal-overlay" id="registerModal">
        <div class="modal-content">
            <span class="close-modal" id="closeRegister">&times;</span>
            <h2>Join Our Community! 🌟</h2>
            <form class="modal-form" action="includes/register.php" method="POST" enctype="multipart/form-data">
                <input type="text" name="full_name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email Address" required>
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password (min. 6 characters)" required minlength="6">
                <input type="text" name="address" placeholder="Address (Optional)">
                <input type="text" name="phone" placeholder="Phone Number (Optional)">
                <input type="file" name="profile_picture" accept="image/*">
                <button type="submit">Create Account</button>
            </form>
            <div class="modal-switch">
                <p>Already have an account? <a href="#" id="showLogin">Login here</a></p>
            </div>
        </div>
    </div>

    <!-- SIMPLE JAVASCRIPT FOR MODALS -->
    <script>
    // WAIT FOR PAGE TO LOAD
    document.addEventListener('DOMContentLoaded', function() {
        console.log("🚀 Page loaded - Initializing modals...");
        
        // GET ALL ELEMENTS
        const loginBtn = document.getElementById('loginBtn');
        const registerBtn = document.getElementById('registerBtn');
        const loginModal = document.getElementById('loginModal');
        const registerModal = document.getElementById('registerModal');
        const closeLogin = document.getElementById('closeLogin');
        const closeRegister = document.getElementById('closeRegister');
        const showRegister = document.getElementById('showRegister');
        const showLogin = document.getElementById('showLogin');
        
        console.log("Login Button:", loginBtn);
        console.log("Register Button:", registerBtn);
        console.log("Login Modal:", loginModal);
        console.log("Register Modal:", registerModal);
        
        // SHOW LOGIN MODAL
        if (loginBtn) {
            loginBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log("📱 Login button clicked!");
                if (loginModal) {
                    loginModal.classList.add('active');
                    document.body.style.overflow = 'hidden';
                    console.log("✅ Login modal shown!");
                }
            });
        } else {
            console.log("❌ Login button not found!");
        }
        
        // SHOW REGISTER MODAL
        if (registerBtn) {
            registerBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log("📱 Register button clicked!");
                if (registerModal) {
                    registerModal.classList.add('active');
                    document.body.style.overflow = 'hidden';
                    console.log("✅ Register modal shown!");
                }
            });
        } else {
            console.log("❌ Register button not found!");
        }
        
        // CLOSE LOGIN MODAL
        if (closeLogin) {
            closeLogin.addEventListener('click', function() {
                console.log("❌ Close login clicked!");
                if (loginModal) {
                    loginModal.classList.remove('active');
                    document.body.style.overflow = 'auto';
                }
            });
        }
        
        // CLOSE REGISTER MODAL
        if (closeRegister) {
            closeRegister.addEventListener('click', function() {
                console.log("❌ Close register clicked!");
                if (registerModal) {
                    registerModal.classList.remove('active');
                    document.body.style.overflow = 'auto';
                }
            });
        }
        
        // SWITCH TO REGISTER MODAL
        if (showRegister) {
            showRegister.addEventListener('click', function(e) {
                e.preventDefault();
                console.log("🔄 Switching to register modal!");
                if (loginModal && registerModal) {
                    loginModal.classList.remove('active');
                    registerModal.classList.add('active');
                }
            });
        }
        
        // SWITCH TO LOGIN MODAL
        if (showLogin) {
            showLogin.addEventListener('click', function(e) {
                e.preventDefault();
                console.log("🔄 Switching to login modal!");
                if (registerModal && loginModal) {
                    registerModal.classList.remove('active');
                    loginModal.classList.add('active');
                }
            });
        }
        
        // CLOSE MODALS WHEN CLICKING OUTSIDE
        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    console.log("🎯 Clicked outside modal - closing!");
                    this.classList.remove('active');
                    document.body.style.overflow = 'auto';
                }
            });
        });
        
        // CLOSE MODALS WITH ESCAPE KEY
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                console.log("⌨️ Escape key pressed - closing modals!");
                document.querySelectorAll('.modal-overlay').forEach(modal => {
                    modal.classList.remove('active');
                });
                document.body.style.overflow = 'auto';
            }
        });
        
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
