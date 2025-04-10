<?php
// users/edit.php
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
    // Set user property values
    $user->username = isset($_POST['username']) ? $_POST['username'] : '';
    $user->full_name = isset($_POST['full_name']) ? $_POST['full_name'] : '';
    $user->password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Validate required fields
    if (empty($user->username)) {
        $errors[] = 'Username is required';
    }
    if (empty($user->full_name)) {
        $errors[] = 'Full Name is required';
    }
    
    // Update the user if no errors
    if (empty($errors)) {
        if ($user->update()) {
            $_SESSION['message'] = 'User updated successfully';
            header('Location: list.php');
            exit();
        } else {
            $errors[] = 'Username already exists or unable to update user';
        }
    }
}

include_once '../includes/header.php';
?>

<h1 class="mb-4">Edit User</h1>

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
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $user->id; ?>" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user->username); ?>" required>
                <div class="invalid-feedback">Please enter a username.</div>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password">
                <div class="form-text">Leave blank to keep current password.</div>
            </div>
            
            <div class="mb-3">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user->full_name); ?>" required>
                <div class="invalid-feedback">Please enter a full name.</div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="list.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update User</button>
            </div>
        </form>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>