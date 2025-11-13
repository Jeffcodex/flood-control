<?php 
include 'includes/header.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    // Basic validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    
    if (empty($subject)) {
        $errors[] = "Subject is required";
    }
    
    if (empty($message)) {
        $errors[] = "Message is required";
    }
    
    if (empty($errors)) {
        // Here you can:
        // 1. Save to database (create contacts table)
        // 2. Send email notification
        // 3. Save to file
        // For now, we'll show success message
        
        $success_message = "Thank you, $name! Your message has been sent successfully. We'll get back to you within 24 hours. 📧";
    }
}
?>

<!-- CONTACT PAGE STYLES -->
<style>
.contact-section {
    padding: 100px 0 60px;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    min-height: 100vh;
}

.contact-section h2 {
    text-align: center;
    font-size: 2.8em;
    margin-bottom: 15px;
    color: #000;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.section-subtitle {
    text-align: center;
    color: #666;
    font-size: 1.2em;
    margin-bottom: 50px;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.contact-content {
    display: grid;
    grid-template-columns: 1fr 1.2fr;
    gap: 50px;
    max-width: 1200px;
    margin: 0 auto;
    align-items: start;
}

/* CONTACT INFO STYLES */
.contact-info {
    background: #fff;
    padding: 40px 30px;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    border: 2px solid #000;
}

.contact-info-item {
    display: flex;
    align-items: flex-start;
    gap: 20px;
    margin-bottom: 30px;
    padding-bottom: 25px;
    border-bottom: 1px solid #eee;
}

.contact-info-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.contact-icon {
    font-size: 2em;
    width: 50px;
    text-align: center;
    flex-shrink: 0;
}

.contact-text h3 {
    color: #000;
    margin-bottom: 10px;
    font-size: 1.3em;
}

.contact-text p {
    color: #666;
    margin-bottom: 5px;
    line-height: 1.5;
}

.contact-text strong {
    color: #000;
}

/* SOCIAL LINKS */
.social-links-contact {
    margin-top: 30px;
    padding-top: 25px;
    border-top: 2px solid #000;
}

.social-links-contact h3 {
    color: #000;
    margin-bottom: 15px;
    font-size: 1.3em;
}

.social-icons {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.social-icon {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 15px;
    background: #000;
    color: #fff;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
    font-weight: 500;
}

.social-icon:hover {
    background: #333;
    transform: translateY(-2px);
}

/* CONTACT FORM STYLES */
.contact-form-container {
    background: #fff;
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    border: 2px solid #000;
}

.form-header {
    text-align: center;
    margin-bottom: 30px;
}

.form-header h3 {
    color: #000;
    font-size: 1.8em;
    margin-bottom: 10px;
}

.form-header p {
    color: #666;
    font-size: 1.1em;
}

/* FORM STYLES */
.contact-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-group {
    position: relative;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #000;
    font-weight: 600;
    font-size: 1em;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 15px;
    border: 2px solid #ddd;
    border-radius: 10px;
    font-size: 1em;
    transition: all 0.3s ease;
    background: #fff;
    color: #000;
    font-family: inherit;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: #000;
    outline: none;
    box-shadow: 0 0 0 3px rgba(0,0,0,0.1);
}

.form-group.focused label {
    color: #000;
}

.form-group textarea {
    resize: vertical;
    min-height: 120px;
}

/* SUBMIT BUTTON */
.submit-btn {
    background: #000;
    color: #fff;
    border: 2px solid #000;
    padding: 16px 30px;
    border-radius: 50px;
    font-size: 1.1em;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-top: 10px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.submit-btn:hover {
    background: #fff;
    color: #000;
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}

/* MESSAGES STYLES */
.error-message {
    background: #fee;
    border: 2px solid #fcc;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 25px;
}

.error-message h4 {
    color: #c00;
    margin-bottom: 10px;
    font-size: 1.1em;
}

.error-message ul {
    color: #c00;
    margin: 0;
    padding-left: 20px;
}

.error-message li {
    margin-bottom: 5px;
}

.success-message {
    background: #efe;
    border: 2px solid #cfc;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 25px;
    text-align: center;
}

.success-message h4 {
    color: #080;
    margin-bottom: 10px;
    font-size: 1.3em;
}

.success-message p {
    color: #080;
    margin: 0;
    font-size: 1.1em;
}

/* MAP SECTION */
.map-section {
    margin-top: 60px;
    text-align: center;
}

.map-section h3 {
    color: #000;
    font-size: 2em;
    margin-bottom: 30px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.map-placeholder {
    background: #fff;
    border: 2px solid #000;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.map-content {
    text-align: center;
}

.map-icon {
    font-size: 4em;
    margin-bottom: 20px;
}

.map-content h4 {
    color: #000;
    font-size: 1.5em;
    margin-bottom: 15px;
}

.map-content p {
    color: #666;
    margin-bottom: 10px;
    font-size: 1.1em;
}

.map-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 25px;
    flex-wrap: wrap;
}

.map-btn {
    background: #000;
    color: #fff;
    border: 2px solid #000;
    padding: 12px 25px;
    border-radius: 50px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.map-btn:hover {
    background: #fff;
    color: #000;
    transform: translateY(-2px);
}

/* RESPONSIVE DESIGN */
@media (max-width: 968px) {
    .contact-content {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .contact-section {
        padding: 80px 0 40px;
    }
    
    .contact-section h2 {
        font-size: 2.2em;
    }
}

@media (max-width: 768px) {
    .contact-info,
    .contact-form-container {
        padding: 30px 20px;
    }
    
    .social-icons {
        grid-template-columns: 1fr;
    }
    
    .map-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .map-btn {
        width: 200px;
    }
    
    .contact-info-item {
        flex-direction: column;
        text-align: center;
        gap: 10px;
    }
    
    .contact-icon {
        margin: 0 auto;
    }
}

@media (max-width: 480px) {
    .contact-section h2 {
        font-size: 1.8em;
    }
    
    .section-subtitle {
        font-size: 1em;
    }
    
    .form-header h3 {
        font-size: 1.5em;
    }
}
</style>

<section class="contact-section">
    <div class="container">
        <h2>Get In Touch 📞</h2>
        <p class="section-subtitle">We'd love to hear from you! Send us a message and we'll respond as soon as possible.</p>
        
        <div class="contact-content">
            <!-- Contact Information -->
            <div class="contact-info">
                <div class="contact-info-item">
                    <div class="contact-icon">📍</div>
                    <div class="contact-text">
                        <h3>Visit Our Store</h3>
                        <p>Don Bosco Street, Canlubang</p>
                        <p>Calamba, Laguna 4027</p>
                    </div>
                </div>
                
                <div class="contact-info-item">
                    <div class="contact-icon">📞</div>
                    <div class="contact-text">
                        <h3>Call Us</h3>
                        <p>+63 924 50072</p>
                        <p>+63 917 123 4567</p>
                    </div>
                </div>
                
                <div class="contact-info-item">
                    <div class="contact-icon">📧</div>
                    <div class="contact-text">
                        <h3>Email Us</h3>
                        <p>ToyRexCorner@gmail.com</p>
                        <p>Support@ToyRexCorner.com</p>
                    </div>
                </div>
                
                <div class="contact-info-item">
                    <div class="contact-icon">🕒</div>
                    <div class="contact-text">
                        <h3>Business Hours</h3>
                        <p><strong>Monday - Friday:</strong> 9:00 AM - 6:00 PM</p>
                        <p><strong>Saturday:</strong> 10:00 AM - 4:00 PM</p>
                        <p><strong>Sunday:</strong> Closed</p>
                    </div>
                </div>
                
                <div class="social-links-contact">
                    <h3>Follow Us</h3>
                    <div class="social-icons">
                        <a href="#" class="social-icon">📘 Facebook</a>
                        <a href="#" class="social-icon">🐦 Twitter</a>
                        <a href="#" class="social-icon">📷 Instagram</a>
                        <a href="#" class="social-icon">💼 LinkedIn</a>
                    </div>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="contact-form-container">
                <div class="form-header">
                    <h3>Send Us a Message 💬</h3>
                    <p>Fill out the form below and we'll get back to you ASAP!</p>
                </div>
                
                <?php if (!empty($errors)): ?>
                    <div class="error-message">
                        <h4>Please fix the following errors:</h4>
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li>❌ <?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($success_message)): ?>
                    <div class="success-message">
                        <h4>✅ Message Sent Successfully!</h4>
                        <p><?php echo $success_message; ?></p>
                    </div>
                <?php endif; ?>
                
                <form class="contact-form" method="POST" action="">
                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input type="text" id="name" name="name" 
                               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject *</label>
                        <select id="subject" name="subject" required>
                            <option value="">Select a subject</option>
                            <option value="General Inquiry" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'General Inquiry') ? 'selected' : ''; ?>>General Inquiry</option>
                            <option value="Product Question" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Product Question') ? 'selected' : ''; ?>>Product Question</option>
                            <option value="Order Support" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Order Support') ? 'selected' : ''; ?>>Order Support</option>
                            <option value="Wholesale Inquiry" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Wholesale Inquiry') ? 'selected' : ''; ?>>Wholesale Inquiry</option>
                            <option value="Partnership" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Partnership') ? 'selected' : ''; ?>>Partnership</option>
                            <option value="Other" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Your Message *</label>
                        <textarea id="message" name="message" rows="6" 
                                  placeholder="Tell us how we can help you..." required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                    </div>
                    
                    <button type="submit" class="submit-btn">
                        <span class="btn-text">Send Message</span>
                        <span class="btn-icon">🚀</span>
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Map Section -->
        <div class="map-section">
            <h3>Find Us Here 🗺️</h3>
            <div class="map-placeholder">
                <div class="map-content">
                    <div class="map-icon">📍</div>
                    <h4>ToyRex Corner - Canlubang Branch</h4>
                    <p>Don Bosco Street, Canlubang, Calamba, Laguna 4027</p>
                    <p>📍 Near Don Bosco College</p>
                    <div class="map-actions">
                        <button class="map-btn" onclick="openDirections()">Get Directions</button>
                        <button class="map-btn" onclick="callStore()">Call Store</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function openDirections() {
    // Google Maps URL for Don Bosco Canlubang
    const address = "Don+Bosco+Street+Canlubang+Calamba+Laguna+4027";
    window.open(`https://www.google.com/maps/search/?api=1&query=${address}`, '_blank');
}

function callStore() {
    window.location.href = 'tel:+6392450072';
}

// Form animation
document.addEventListener('DOMContentLoaded', function() {
    const formInputs = document.querySelectorAll('.contact-form input, .contact-form select, .contact-form textarea');
    
    formInputs.forEach(input => {
        // Add focus effects
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
            }
        });
        
        // Check if input has value on load
        if (input.value) {
            input.parentElement.classList.add('focused');
        }
    });
    
    // Auto-hide messages after 5 seconds
    setTimeout(() => {
        const messages = document.querySelectorAll('.error-message, .success-message');
        messages.forEach(message => {
            message.style.opacity = '0';
            setTimeout(() => {
                if (message.parentElement) {
                    message.style.display = 'none';
                }
            }, 300);
        });
    }, 5000);
});
</script>

<?php include 'includes/footer.php'; ?>