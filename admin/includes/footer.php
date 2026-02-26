            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Modern Admin JS -->
    <script src="<?= SITE_URL ?>/assets/js/admin-modern.js"></script>

    <!-- Initialize Feather Icons -->
    <script>
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    </script>

    <?php if (isset($additionalJS)): ?>
        <?= $additionalJS ?>
    <?php endif; ?>

    <!-- Page-specific scripts -->
    <script>
        // Auto-hide alerts after 5 seconds
        document.querySelectorAll('.alert:not([data-persistent])').forEach(alert => {
            setTimeout(() => {
                if (bootstrap && bootstrap.Alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 5000);
        });

        // Confirm delete actions
        document.querySelectorAll('[data-confirm-delete]').forEach(element => {
            element.addEventListener('click', function(e) {
                e.preventDefault();
                const message = this.dataset.confirmDelete || 'Are you sure you want to delete this item?';

                confirmDialog(message, () => {
                    const form = this.closest('form');
                    if (form) {
                        form.submit();
                    } else if (this.href) {
                        window.location.href = this.href;
                    }
                });
            });
        });

        // Image preview on file select
        document.querySelectorAll('input[type="file"][accept*="image"]').forEach(input => {
            input.addEventListener('change', function() {
                const file = this.files[0];
                if (file && file.type.startsWith('image/')) {
                    const preview = document.getElementById(this.name + '_preview') ||
                                  document.querySelector(`img[data-preview-for="${this.name}"]`);

                    if (preview) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            preview.src = e.target.result;
                            preview.style.display = 'block';
                        };
                        reader.readAsDataURL(file);
                    }
                }
            });
        });

        // Initialize tooltips
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            const tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltips.map(el => new bootstrap.Tooltip(el));
        }
    </script>
</body>
</html>
