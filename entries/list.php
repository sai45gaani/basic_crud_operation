<?php
// entries/list.php
include_once '../config/Database.php';
include_once '../models/Entry.php';
include_once '../includes/functions.php';

// Check if user is logged in
requireLogin();

$db = new Database();
$conn = $db->getConnection();

// Create entry object
$entry = new Entry($conn);

// Get all entries
$result = $entry->readAll();

include_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Entries</h1>
    <a href="add.php" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> New Entry
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
                        <th>Account</th>
                        <th>Narration</th>
                        <th>Currency</th>
                        <th>Credit</th>
                        <th>Debit</th>
                        <th>Created By</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['account']); ?></td>
                        <td><?php echo htmlspecialchars($row['narration']); ?></td>
                        <td><?php echo htmlspecialchars($row['currency']); ?></td>
                        <td><?php echo number_format($row['credit'], 2); ?></td>
                        <td><?php echo number_format($row['debit'], 2); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>
                        <td class="table-actions">
                            <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger delete-btn">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-center">No entries found. <a href="add.php">Add your first entry</a>.</p>
        <?php endif; ?>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>