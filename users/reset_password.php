<?php
// users/reset_password.php
include_once '../config/Database.php';
include_once '../models/User.php';
include_once '../includes/functions.php';

// Check if user is logged in
requireLogin();

$db = new Database();
$conn = $db->getConnection();

// Check if id parameter exists
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: list.php');
    exit();
}

// Create user object
$user = new User($conn);
$user->id = $_GET['id'];

// Get user details
if (!$user->readOne()) {
    $_SESSION['message'] = 'User not found';
    header('Location: list.php');
    exit();
}

// Process form submission
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Set new password
    $user->password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    
    // Validate required fields
    if (empty($user->password)) {
        $errors[] = 'Password is required';
    }
    if ($user->password !== $confirm_password) {
        $errors[] = 'Passwords do not match';
    }
    
    // Update password if no errors
    if (empty($errors)) {
        if ($user->resetPassword()) {
            $_SESSION['message'] = 'Password reset successfully';
            header('Location: list.php');
            exit();
        } else {
            $errors[] = 'Unable to reset password';
        }
    }
}

include_once '../includes/header.php';
?>

<h1 class="mb-4">Reset Password</h1>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <p>Resetting password for user: <strong><?php echo htmlspecialchars($user->username); ?></strong></p>
        
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $user->id; ?>" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <div class="invalid-feedback">Please enter a new password.</div>
            </div>
            
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                <div class="invalid-feedback">Please confirm your password.</div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="list.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Reset Password</button>
            </div>
        </form>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>