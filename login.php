<?php
require_once 'config/database.php';
require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'includes/header.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        $db = new Database();
        $user = new User($db->getConnection());
        
        if ($userData = $user->login($username, $password)) {
            $_SESSION['user_id'] = $userData['user_id'];
            $_SESSION['username'] = $userData['username'];
            $_SESSION['full_name'] = $userData['full_name'];
            
            // Store last login time
            $_SESSION['last_login'] = date('Y-m-d H:i:s');
            
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password';
        }
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Login</div>
                <div class="card-body">
                    <?php if($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required
                                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Login</button>
                        <a href="register.php" class="btn btn-link">Don't have an account? Register</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
