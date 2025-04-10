<?php
// users/list.php
include_once '../config/Database.php';
include_once '../models/User.php';
include_once '../includes/functions.php';

// Check if user is logged in
requireLogin();

$db = new Database();
$conn = $db->getConnection();

// Create user object
$user = new User($conn);

// Get all users
$result = $user->readAll();

include_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Users</h1>
    <a href="add.php" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> New User
    </a>
</div>

<?php
// Check for messages from redirect
if (isset($_SESSION['message'])) {
    echo showSuccess($_SESSION['message']);
    unset($_SESSION['message']);
}
?>

<div class="card">
    <div class="card-body">
        <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>
                        <td class="table-actions">
                            <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php if ($_SESSION['user_id'] != $row['id']): // Prevent deleting self ?>
                            <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger delete-btn">
                                <i class="bi bi-trash"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-center">No users found. <a href="add.php">Add your first user</a>.</p>
        <?php endif; ?>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>