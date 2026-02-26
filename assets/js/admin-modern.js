/**
 * FITZONE ADMIN PANEL - MODERN JAVASCRIPT
 * Interactive features and utilities
 */

(function() {
    'use strict';

    // ========================================
    // SIDEBAR MANAGEMENT
    // ========================================

    const sidebar = document.getElementById('adminSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const content = document.querySelector('.admin-content');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('show');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 1024) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });

        // Close sidebar on window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 1024) {
                sidebar.classList.remove('show');
            }
        });
    }

    // ========================================
    // TOAST NOTIFICATIONS
    // ========================================

    window.showToast = function(message, type = 'info', duration = 5000) {
        // Create toast container if it doesn't exist
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }

        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;

        const icons = {
            success: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>',
            error: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>',
            warning: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>',
            info: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>'
        };

        const titles = {
            success: 'Success',
            error: 'Error',
            warning: 'Warning',
            info: 'Info'
        };

        toast.innerHTML = `
            <div class="toast-icon">${icons[type] || icons.info}</div>
            <div class="toast-content">
                <div class="toast-title">${titles[type]}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        `;

        container.appendChild(toast);

        // Auto remove after duration
        if (duration > 0) {
            setTimeout(() => {
                toast.style.animation = 'slideOutUp 0.3s ease-in forwards';
                setTimeout(() => toast.remove(), 300);
            }, duration);
        }

        return toast;
    };

    // ========================================
    // MODAL MANAGEMENT
    // ========================================

    window.showModal = function(options) {
        const {
            title = 'Modal',
            content = '',
            confirmText = 'Confirm',
            cancelText = 'Cancel',
            onConfirm = () => {},
            onCancel = () => {},
            showCancel = true
        } = options;

        // Create overlay
        const overlay = document.createElement('div');
        overlay.className = 'modal-overlay';

        // Create modal
        const modal = document.createElement('div');
        modal.className = 'modal';

        modal.innerHTML = `
            <div class="modal-header">
                <h3 class="modal-title">${title}</h3>
                <button class="modal-close" onclick="this.closest('.modal-overlay').remove()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="modal-body">${content}</div>
            <div class="modal-footer">
                ${showCancel ? `<button class="btn btn-secondary modal-cancel-btn">${cancelText}</button>` : ''}
                <button class="btn btn-primary modal-confirm-btn">${confirmText}</button>
            </div>
        `;

        overlay.appendChild(modal);
        document.body.appendChild(overlay);

        // Add event listeners
        const confirmBtn = modal.querySelector('.modal-confirm-btn');
        const cancelBtn = modal.querySelector('.modal-cancel-btn');

        if (confirmBtn) {
            confirmBtn.addEventListener('click', function() {
                onConfirm();
                overlay.remove();
            });
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', function() {
                onCancel();
                overlay.remove();
            });
        }

        // Close on overlay click
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                onCancel();
                overlay.remove();
            }
        });

        // Close on ESC key
        document.addEventListener('keydown', function escHandler(e) {
            if (e.key === 'Escape') {
                onCancel();
                overlay.remove();
                document.removeEventListener('keydown', escHandler);
            }
        });

        return overlay;
    };

    // ========================================
    // CONFIRM DIALOG
    // ========================================

    window.confirmDialog = function(message, onConfirm, onCancel = () => {}) {
        return showModal({
            title: 'Confirm Action',
            content: `<p style="font-size: 16px; color: var(--gray-700);">${message}</p>`,
            confirmText: 'Confirm',
            cancelText: 'Cancel',
            onConfirm: onConfirm,
            onCancel: onCancel,
            showCancel: true
        });
    };

    // ========================================
    // DELETE CONFIRMATION
    // ========================================

    const deleteButtons = document.querySelectorAll('[data-delete-confirm]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.href || this.dataset.deleteUrl;
            const message = this.dataset.deleteConfirm || 'Are you sure you want to delete this item? This action cannot be undone.';

            confirmDialog(message, function() {
                if (url) {
                    window.location.href = url;
                }
            });
        });
    });

    // ========================================
    // TABS MANAGEMENT
    // ========================================

    const tabs = document.querySelectorAll('.tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabGroup = this.closest('.tabs');
            const targetId = this.dataset.tab;

            // Remove active class from all tabs in group
            tabGroup.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            // Show target content
            const container = tabGroup.nextElementSibling;
            container.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });

            const targetContent = document.getElementById(targetId);
            if (targetContent) {
                targetContent.classList.add('active');
            }
        });
    });

    // ========================================
    // DROPDOWN MANAGEMENT
    // ========================================

    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(dropdown => {
        const trigger = dropdown.querySelector('[data-bs-toggle="dropdown"]');
        const menu = dropdown.querySelector('.dropdown-menu');

        if (trigger && menu) {
            trigger.addEventListener('click', function(e) {
                e.stopPropagation();
                menu.classList.toggle('show');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!dropdown.contains(e.target)) {
                    menu.classList.remove('show');
                }
            });
        }
    });

    // ========================================
    // TABLE SORTING
    // ========================================

    const sortableHeaders = document.querySelectorAll('[data-sortable]');
    sortableHeaders.forEach(header => {
        header.style.cursor = 'pointer';
        header.addEventListener('click', function() {
            const table = this.closest('table');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const columnIndex = Array.from(this.parentElement.children).indexOf(this);
            const currentOrder = this.dataset.order || 'asc';
            const newOrder = currentOrder === 'asc' ? 'desc' : 'asc';

            // Update order
            this.dataset.order = newOrder;

            // Sort rows
            rows.sort((a, b) => {
                const aValue = a.children[columnIndex].textContent.trim();
                const bValue = b.children[columnIndex].textContent.trim();

                if (newOrder === 'asc') {
                    return aValue.localeCompare(bValue, undefined, { numeric: true });
                } else {
                    return bValue.localeCompare(aValue, undefined, { numeric: true });
                }
            });

            // Re-append sorted rows
            rows.forEach(row => tbody.appendChild(row));
        });
    });

    // ========================================
    // SEARCH FUNCTIONALITY
    // ========================================

    const searchInputs = document.querySelectorAll('[data-search]');
    searchInputs.forEach(input => {
        const targetSelector = input.dataset.search;
        const targetElements = document.querySelectorAll(targetSelector);

        input.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();

            targetElements.forEach(element => {
                const text = element.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    element.style.display = '';
                } else {
                    element.style.display = 'none';
                }
            });
        });
    });

    // ========================================
    // DARK MODE TOGGLE
    // ========================================

    const darkModeToggle = document.getElementById('darkModeToggle');
    if (darkModeToggle) {
        // Load saved preference
        const currentTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', currentTheme);

        darkModeToggle.addEventListener('click', function() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);

            showToast(`${newTheme === 'dark' ? 'Dark' : 'Light'} mode activated`, 'info', 3000);
        });
    }

    // ========================================
    // FILE UPLOAD PREVIEW
    // ========================================

    const fileInputs = document.querySelectorAll('input[type="file"][data-preview]');
    fileInputs.forEach(input => {
        const previewId = input.dataset.preview;
        const preview = document.getElementById(previewId);

        if (preview) {
            input.addEventListener('change', function() {
                const file = this.files[0];
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });

    // ========================================
    // COPY TO CLIPBOARD
    // ========================================

    window.copyToClipboard = function(text) {
        navigator.clipboard.writeText(text).then(function() {
            showToast('Copied to clipboard!', 'success', 2000);
        }).catch(function() {
            showToast('Failed to copy', 'error', 2000);
        });
    };

    // ========================================
    // KEYBOARD SHORTCUTS
    // ========================================

    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + K - Focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const searchInput = document.querySelector('.topbar-search input');
            if (searchInput) {
                searchInput.focus();
            }
        }

        // Ctrl/Cmd + B - Toggle sidebar
        if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
            e.preventDefault();
            if (sidebar) {
                sidebar.classList.toggle('show');
            }
        }
    });

    // ========================================
    // FORM AUTO-SAVE
    // ========================================

    const autoSaveForms = document.querySelectorAll('[data-autosave]');
    autoSaveForms.forEach(form => {
        const formId = form.id || 'form-' + Date.now();
        const inputs = form.querySelectorAll('input, textarea, select');

        // Load saved data
        inputs.forEach(input => {
            const savedValue = localStorage.getItem(`${formId}-${input.name}`);
            if (savedValue && input.type !== 'file') {
                input.value = savedValue;
            }
        });

        // Save on change
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.type !== 'file') {
                    localStorage.setItem(`${formId}-${this.name}`, this.value);
                }
            });
        });

        // Clear saved data on submit
        form.addEventListener('submit', function() {
            inputs.forEach(input => {
                localStorage.removeItem(`${formId}-${input.name}`);
            });
        });
    });

    // ========================================
    // ANIMATE ON SCROLL
    // ========================================

    const animateOnScroll = function() {
        const elements = document.querySelectorAll('[data-animate]');

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const animation = entry.target.dataset.animate;
                    entry.target.classList.add(`animate-${animation}`);
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1
        });

        elements.forEach(element => observer.observe(element));
    };

    if ('IntersectionObserver' in window) {
        animateOnScroll();
    }

    // ========================================
    // RIPPLE EFFECT
    // ========================================

    document.querySelectorAll('.ripple-container').forEach(element => {
        element.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            ripple.className = 'ripple-effect';

            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';

            this.appendChild(ripple);

            setTimeout(() => ripple.remove(), 600);
        });
    });

    // ========================================
    // AUTO-DISMISS ALERTS
    // ========================================

    const alerts = document.querySelectorAll('.alert[data-dismiss-after]');
    alerts.forEach(alert => {
        const dismissAfter = parseInt(alert.dataset.dismissAfter) || 5000;
        setTimeout(() => {
            alert.style.animation = 'slideOutUp 0.3s ease-in forwards';
            setTimeout(() => alert.remove(), 300);
        }, dismissAfter);
    });

    // ========================================
    // INITIALIZE TOOLTIPS
    // ========================================

    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(element => {
        element.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = this.dataset.tooltip;
            tooltip.style.cssText = `
                position: absolute;
                background: var(--dark-900);
                color: var(--white);
                padding: 6px 12px;
                border-radius: 6px;
                font-size: 12px;
                z-index: 10000;
                white-space: nowrap;
                pointer-events: none;
            `;

            document.body.appendChild(tooltip);

            const rect = this.getBoundingClientRect();
            tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
            tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';

            this._tooltip = tooltip;
        });

        element.addEventListener('mouseleave', function() {
            if (this._tooltip) {
                this._tooltip.remove();
                this._tooltip = null;
            }
        });
    });

    // ========================================
    // INITIALIZE ON PAGE LOAD
    // ========================================

    console.log('%cFitZone Admin Panel', 'font-size: 20px; font-weight: bold; color: #6366f1;');
    console.log('%cModern Admin Panel v2.0', 'font-size: 12px; color: #64748b;');

})();
