        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
        
        // Confirm delete actions
        function confirmDelete(message) {
            return confirm(message || 'Are you sure you want to delete this item?');
        }
        
        // Format currency
        function formatCurrency(amount) {
            return '<?php echo CURRENCY_SYMBOL; ?>' + parseFloat(amount).toFixed(2);
        }
        
        // Calculate days left
        function calculateDaysLeft(expiryDate) {
            const today = new Date();
            const expiry = new Date(expiryDate);
            const diffTime = expiry - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            return diffDays;
        }
        
        // Get alert class based on days left
        function getAlertClass(daysLeft) {
            if (daysLeft < 0) return 'danger';
            if (daysLeft <= 7) return 'danger';
            if (daysLeft <= 15) return 'warning';
            if (daysLeft <= 30) return 'info';
            return 'success';
        }
        
        // Mobile sidebar toggle
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');
        
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
            });
        }
    </script>
    
    <?php if (isset($additionalScripts)): ?>
        <?php echo $additionalScripts; ?>
    <?php endif; ?>
</body>
</html>

// Made with Bob
