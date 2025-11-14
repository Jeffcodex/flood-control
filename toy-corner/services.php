<?php include 'includes/header.php'; ?>

<section class="services-section" style="padding: 100px 20px; background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); min-height: 80vh;">
    <div class="container" style="max-width: 1200px; margin: 0 auto;">
        <h2 style="text-align: center; font-size: 2.8em; margin-bottom: 20px; color: #000; text-transform: uppercase; letter-spacing: 2px;">Our Services</h2>
        <p class="section-subtitle" style="text-align: center; color: #666; font-size: 1.2em; margin-bottom: 60px;">
            Comprehensive solutions for toy enthusiasts
        </p>
        
        <div class="services-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
            <!-- SERVICE 1 WITH HOVER ANIMATIONS -->
            <div class="service-item" style="background: white; padding: 40px 30px; border-radius: 20px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 2px solid #000; transition: all 0.4s ease; position: relative; overflow: hidden;">
                <div class="service-icon" style="font-size: 4em; margin-bottom: 25px; transition: all 0.3s ease;">🛒</div>
                <h3 style="color: #000; margin-bottom: 20px; font-size: 1.5em; text-transform: uppercase; letter-spacing: 1px;">Online Ordering System</h3>
                <p style="color: #666; line-height: 1.7; font-size: 1.05em;">Easy-to-use platform for browsing and purchasing toys with secure payment options and real-time inventory updates.</p>
                
                <!-- Hover Overlay Effect -->
                <div style="position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(0,0,0,0.03), transparent); transition: left 0.6s ease;"></div>
            </div>
            
            <!-- SERVICE 2 WITH HOVER ANIMATIONS -->
            <div class="service-item" style="background: white; padding: 40px 30px; border-radius: 20px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 2px solid #000; transition: all 0.4s ease; position: relative; overflow: hidden;">
                <div class="service-icon" style="font-size: 4em; margin-bottom: 25px; transition: all 0.3s ease;">🚚</div>
                <h3 style="color: #000; margin-bottom: 20px; font-size: 1.5em; text-transform: uppercase; letter-spacing: 1px;">Nationwide Delivery</h3>
                <p style="color: #666; line-height: 1.7; font-size: 1.05em;">Fast and reliable shipping across the Philippines with secure packaging and order tracking included.</p>
                
                <!-- Hover Overlay Effect -->
                <div style="position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(0,0,0,0.03), transparent); transition: left 0.6s ease;"></div>
            </div>
            
            <!-- SERVICE 3 WITH HOVER ANIMATIONS -->
            <div class="service-item" style="background: white; padding: 40px 30px; border-radius: 20px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 2px solid #000; transition: all 0.4s ease; position: relative; overflow: hidden;">
                <div class="service-icon" style="font-size: 4em; margin-bottom: 25px; transition: all 0.3s ease;">🔧</div>
                <h3 style="color: #000; margin-bottom: 20px; font-size: 1.5em; text-transform: uppercase; letter-spacing: 1px;">Builder Support</h3>
                <p style="color: #666; line-height: 1.7; font-size: 1.05em;">Expert guidance for assembly, painting, and customization from experienced model builders.</p>
                
                <!-- Hover Overlay Effect -->
                <div style="position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(0,0,0,0.03), transparent); transition: left 0.6s ease;"></div>
            </div>
        </div>
    </div>
</section>

<!-- HOVER ANIMATIONS SCRIPT -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add hover effects to service items
        const serviceItems = document.querySelectorAll('.service-item');
        
        serviceItems.forEach(item => {
            // Mouse Enter - Hover In
            item.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-15px) scale(1.02)';
                this.style.boxShadow = '0 20px 40px rgba(0,0,0,0.2)';
                this.style.borderColor = '#333';
                
                // Animate the icon
                const icon = this.querySelector('.service-icon');
                if (icon) {
                    icon.style.transform = 'scale(1.1) rotate(5deg)';
                }
                
                // Animate the overlay
                const overlay = this.querySelector('div:last-child');
                if (overlay) {
                    overlay.style.left = '100%';
                }
            });
            
            // Mouse Leave - Hover Out
            item.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
                this.style.boxShadow = '0 10px 30px rgba(0,0,0,0.1)';
                this.style.borderColor = '#000';
                
                // Reset the icon
                const icon = this.querySelector('.service-icon');
                if (icon) {
                    icon.style.transform = 'scale(1) rotate(0)';
                }
                
                // Reset the overlay
                const overlay = this.querySelector('div:last-child');
                if (overlay) {
                    overlay.style.left = '-100%';
                }
            });
        });
        
        // Add click effects to buttons
        const buttons = document.querySelectorAll('button, .service-item');
        buttons.forEach(button => {
            button.addEventListener('mousedown', function() {
                this.style.transform = 'scale(0.95)';
            });
            
            button.addEventListener('mouseup', function() {
                this.style.transform = '';
            });
            
            button.addEventListener('mouseleave', function() {
                this.style.transform = '';
            });
        });
    });
</script>

<?php include 'includes/footer.php'; ?>
