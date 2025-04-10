<?php
// users/add.php
include_once '../config/Database.php';
include_once '../models/User.php';
include_once '../includes/functions.php';

// Check if user is logged in
requireLogin();

$db = new Database();
$conn = $db->getConnection();

// Create user object
$user = new User($conn);

// Process form submission
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Set user property values
    $user->username = isset($_POST['username']) ? $_POST['username'] : '';
    $user->password = isset($_POST['password']) ? $_POST['password'] : '';
    $user->full_name = isset($_POST['full_name']) ? $_POST['full_name'] : '';
    
    // Validate required fields
    if (empty($user->username)) {
        $errors[] = 'Username is required';
    }
    if (empty($user->password)) {
        $errors[] = 'Password is required';
    }
    if (empty($user->full_name)) {
        $errors[] = 'Full Name is required';
    }
    
    // Create the user if no errors
    if (empty($errors)) {
        if ($user->create()) {
            $_SESSION['message'] = 'User created successfully';
            header('Location: list.php');
            exit();
        } else {
            $errors[] = 'Username already exists or unable to create user';
        }
    }
}

include_once '../includes/header.php';
?>

<h1 class="mb-4">Create New User</h1>

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
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                <div class="invalid-feedback">Please enter a username.</div>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <div class="invalid-feedback">Please enter a password.</div>
            </div>
            
            <div class="mb-3">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>" required>
                <div class="invalid-feedback">Please enter a full name.</div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="list.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create User</button>
            </div>
        </form>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>