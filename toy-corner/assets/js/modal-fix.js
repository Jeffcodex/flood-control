// assets/js/modal-fix.js - ULTIMATE MODAL FIX
console.log("🚀 ULTIMATE MODAL FIX LOADED!");

// SIMPLE MODAL SYSTEM - NO CONFLICTS!
function showModal(modalId) {
    console.log("📱 Showing modal:", modalId);
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        console.log("✅ Modal shown successfully!");
    } else {
        console.log("❌ Modal not found:", modalId);
    }
}

function hideModal(modalId) {
    console.log("❌ Hiding modal:", modalId);
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

function hideAllModals() {
    console.log("🎯 Hiding all modals");
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.style.display = 'none';
    });
    document.body.style.overflow = 'auto';
}

// WAIT FOR PAGE TO LOAD
document.addEventListener('DOMContentLoaded', function() {
    console.log("🎯 Page loaded - Ultimate Modal Fix Active!");
    
    // DEBUG: Check if buttons exist
    const loginBtn = document.getElementById('loginBtn');
    const registerBtn = document.getElementById('registerBtn');
    
    console.log("🔍 Button Check:", {
        loginBtn: loginBtn ? "✅ FOUND" : "❌ MISSING",
        registerBtn: registerBtn ? "✅ FOUND" : "❌ MISSING"
    });
    
    // ULTIMATE BUTTON FIX - DIRECT EVENT ASSIGNMENT
    if (loginBtn) {
        loginBtn.onclick = function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log("🖱️ LOGIN BUTTON CLICKED!");
            showModal('loginModal');
        };
    }
    
    if (registerBtn) {
        registerBtn.onclick = function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log("🖱️ REGISTER BUTTON CLICKED!");
            showModal('registerModal');
        };
    }
    
    // CLOSE BUTTONS
    document.querySelectorAll('.close-modal').forEach(btn => {
        btn.onclick = function() {
            const modal = this.closest('.modal-overlay');
            if (modal) {
                hideModal(modal.id);
            }
        };
    });
    
    // CLOSE ON OUTSIDE CLICK
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.onclick = function(e) {
            if (e.target === this) {
                hideModal(this.id);
            }
        };
    });
    
    // CLOSE WITH ESCAPE KEY
    document.onkeydown = function(e) {
        if (e.key === 'Escape') {
            hideAllModals();
        }
    };
    
    // SWITCH BETWEEN LOGIN/REGISTER
    document.getElementById('showRegister')?.onclick = function(e) {
        e.preventDefault();
        hideModal('loginModal');
        showModal('registerModal');
    };
    
    document.getElementById('showLogin')?.onclick = function(e) {
        e.preventDefault();
        hideModal('registerModal');
        showModal('loginModal');
    };
    
    // MOBILE MENU
    const menuToggle = document.getElementById('menuToggle');
    const navLinks = document.getElementById('navLinks');
    
    if (menuToggle && navLinks) {
        menuToggle.onclick = function() {
            navLinks.classList.toggle('active');
            menuToggle.classList.toggle('active');
        };
    }
    
    // AUTO-TEST: Show login modal after 2 seconds
    setTimeout(() => {
        console.log("🧪 AUTO-TEST: Showing login modal...");
        showModal('loginModal');
    }, 2000);
});

// PRODUCT MODAL FUNCTIONS
function openProductModal(product, imagePath) {
    console.log("Opening product modal:", product);
    
    // Set product details
    document.getElementById('modalProductName').textContent = product.name;
    document.getElementById('modalProductDescription').textContent = product.description;
    document.getElementById('modalProductPrice').textContent = '₱' + parseFloat(product.price).toFixed(2);
    document.getElementById('modalProductStock').textContent = 'Stock: ' + product.quantity + ' left';
    document.getElementById('modalProductCategory').textContent = 'Category: ' + product.category;
    
    // Set product image
    const imageContainer = document.getElementById('modalImageContainer');
    if (imagePath && imagePath !== '') {
        imageContainer.innerHTML = `<img src="${imagePath}" alt="${product.name}" style="max-width: 100%; max-height: 300px; border-radius: 10px;">`;
    } else {
        imageContainer.innerHTML = `
            <div style="width:100%;height:200px;background:#f0f0f0;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#666;font-weight:bold;border:2px dashed #ccc;">
                ${product.name}<br>
                <small>Image Not Available</small>
            </div>
        `;
    }
    
    showModal('productModal');
}