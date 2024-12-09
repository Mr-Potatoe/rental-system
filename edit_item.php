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

// Get item ID from URL
$item_id = intval($_GET['id'] ?? 0);

// Verify item exists and belongs to user
$item_data = $item->getItemById($item_id);
if (!$item_data || $item_data['owner_id'] != $_SESSION['user_id']) {
    header('Location: dashboard.php');
    exit;
}

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
    $availability_status = $_POST['availability_status'] ?? 'AVAILABLE';

    if (empty($name) || empty($description) || empty($category) || $daily_rate <= 0 || $deposit_amount <= 0) {
        $error = 'Please fill in all required fields';
    } else {
        $result = $item->updateItem($item_id, [
            'name' => $name,
            'description' => $description,
            'category' => $category,
            'daily_rate' => $daily_rate,
            'deposit_amount' => $deposit_amount,
            'location' => $location,
            'condition' => $condition,
            'insurance_required' => $insurance_required,
            'max_rental_duration' => $max_rental_duration,
            'availability_status' => $availability_status
        ]);

        if ($result) {
            $success = 'Item updated successfully';
            $item_data = $item->getItemById($item_id); // Refresh data
        } else {
            $error = 'Error updating item';
        }
    }
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Edit Item</h4>
                    <a href="dashboard.php" class="btn btn-link">Back to Dashboard</a>
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
                            <input type="text" name="name" class="form-control" required
                                   value="<?php echo htmlspecialchars($item_data['name']); ?>">
                        </div>
                        <div class="mb-3">
                            <label>Description *</label>
                            <textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($item_data['description']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Category *</label>
                            <select name="category" class="form-control" required>
                                <?php
                                $categories = ['Electronics', 'Sports', 'Tools', 'Events', 'Water Sports'];
                                foreach ($categories as $cat) {
                                    $selected = ($cat == $item_data['category']) ? 'selected' : '';
                                    echo "<option value=\"$cat\" $selected>$cat</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Daily Rate ($) *</label>
                                <input type="number" name="daily_rate" class="form-control" step="0.01" min="0" required
                                       value="<?php echo htmlspecialchars($item_data['daily_rate']); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Deposit Amount ($) *</label>
                                <input type="number" name="deposit_amount" class="form-control" step="0.01" min="0" required
                                       value="<?php echo htmlspecialchars($item_data['deposit_amount']); ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Location *</label>
                            <input type="text" name="location" class="form-control" required
                                   value="<?php echo htmlspecialchars($item_data['location']); ?>">
                        </div>
                        <div class="mb-3">
                            <label>Condition *</label>
                            <select name="condition" class="form-control" required>
                                <?php
                                $conditions = ['Excellent', 'Very Good', 'Good', 'Fair'];
                                foreach ($conditions as $cond) {
                                    $selected = ($cond == $item_data['condition']) ? 'selected' : '';
                                    echo "<option value=\"$cond\" $selected>$cond</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Availability Status *</label>
                            <select name="availability_status" class="form-control" required>
                                <?php
                                $statuses = ['AVAILABLE', 'UNAVAILABLE', 'MAINTENANCE'];
                                foreach ($statuses as $status) {
                                    $selected = ($status == $item_data['availability_status']) ? 'selected' : '';
                                    echo "<option value=\"$status\" $selected>$status</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="insurance_required" class="form-check-input" id="insurance"
                                       <?php echo $item_data['insurance_required'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="insurance">Insurance Required</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Maximum Rental Duration (days)</label>
                            <input type="number" name="max_rental_duration" class="form-control" min="1"
                                   value="<?php echo htmlspecialchars($item_data['max_rental_duration']); ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Update Item</button>
                        <a href="dashboard.php" class="btn btn-link">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 