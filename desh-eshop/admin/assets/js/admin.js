/**
 * Admin Panel JavaScript
 * Desh Engineering Ecommerce
 */

// Global admin object
const Admin = {
    init: function() {
        this.initializeComponents();
        this.bindEvents();
        this.loadDashboardData();
    },

    initializeComponents: function() {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Initialize popovers
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });

        // Initialize file upload areas
        this.initializeFileUploads();

        // Initialize rich text editors
        this.initializeRichTextEditors();

        // Initialize image previews
        this.initializeImagePreviews();
    },

    bindEvents: function() {
        // Bulk actions
        this.bindBulkActions();

        // Status toggles
        this.bindStatusToggles();

        // Quick edit functionality
        this.bindQuickEdit();

        // Search and filters
        this.bindSearchFilters();

        // Form submissions
        this.bindFormSubmissions();
    },

    initializeFileUploads: function() {
        const uploadAreas = document.querySelectorAll('.file-upload-label');
        
        uploadAreas.forEach(area => {
            const input = area.parentElement.querySelector('.file-upload-input');
            
            // Click to upload
            area.addEventListener('click', () => {
                input.click();
            });

            // Drag and drop
            area.addEventListener('dragover', (e) => {
                e.preventDefault();
                area.classList.add('dragover');
            });

            area.addEventListener('dragleave', () => {
                area.classList.remove('dragover');
            });

            area.addEventListener('drop', (e) => {
                e.preventDefault();
                area.classList.remove('dragover');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    input.files = files;
                    this.handleFileUpload(input);
                }
            });

            // File input change
            input.addEventListener('change', () => {
                this.handleFileUpload(input);
            });
        });
    },

    handleFileUpload: function(input) {
        const file = input.files[0];
        if (!file) return;

        const label = input.parentElement.querySelector('.file-upload-label');
        const preview = input.parentElement.querySelector('.image-preview');

        // Update label text
        label.innerHTML = `
            <div>
                <i class="bi bi-check-circle text-success me-2"></i>
                <strong>${file.name}</strong>
                <br>
                <small class="text-muted">${this.formatFileSize(file.size)}</small>
            </div>
        `;

        // Show image preview if it's an image
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                if (preview) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                } else {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'image-preview';
                    img.style.display = 'block';
                    label.parentElement.appendChild(img);
                }
            };
            reader.readAsDataURL(file);
        }
    },

    initializeRichTextEditors: function() {
        // Simple rich text editor initialization
        const textareas = document.querySelectorAll('.rich-text-editor');
        textareas.forEach(textarea => {
            // Add toolbar
            const toolbar = this.createEditorToolbar();
            textarea.parentElement.insertBefore(toolbar, textarea);
            
            // Make textarea content editable
            textarea.style.minHeight = '200px';
            textarea.style.fontFamily = 'inherit';
        });
    },

    createEditorToolbar: function() {
        const toolbar = document.createElement('div');
        toolbar.className = 'editor-toolbar btn-toolbar mb-2';
        toolbar.innerHTML = `
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-command="bold" title="Bold">
                    <i class="bi bi-type-bold"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-command="italic" title="Italic">
                    <i class="bi bi-type-italic"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-command="underline" title="Underline">
                    <i class="bi bi-type-underline"></i>
                </button>
            </div>
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-command="insertUnorderedList" title="Bullet List">
                    <i class="bi bi-list-ul"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-command="insertOrderedList" title="Numbered List">
                    <i class="bi bi-list-ol"></i>
                </button>
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-command="createLink" title="Insert Link">
                    <i class="bi bi-link"></i>
                </button>
            </div>
        `;

        // Bind toolbar events
        toolbar.addEventListener('click', (e) => {
            const button = e.target.closest('button');
            if (button && button.dataset.command) {
                e.preventDefault();
                this.executeEditorCommand(button.dataset.command);
            }
        });

        return toolbar;
    },

    executeEditorCommand: function(command) {
        if (command === 'createLink') {
            const url = prompt('Enter URL:');
            if (url) {
                document.execCommand(command, false, url);
            }
        } else {
            document.execCommand(command, false, null);
        }
    },

    initializeImagePreviews: function() {
        const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
        imageInputs.forEach(input => {
            input.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        let preview = input.parentElement.querySelector('.image-preview');
                        if (!preview) {
                            preview = document.createElement('img');
                            preview.className = 'image-preview mt-2';
                            input.parentElement.appendChild(preview);
                        }
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    },

    bindBulkActions: function() {
        const bulkActionSelect = document.getElementById('bulkAction');
        const bulkActionBtn = document.getElementById('bulkActionBtn');
        const selectAllCheckbox = document.getElementById('selectAll');
        const itemCheckboxes = document.querySelectorAll('.item-checkbox');

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                itemCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                Admin.updateBulkActionButton();
            });
        }

        itemCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                this.updateBulkActionButton();
            });
        });

        if (bulkActionBtn) {
            bulkActionBtn.addEventListener('click', () => {
                const selectedItems = Array.from(itemCheckboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.value);

                if (selectedItems.length === 0) {
                    this.showNotification('Please select items to perform bulk action', 'warning');
                    return;
                }

                const action = bulkActionSelect.value;
                if (!action) {
                    this.showNotification('Please select an action', 'warning');
                    return;
                }

                this.performBulkAction(action, selectedItems);
            });
        }
    },

    updateBulkActionButton: function() {
        const itemCheckboxes = document.querySelectorAll('.item-checkbox');
        const bulkActionBtn = document.getElementById('bulkActionBtn');
        const selectedCount = Array.from(itemCheckboxes).filter(cb => cb.checked).length;

        if (bulkActionBtn) {
            bulkActionBtn.disabled = selectedCount === 0;
            bulkActionBtn.textContent = selectedCount > 0 
                ? `Apply to ${selectedCount} item(s)` 
                : 'Apply Action';
        }
    },

    performBulkAction: function(action, items) {
        if (!confirm(`Are you sure you want to ${action} ${items.length} item(s)?`)) {
            return;
        }

        // Show loading state
        const bulkActionBtn = document.getElementById('bulkActionBtn');
        const originalText = bulkActionBtn.textContent;
        bulkActionBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
        bulkActionBtn.disabled = true;

        // Perform AJAX request
        fetch(window.location.href, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                bulk_action: action,
                items: items
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showNotification(data.message || 'Bulk action completed successfully', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                this.showNotification(data.message || 'Bulk action failed', 'error');
            }
        })
        .catch(error => {
            console.error('Bulk action error:', error);
            this.showNotification('An error occurred during bulk action', 'error');
        })
        .finally(() => {
            bulkActionBtn.textContent = originalText;
            bulkActionBtn.disabled = false;
        });
    },

    bindStatusToggles: function() {
        const statusToggles = document.querySelectorAll('.status-toggle');
        statusToggles.forEach(toggle => {
            toggle.addEventListener('change', (e) => {
                const itemId = e.target.dataset.itemId;
                const itemType = e.target.dataset.itemType;
                const newStatus = e.target.checked ? 1 : 0;

                this.updateItemStatus(itemType, itemId, newStatus, e.target);
            });
        });
    },

    updateItemStatus: function(itemType, itemId, status, toggleElement) {
        const originalState = toggleElement.checked;
        
        fetch(`${window.location.origin}/admin/api/update-status.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                type: itemType,
                id: itemId,
                status: status
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showNotification(data.message || 'Status updated successfully', 'success');
            } else {
                // Revert toggle state
                toggleElement.checked = originalState;
                this.showNotification(data.message || 'Failed to update status', 'error');
            }
        })
        .catch(error => {
            console.error('Status update error:', error);
            toggleElement.checked = originalState;
            this.showNotification('An error occurred while updating status', 'error');
        });
    },

    bindQuickEdit: function() {
        const quickEditBtns = document.querySelectorAll('.quick-edit-btn');
        quickEditBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const itemId = btn.dataset.itemId;
                const itemType = btn.dataset.itemType;
                this.showQuickEditModal(itemType, itemId);
            });
        });
    },

    showQuickEditModal: function(itemType, itemId) {
        // Create and show quick edit modal
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Quick Edit ${itemType}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();

        // Load quick edit form
        fetch(`${window.location.origin}/admin/api/quick-edit.php?type=${itemType}&id=${itemId}`)
            .then(response => response.text())
            .then(html => {
                modal.querySelector('.modal-body').innerHTML = html;
                this.bindQuickEditForm(modal, itemType, itemId);
            })
            .catch(error => {
                console.error('Quick edit load error:', error);
                modal.querySelector('.modal-body').innerHTML = '<div class="alert alert-danger">Failed to load quick edit form</div>';
            });

        // Clean up modal when hidden
        modal.addEventListener('hidden.bs.modal', () => {
            modal.remove();
        });
    },

    bindQuickEditForm: function(modal, itemType, itemId) {
        const form = modal.querySelector('form');
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                
                const formData = new FormData(form);
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
                submitBtn.disabled = true;

                fetch(form.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.showNotification(data.message || 'Item updated successfully', 'success');
                        bootstrap.Modal.getInstance(modal).hide();
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        this.showNotification(data.message || 'Failed to update item', 'error');
                    }
                })
                .catch(error => {
                    console.error('Quick edit save error:', error);
                    this.showNotification('An error occurred while saving', 'error');
                })
                .finally(() => {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                });
            });
        }
    },

    bindSearchFilters: function() {
        const searchInput = document.getElementById('searchInput');
        const filterSelects = document.querySelectorAll('.filter-select');

        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.applyFilters();
                }, 500);
            });
        }

        filterSelects.forEach(select => {
            select.addEventListener('change', () => {
                this.applyFilters();
            });
        });
    },

    applyFilters: function() {
        const searchInput = document.getElementById('searchInput');
        const filterSelects = document.querySelectorAll('.filter-select');
        
        const params = new URLSearchParams();
        
        if (searchInput && searchInput.value.trim()) {
            params.append('search', searchInput.value.trim());
        }

        filterSelects.forEach(select => {
            if (select.value) {
                params.append(select.name, select.value);
            }
        });

        // Update URL and reload page with filters
        const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
        window.location.href = newUrl;
    },

    bindFormSubmissions: function() {
        const forms = document.querySelectorAll('form.ajax-form');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.submitFormAjax(form);
            });
        });
    },

    submitFormAjax: function(form) {
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
        submitBtn.disabled = true;

        fetch(form.action || window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showNotification(data.message || 'Operation completed successfully', 'success');
                if (data.redirect) {
                    setTimeout(() => window.location.href = data.redirect, 1500);
                } else if (data.reload) {
                    setTimeout(() => location.reload(), 1500);
                }
            } else {
                this.showNotification(data.message || 'Operation failed', 'error');
            }
        })
        .catch(error => {
            console.error('Form submission error:', error);
            this.showNotification('An error occurred while processing the form', 'error');
        })
        .finally(() => {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    },

    loadDashboardData: function() {
        // Load real-time dashboard data
        if (window.location.pathname.includes('/admin/index.php') || window.location.pathname.endsWith('/admin/')) {
            this.updateDashboardStats();
            
            // Update stats every 30 seconds
            setInterval(() => {
                this.updateDashboardStats();
            }, 30000);
        }
    },

    updateDashboardStats: function() {
        fetch('/admin/api/dashboard-stats.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update stat cards
                    Object.keys(data.stats).forEach(key => {
                        const element = document.getElementById(`stat-${key}`);
                        if (element) {
                            element.textContent = data.stats[key];
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Dashboard stats update error:', error);
            });
    },

    showNotification: function(message, type = 'info') {
        const alertClass = type === 'error' ? 'alert-danger' : `alert-${type}`;
        const iconClass = type === 'error' ? 'bi-exclamation-triangle' : 
                         type === 'success' ? 'bi-check-circle' : 'bi-info-circle';
        
        const alert = document.createElement('div');
        alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
        alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alert.innerHTML = `
            <i class="bi ${iconClass} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alert);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alert.parentElement) {
                alert.remove();
            }
        }, 5000);
    },

    formatFileSize: function(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    },

    formatCurrency: function(amount) {
        return new Intl.NumberFormat('en-BD', {
            style: 'currency',
            currency: 'BDT'
        }).format(amount);
    },

    formatDate: function(dateString) {
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
};

// Initialize admin panel when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    Admin.init();
});

// Export for global use
window.Admin = Admin;
