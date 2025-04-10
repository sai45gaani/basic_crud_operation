<?php
// audit/list.php
include_once '../config/Database.php';
include_once '../models/Audit.php';
include_once '../includes/functions.php';

// Check if user is logged in
requireLogin();

$db = new Database();
$conn = $db->getConnection();

// Create audit object
$audit = new Audit($conn);

// Get all audit logs
$result = $audit->readAll();

include_once '../includes/header.php';
?>

<h1 class="mb-4">Audit Logs</h1>

<div class="card">
    <div class="card-body">
        <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Table</th>
                        <th>Field</th>
                        <th>Old Value</th>
                        <th>New Value</th>
                        <th>User</th>
                        <th>Entry ID</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['table_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['field_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['old_value']); ?></td>
                        <td><?php echo htmlspecialchars($row['new_value']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td>
                            <?php if ($row['entry_id']): ?>
                                <a href="../entries/edit.php?id=<?php echo $row['entry_id']; ?>">
                                    <?php echo $row['entry_id']; ?>
                                </a>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-center">No audit logs found.</p>
        <?php endif; ?>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>