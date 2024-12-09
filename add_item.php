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
$item = new Item($db->getConnection());
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = $_POST['category'] ?? '';
    $daily_rate = floatval($_POST['daily_rate'] ?? 0);
    $deposit_amount = floatval($_POST['deposit_amount'] ?? 0);
    $location = trim($_POST['location'] ?? '');
    $condition = $_POST['condition'] ?? '';
    $insurance_required = isset($_POST['insurance_required']) ? 1 : 0;
    $max_rental_duration = intval($_POST['max_rental_duration'] ?? 0);

    if (empty($name) || empty($description) || empty($category) || $daily_rate <= 0 || $deposit_amount <= 0) {
        $error = 'Please fill in all required fields';
    } else {
        $result = $item->addItem([
            'owner_id' => $_SESSION['user_id'],
            'name' => $name,
            'description' => $description,
            'category' => $category,
            'daily_rate' => $daily_rate,
            'deposit_amount' => $deposit_amount,
            'location' => $location,
            'condition' => $condition,
            'insurance_required' => $insurance_required,
            'max_rental_duration' => $max_rental_duration
        ]);

        if ($result) {
            $success = 'Item added successfully';
        } else {
            $error = 'Error adding item';
        }
    }
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Add New Item</h4>
                </div>
                <div class="card-body">
                    <?php if($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <?php if($success): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label>Item Name *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Description *</label>
                            <textarea name="description" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Category *</label>
                            <select name="category" class="form-control" required>
                                <option value="">Select Category</option>
                                <option value="Electronics">Electronics</option>
                                <option value="Sports">Sports</option>
                                <option value="Tools">Tools</option>
                                <option value="Events">Events</option>
                                <option value="Water Sports">Water Sports</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Daily Rate ($) *</label>
                                <input type="number" name="daily_rate" class="form-control" step="0.01" min="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Deposit Amount ($) *</label>
                                <input type="number" name="deposit_amount" class="form-control" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Location *</label>
                            <input type="text" name="location" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Condition *</label>
                            <select name="condition" class="form-control" required>
                                <option value="">Select Condition</option>
                                <option value="Excellent">Excellent</option>
                                <option value="Very Good">Very Good</option>
                                <option value="Good">Good</option>
                                <option value="Fair">Fair</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="insurance_required" class="form-check-input" id="insurance">
                                <label class="form-check-label" for="insurance">Insurance Required</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Maximum Rental Duration (days)</label>
                            <input type="number" name="max_rental_duration" class="form-control" min="1" value="7">
                        </div>
                        <button type="submit" class="btn btn-primary">Add Item</button>
                        <a href="dashboard.php" class="btn btn-link">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 