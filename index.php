<?php
require_once 'config/database.php';
require_once 'classes/Database.php';
require_once 'classes/Item.php';
require_once 'includes/header.php';

// Initialize database connection
$db = new Database();
$item = new Item($db->getConnection());

// Get featured/latest items
$featured_items = $item->getFeaturedItems(6); // Get 6 featured items
$latest_items = $item->getLatestItems(8); // Get 8 latest items
?>

<!-- Hero Section -->
<div class="bg-dark text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4">Rent Anything, Anytime</h1>
                <p class="lead">Access thousands of items without the burden of ownership.</p>
                <?php if(!isset($_SESSION['user_id'])): ?>
                    <a href="register.php" class="btn btn-primary btn-lg">Get Started</a>
                    <a href="items.php" class="btn btn-outline-light btn-lg ms-2">Browse Items</a>
                <?php else: ?>
                    <a href="items.php" class="btn btn-primary btn-lg">Browse Items</a>
                    <a href="dashboard.php" class="btn btn-outline-light btn-lg ms-2">My Dashboard</a>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <img src="assets/images/hero-image.png" alt="Rental Items" class="img-fluid rounded">
            </div>
        </div>
    </div>
</div>

<!-- Featured Categories -->
<div class="container my-5">
    <h2 class="text-center mb-4">Popular Categories</h2>
    <div class="row g-4">
        <div class="col-md-3">
            <a href="items.php?category=Electronics" class="text-decoration-none">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-laptop fa-3x mb-3 text-primary"></i>
                        <h5 class="card-title">Electronics</h5>
                        <p class="card-text text-muted">Cameras, Laptops, and more</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="items.php?category=Sports" class="text-decoration-none">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-basketball-ball fa-3x mb-3 text-success"></i>
                        <h5 class="card-title">Sports</h5>
                        <p class="card-text text-muted">Equipment and gear</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="items.php?category=Tools" class="text-decoration-none">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-tools fa-3x mb-3 text-warning"></i>
                        <h5 class="card-title">Tools</h5>
                        <p class="card-text text-muted">Power tools and equipment</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="items.php?category=Events" class="text-decoration-none">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-birthday-cake fa-3x mb-3 text-danger"></i>
                        <h5 class="card-title">Events</h5>
                        <p class="card-text text-muted">Party and event equipment</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- Featured Items -->
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Featured Items</h2>
        <a href="items.php" class="btn btn-outline-primary">View All</a>
    </div>
    <div class="row g-4">
        <?php foreach($featured_items as $item): ?>
            <div class="col-md-4">
                <div class="card h-100">
                    <img src="<?php echo htmlspecialchars($item['image_url'] ?? 'assets/images/placeholder.png'); ?>" 
                         class="card-img-top" alt="<?php echo htmlspecialchars($item['name']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars(substr($item['description'], 0, 100)) . '...'; ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-primary fw-bold">$<?php echo number_format($item['daily_rate'], 2); ?>/day</span>
                            <a href="item.php?id=<?php echo $item['item_id']; ?>" class="btn btn-outline-primary">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- How It Works -->
<div class="bg-light py-5">
    <div class="container">
        <h2 class="text-center mb-5">How It Works</h2>
        <div class="row g-4">
            <div class="col-md-4 text-center">
                <div class="p-3">
                    <i class="fas fa-search fa-3x mb-3 text-primary"></i>
                    <h4>Find Items</h4>
                    <p>Browse through our extensive collection of items available for rent</p>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="p-3">
                    <i class="fas fa-calendar-alt fa-3x mb-3 text-primary"></i>
                    <h4>Book & Pay</h4>
                    <p>Select your rental dates and make secure payment</p>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="p-3">
                    <i class="fas fa-box-open fa-3x mb-3 text-primary"></i>
                    <h4>Pickup & Return</h4>
                    <p>Get your items and return them after use</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
