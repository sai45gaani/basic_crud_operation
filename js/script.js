// js/script.js
// Global functions for the application
document.addEventListener('DOMContentLoaded', function() {
    // Initialize any Bootstrap components that need JavaScript
    
    // Initialize charts if the container exists
    const chartContainer = document.getElementById('entriesChart');
    if (chartContainer) {
        // Chart data will be added by PHP
    }
    
    // Initialize delete confirmations
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
                return false;
            }
        });
    });
    
    // Initialize form validations
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
});