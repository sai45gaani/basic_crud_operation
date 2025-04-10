<?php
// index.php (Login page)
include_once 'config/Database.php';
include_once 'models/User.php';
include_once 'includes/functions.php';

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

$user = new User($conn);
$error_msg = '';

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $user->username = $_POST['username'];
        $user->password = $_POST['password'];
        
        if ($user->login()) {
            // Set session and cookie
            $_SESSION['user_id'] = $user->id;
            $_SESSION['username'] = $user->username;
// index.php (Login page) - continued
            $_SESSION['full_name'] = $user->full_name;
            
            // Set cookie for 7 days
            setcookie('user_login', $user->username, time() + (7 * 24 * 60 * 60), '/');
            
            header("Location: dashboard.php");
            exit();
        } else {
            $error_msg = 'Invalid username or password.';
        }
    } else {
        $error_msg = 'Please enter both username and password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Simple CRUD Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="form-signin">
            <h1 class="text-center mb-4">CRUD Application</h1>
            
            <?php if(!empty($error_msg)): ?>
                <div class="alert alert-danger"><?php echo $error_msg; ?></div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-center">Login</h4>
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                            <div class="invalid-feedback">Please enter your username.</div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="invalid-feedback">Please enter your password.</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                        <div class="text-center mt-3">
                         <a href="register.php">Don't have an account? Register</a>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center text-muted">
                    Default: admin / admin123
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/script.js"></script>
</body>
</html>