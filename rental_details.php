<?php
require_once 'config/database.php';
require_once 'classes/Database.php';
require_once 'classes/Item.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db = new Database();
$conn = $db->getConnection();

// Get rental transaction ID from URL
$transaction_id = intval($_GET['id'] ?? 0);

// Get rental details with related information
$sql = "SELECT r.*, 
               i.name as item_name, i.description as item_description, 
               i.daily_rate, i.deposit_amount,
               o.username as owner_name, o.email as owner_email,
               rr.username as renter_name, rr.email as renter_email
        FROM Rental_Transactions r
        JOIN Items i ON r.item_id = i.item_id
        JOIN Users o ON i.owner_id = o.user_id
        JOIN Users rr ON r.renter_id = rr.user_id
        WHERE r.transaction_id = :transaction_id 
        AND (r.renter_id = :user_id OR i.owner_id = :user_id)";

$stmt = $conn->prepare($sql);
$stmt->execute([
    'transaction_id' => $transaction_id,
    'user_id' => $_SESSION['user_id']
]);

$rental = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$rental) {
    header('Location: dashboard.php');
    exit;
}

// Get reviews for this transaction
$sql_reviews = "SELECT r.*, u.username 
                FROM Reviews r
                JOIN Users u ON r.reviewer_id = u.user_id
                WHERE r.transaction_id = :transaction_id";
$stmt_reviews = $conn->prepare($sql_reviews);
$stmt_reviews->execute(['transaction_id' => $transaction_id]);
$reviews = $stmt_reviews->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Rental Details</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Item Information</h5>
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($rental['item_name']); ?></p>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($rental['item_description']); ?></p>
                            <p><strong>Daily Rate:</strong> $<?php echo number_format($rental['daily_rate'], 2); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5>Rental Information</h5>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-<?php 
                                    echo $rental['status'] == 'ACTIVE' ? 'success' : 
                                         ($rental['status'] == 'COMPLETED' ? 'info' : 'warning'); 
                                ?>">
                                    <?php echo $rental['status']; ?>
                                </span>
                            </p>
                            <p><strong>Start Date:</strong> <?php echo date('M d, Y', strtotime($rental['start_date'])); ?></p>
                            <p><strong>End Date:</strong> <?php echo date('M d, Y', strtotime($rental['end_date'])); ?></p>
                            <p><strong>Total Cost:</strong> $<?php echo number_format($rental['total_cost'], 2); ?></p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Owner Information</h5>
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($rental['owner_name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($rental['owner_email']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5>Renter Information</h5>
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($rental['renter_name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($rental['renter_email']); ?></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <h5>Location Details</h5>
                            <p><strong>Pickup Location:</strong> <?php echo htmlspecialchars($rental['pickup_location']); ?></p>
                            <p><strong>Return Location:</strong> <?php echo htmlspecialchars($rental['return_location']); ?></p>
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
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <h6><?php echo htmlspecialchars($review['username']); ?></h6>
                                <div>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <p class="mb-1"><?php echo htmlspecialchars($review['review_text']); ?></p>
                            <small class="text-muted">
                                <?php echo date('M d, Y', strtotime($review['review_date'])); ?>
                            </small>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Actions</h5>
                    <div class="d-grid gap-2">
                        <?php if ($rental['status'] == 'ACTIVE'): ?>
                            <a href="complete_rental.php?id=<?php echo $rental['transaction_id']; ?>" 
                               class="btn btn-success">Mark as Completed</a>
                        <?php endif; ?>
                        <a href="submit_review.php?id=<?php echo $rental['transaction_id']; ?>" 
                           class="btn btn-primary">Submit Review</a>
                        <?php if ($rental['status'] == 'ACTIVE'): ?>
                            <a href="report_issue.php?id=<?php echo $rental['transaction_id']; ?>" 
                               class="btn btn-warning">Report Issue</a>
                        <?php endif; ?>
                        <a href="dashboard.php" class="btn btn-link">Back to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 