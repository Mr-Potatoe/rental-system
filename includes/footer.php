    <footer class="footer bg-light mt-auto py-3">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>About Us</h5>
                    <p class="text-muted">Your trusted platform for renting items safely and securely.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-muted">Home</a></li>
                        <li><a href="items.php" class="text-muted">Browse Items</a></li>
                        <li><a href="contact.php" class="text-muted">Contact Us</a></li>
                        <li><a href="terms.php" class="text-muted">Terms & Conditions</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Info</h5>
                    <ul class="list-unstyled text-muted">
                        <li><i class="fas fa-envelope me-2"></i>support@rentalsystem.com</li>
                        <li><i class="fas fa-phone me-2"></i>(555) 123-4567</li>
                        <li><i class="fas fa-map-marker-alt me-2"></i>123 Rental Street, City, Country</li>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-12 text-center">
                    <p class="text-muted mb-0">&copy; <?php echo date('Y'); ?> Rental System. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom JavaScript -->
    <script>
        // Enable Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Enable Bootstrap popovers
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl)
        });
    </script>
</body>
</html>
