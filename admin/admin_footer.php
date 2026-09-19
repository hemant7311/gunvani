        </main>
        <!-- End Main Page Content -->

        <!-- Footer -->
        <footer class="px-4 py-3 bg-white border-top text-center text-muted small mt-auto">
            © <?= date('Y') ?> <strong>Gunvani News</strong> — All Rights Reserved. Production CMS Admin Panel.
        </footer>
    </div>
    <!-- End Main Wrapper -->

    <!-- Shared Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel"><i class="fa-solid fa-triangle-exclamation me-2"></i>Confirm Deletion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="display-6 text-danger mb-3"><i class="fa-solid fa-trash-can"></i></div>
                    <h5>Are you sure you want to delete this item?</h5>
                    <p class="text-muted small mb-0">This action cannot be undone and will permanently remove the record.</p>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="deleteConfirmBtn" class="btn btn-danger px-4">Delete Permanently</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Sidebar Collapse Toggle Desktop
    function toggleSidebar() {
        if (window.innerWidth <= 991) {
            // On mobile, just slide out the full sidebar drawer
            document.body.classList.toggle('sidebar-open');
            document.body.classList.remove('sidebar-collapsed');
        } else {
            // On desktop, toggle the collapsed mode
            document.body.classList.toggle('sidebar-collapsed');
        }
    }

    // Mobile Sidebar Drawer Overlay Toggle
    function toggleMobileSidebar() {
        document.body.classList.remove('sidebar-open');
    }

    // Safe Delete Modal Confirmation
    function confirmDeleteModal(url) {
        var modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
        document.getElementById('deleteConfirmBtn').setAttribute('href', url);
        modal.show();
        return false;
    }

    // Initialize Tooltips
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title], [data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                trigger: 'hover'
            });
        });
    });
    </script>
</body>
</html>
