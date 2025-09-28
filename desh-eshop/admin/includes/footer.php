    </main>
    
    <!-- Footer -->
    <footer class="admin-footer bg-light border-top py-3 mt-5">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <small class="text-muted">
                        &copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.
                    </small>
                </div>
                <div class="col-md-6 text-md-end">
                    <small class="text-muted">
                        Admin Panel v1.0 | 
                        <a href="<?php echo SITE_URL; ?>/admin/support.php" class="text-decoration-none">Support</a> | 
                        <a href="<?php echo SITE_URL; ?>/admin/settings.php" class="text-decoration-none">Settings</a>
                    </small>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Custom Admin JS -->
    <script src="<?php echo SITE_URL; ?>/admin/assets/js/admin.js"></script>
    
    <!-- Additional scripts -->
    <?php if (isset($additional_scripts)) echo $additional_scripts; ?>
    
    <script>
        // Initialize DataTables
        $(document).ready(function() {
            $('.data-table').DataTable({
                responsive: true,
                pageLength: 25,
                order: [[0, 'desc']],
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                }
            });
            
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
            
            // Confirm delete actions
            $('.btn-delete').on('click', function(e) {
                if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                    e.preventDefault();
                }
            });
            
            // Form validation
            $('.needs-validation').on('submit', function(e) {
                if (!this.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                this.classList.add('was-validated');
            });
        });
        
        // Utility functions
        function showNotification(message, type = 'info') {
            const alertClass = type === 'error' ? 'alert-danger' : `alert-${type}`;
            const iconClass = type === 'error' ? 'bi-exclamation-triangle' : 
                             type === 'success' ? 'bi-check-circle' : 'bi-info-circle';
            
            const alert = $(`
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    <i class="bi ${iconClass} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `);
            
            $('.admin-main').prepend(alert);
            
            setTimeout(function() {
                alert.fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 5000);
        }
        
        function formatCurrency(amount) {
            return new Intl.NumberFormat('en-BD', {
                style: 'currency',
                currency: 'BDT'
            }).format(amount);
        }
        
        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    </script>
</body>
</html>
