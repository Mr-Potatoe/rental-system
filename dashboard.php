<?php
require_once 'config/database.php';
require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'classes/Item.php';
require_once 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db = new Database();
$conn = $db->getConnection();
$user_id = $_SESSION['user_id'];

// Get user's items and rentals
$sql_my_items = "SELECT * FROM Items WHERE owner_id = :user_id ORDER BY created_at DESC";
$stmt_items = $conn->prepare($sql_my_items);
$stmt_items->execute(['user_id' => $user_id]);
$my_items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

$sql_my_rentals = "SELECT r.*, i.name as item_name, i.daily_rate, u.username as owner_name 
                   FROM Rental_Transactions r 
                   JOIN Items i ON r.item_id = i.item_id 
                   JOIN Users u ON i.owner_id = u.user_id 
                   WHERE r.renter_id = :user_id 
                   ORDER BY r.start_date DESC";
$stmt_rentals = $conn->prepare($sql_my_rentals);
$stmt_rentals->execute(['user_id' => $user_id]);
$my_rentals = $stmt_rentals->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container my-5">
    <!-- Account Overview -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></h4>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center">
                                <h5>Account Balance</h5>
                                <h3 class="text-primary">$<?php echo number_format($_SESSION['account_balance'] ?? 0, 2); ?></h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center">
                                <h5>My Items</h5>
                                <h3 class="text-success"><?php echo count($my_items); ?></h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center">
                                <h5>Active Rentals</h5>
                                <h3 class="text-warning"><?php echo count(array_filter($my_rentals, function($rental) { 
                                    return $rental['status'] == 'ACTIVE'; 
                                })); ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- My Items -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Items</h5>
                    <a href="add_item.php" class="btn btn-primary btn-sm">Add New Item</a>
                </div>
                <div class="card-body">
                    <?php if (empty($my_items)): ?>
                        <p class="text-muted text-center">You haven't listed any items yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Daily Rate</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($my_items as $item): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                                            <td>$<?php echo number_format($item['daily_rate'], 2); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $item['availability_status'] == 'AVAILABLE' ? 'success' : 'warning'; ?>">
                                                    <?php echo $item['availability_status']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="edit_item.php?id=<?php echo $item['item_id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                                <a href="item.php?id=<?php echo $item['item_id']; ?>" class="btn btn-sm btn-outline-info">View</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- My Rentals -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">My Rentals</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($my_rentals)): ?>
                        <p class="text-muted text-center">You haven't rented any items yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Owner</th>
                                        <th>Dates</th>
                                        <th>Total Cost</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($my_rentals as $rental): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($rental['item_name']); ?></td>
                                            <td><?php echo htmlspecialchars($rental['owner_name']); ?></td>
                                            <td>
                                                <?php 
                                                echo date('M d, Y', strtotime($rental['start_date'])) . ' - ' . 
                                                     date('M d, Y', strtotime($rental['end_date']));
                                                ?>
                                            </td>
                                            <td>$<?php echo number_format($rental['total_cost'], 2); ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $rental['status'] == 'ACTIVE' ? 'success' : 
                                                         ($rental['status'] == 'COMPLETED' ? 'info' : 'warning'); 
                                                ?>">
                                                    <?php echo $rental['status']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="rental_details.php?id=<?php echo $rental['transaction_id']; ?>" 
                                                   class="btn btn-sm btn-outline-primary">Details</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
