// Modal functionality
class ModalSystem {
    constructor() {
        this.init();
    }
    
    init() {
        // Login modal
        document.getElementById('loginBtn')?.addEventListener('click', (e) => {
            e.preventDefault();
            this.showModal('loginModal');
        });
        
        // Register modal  
        document.getElementById('registerBtn')?.addEventListener('click', (e) => {
            e.preventDefault();
            this.showModal('registerModal');
        });
        
        // Close buttons
        document.querySelectorAll('.close-modal').forEach(btn => {
            btn.addEventListener('click', () => {
                this.hideAllModals();
            });
        });
        
        // Close on outside click
        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.hideModal(modal.id);
                }
            });
        });
        
        // Close with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.hideAllModals();
            }
        });
    }
    
    showModal(modalId) {
        document.getElementById(modalId)?.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    hideModal(modalId) {
        document.getElementById(modalId)?.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
    
    hideAllModals() {
        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.classList.remove('active');
        });
        document.body.style.overflow = 'auto';
    }
}

// Initialize modal system
new ModalSystem();