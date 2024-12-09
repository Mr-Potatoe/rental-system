<?php
require_once 'config/database.php';
require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    // Basic validation
    if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
        $error = 'All required fields must be filled out';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } else {
        $db = new Database();
        $user = new User($db->getConnection());
        
        if ($user->register($username, $email, $password, $full_name, $phone, $address)) {
            $success = 'Registration successful! You can now login.';
        } else {
            $error = 'Username or email already exists';
        }
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Register</div>
                <div class="card-body">
                    <?php if($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <?php if($success): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label>Username*</label>
                            <input type="text" name="username" class="form-control" required 
                                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label>Email*</label>
                            <input type="email" name="email" class="form-control" required
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label>Password*</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Confirm Password*</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Full Name*</label>
                            <input type="text" name="full_name" class="form-control" required
                                   value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label>Phone Number</label>
                            <input type="tel" name="phone" class="form-control"
                                   value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label>Address</label>
                            <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Register</button>
                        <a href="login.php" class="btn btn-link">Already have an account? Login</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
