<?php
require_once 'config/database.php';
require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db = new Database();
$user = new User($db->getConnection());
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!empty($new_password)) {
        if ($new_password !== $confirm_password) {
            $error = 'New passwords do not match';
        } elseif (strlen($new_password) < 8) {
            $error = 'Password must be at least 8 characters long';
        } elseif (!$user->verifyPassword($_SESSION['user_id'], $current_password)) {
            $error = 'Current password is incorrect';
        }
    }

    if (empty($error)) {
        if ($user->updateProfile($_SESSION['user_id'], $full_name, $email, $phone, $address, $new_password)) {
            $success = 'Profile updated successfully';
            $_SESSION['full_name'] = $full_name;
        } else {
            $error = 'Error updating profile';
        }
    }
}

$user_data = $user->getUserById($_SESSION['user_id']);
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Profile Settings</h4>
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
                            <label>Full Name</label>
                            <input type="text" name="full_name" class="form-control" required
                                   value="<?php echo htmlspecialchars($user_data['full_name']); ?>">
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required
                                   value="<?php echo htmlspecialchars($user_data['email']); ?>">
                        </div>
                        <div class="mb-3">
                            <label>Phone Number</label>
                            <input type="tel" name="phone" class="form-control"
                                   value="<?php echo htmlspecialchars($user_data['phone_number']); ?>">
                        </div>
                        <div class="mb-3">
                            <label>Address</label>
                            <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars($user_data['address']); ?></textarea>
                        </div>

                        <h5 class="mt-4">Change Password</h5>
                        <div class="mb-3">
                            <label>Current Password</label>
                            <input type="password" name="current_password" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>New Password</label>
                            <input type="password" name="new_password" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 