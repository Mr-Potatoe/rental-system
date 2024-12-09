<?php
require_once 'config/database.php';
require_once 'classes/Database.php';
require_once 'classes/Item.php';
require_once 'classes/User.php';
require_once 'includes/header.php';

$db = new Database();
$item = new Item($db->getConnection());
$user = new User($db->getConnection());

// Get item ID from URL
$item_id = intval($_GET['id'] ?? 0);
$item_data = $item->getItemById($item_id);

if (!$item_data) {
    header('Location: index.php');
    exit;
}

// Get owner information
$owner = $user->getUserById($item_data['owner_id']);

// Get reviews for this item
$sql = "SELECT r.*, u.username 
        FROM Reviews r
        JOIN Users u ON r.reviewer_id = u.user_id
        JOIN Rental_Transactions rt ON r.transaction_id = rt.transaction_id
        WHERE rt.item_id = :item_id
        ORDER BY r.review_date DESC";
$stmt = $db->getConnection()->prepare($sql);
$stmt->execute(['item_id' => $item_id]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate average rating
$average_rating = 0;
if (!empty($reviews)) {
    $total_rating = array_sum(array_column($reviews, 'rating'));
    $average_rating = round($total_rating / count($reviews), 1);
}
?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="card-title mb-3"><?php echo htmlspecialchars($item_data['name']); ?></h2>
                    
                    <?php if ($average_rating > 0): ?>
                    <div class="mb-3">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?php echo $i <= $average_rating ? 'text-warning' : 'text-muted'; ?>"></i>
                        <?php endfor; ?>
                        <span class="ms-2"><?php echo $average_rating; ?> (<?php echo count($reviews); ?> reviews)</span>
                    </div>
                    <?php endif; ?>

                    <div class="mb-4">
                        <h5>Description</h5>
                        <p><?php echo nl2br(htmlspecialchars($item_data['description'])); ?></p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Details</h5>
                            <ul class="list-unstyled">
                                <li><strong>Category:</strong> <?php echo htmlspecialchars($item_data['category']); ?></li>
                                <li><strong>Condition:</strong> <?php echo htmlspecialchars($item_data['condition']); ?></li>
                                <li><strong>Location:</strong> <?php echo htmlspecialchars($item_data['location']); ?></li>
                                <li><strong>Maximum Rental Duration:</strong> <?php echo $item_data['max_rental_duration']; ?> days</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Owner Information</h5>
                            <ul class="list-unstyled">
                                <li><strong>Name:</strong> <?php echo htmlspecialchars($owner['full_name']); ?></li>
                                <li><strong>Member Since:</strong> 
                                    <?php echo date('M d, Y', strtotime($owner['registration_date'])); ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($reviews)): ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Reviews</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($reviews as $review): ?>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-1"><?php echo htmlspecialchars($review['username']); ?></h6>
                                <small class="text-muted"><?php echo date('M d, Y', strtotime($review['review_date'])); ?></small>
                            </div>
                            <div class="mb-2">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <p class="mb-0"><?php echo htmlspecialchars($review['review_text']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-4">Rental Information</h5>
                    <div class="mb-3">
                        <h3 class="text-primary mb-0">$<?php echo number_format($item_data['daily_rate'], 2); ?></h3>
                        <small class="text-muted">per day</small>
                    </div>
                    <p><strong>Security Deposit:</strong> $<?php echo number_format($item_data['deposit_amount'], 2); ?></p>
                    
                    <?php if ($item_data['insurance_required']): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Insurance required for this item
                        </div>
                    <?php endif; ?>

                    <div class="d-grid gap-2">
                        <?php if ($item_data['availability_status'] == 'AVAILABLE'): ?>
                            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $item_data['owner_id']): ?>
                                <a href="rent_item.php?id=<?php echo $item_id; ?>" class="btn btn-primary">Rent Now</a>
                            <?php elseif (!isset($_SESSION['user_id'])): ?>
                                <a href="login.php" class="btn btn-primary">Login to Rent</a>
                            <?php endif; ?>
                        <?php else: ?>
                            <button class="btn btn-secondary" disabled>Currently Unavailable</button>
                        <?php endif; ?>
                        <a href="javascript:history.back()" class="btn btn-link">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 