<?php
// entries/edit.php
include_once '../config/Database.php';
include_once '../models/Entry.php';
include_once '../models/Audit.php';
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

// Create entry object
$entry = new Entry($conn);
$entry->id = $_GET['id'];

// Get entry details
if (!$entry->readOne()) {
    $_SESSION['message'] = 'Entry not found';
    header('Location: list.php');
    exit();
}

// Get audit logs for this entry
$audit = new Audit($conn);
$audit_logs = $audit->readByEntry($entry->id);

// Process form submission
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Set entry property values
    $entry->account = isset($_POST['account']) ? $_POST['account'] : '';
    $entry->narration = isset($_POST['narration']) ? $_POST['narration'] : '';
    $entry->currency = isset($_POST['currency']) ? $_POST['currency'] : '';
    $entry->credit = isset($_POST['credit']) ? $_POST['credit'] : 0;
    $entry->debit = isset($_POST['debit']) ? $_POST['debit'] : 0;
    
    // Validate required fields
    if (empty($entry->account)) {
        $errors[] = 'Account is required';
    }
    if (empty($entry->currency)) {
        $errors[] = 'Currency is required';
    }
    
    // Update the entry if no errors
    if (empty($errors)) {
        if ($entry->update()) {
            $_SESSION['message'] = 'Entry updated successfully';
            header('Location: list.php');
            exit();
        } else {
            $errors[] = 'Unable to update entry';
        }
    }
}

include_once '../includes/header.php';
?>

<h1 class="mb-4">Edit Entry</h1>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-body">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $entry->id; ?>" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="account" class="form-label">Account</label>
                        <input type="text" class="form-control" id="account" name="account" value="<?php echo htmlspecialchars($entry->account); ?>" required>
                        <div class="invalid-feedback">Please enter an account name.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="narration" class="form-label">Narration</label>
                        <textarea class="form-control" id="narration" name="narration" rows="3"><?php echo htmlspecialchars($entry->narration); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="currency" class="form-label">Currency</label>
                        <select class="form-select" id="currency" name="currency" required>
                            <option value="">Select a currency</option>
                            <option value="USD" <?php echo ($entry->currency === 'USD') ? 'selected' : ''; ?>>USD</option>
                            <option value="EUR" <?php echo ($entry->currency === 'EUR') ? 'selected' : ''; ?>>EUR</option>
                            <option value="GBP" <?php echo ($entry->currency === 'GBP') ? 'selected' : ''; ?>>GBP</option>
                            <option value="JPY" <?php echo ($entry->currency === 'JPY') ? 'selected' : ''; ?>>JPY</option>
                            <option value="INR" <?php echo ($entry->currency === 'INR') ? 'selected' : ''; ?>>INR</option>
                        </select>
                        <div class="invalid-feedback">Please select a currency.</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="credit" class="form-label">Credit</label>
                                <input type="number" class="form-control" id="credit" name="credit" step="0.01" min="0" value="<?php echo htmlspecialchars($entry->credit); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="debit" class="form-label">Debit</label>
                                <input type="number" class="form-control" id="debit" name="debit" step="0.01" min="0" value="<?php echo htmlspecialchars($entry->debit); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="list.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Entry</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Audit Logs</h5>
            </div>
            <div class="card-body p-0">
                <?php if ($audit_logs && $audit_logs->num_rows > 0): ?>
                <div class="list-group list-group-flush">
                    <?php while($log = $audit_logs->fetch_assoc()): ?>
                    <div class="list-group-item">
                        <small class="text-muted d-block">
                            <?php echo date('Y-m-d H:i', strtotime($log['created_at'])); ?> by <?php echo htmlspecialchars($log['username']); ?>
                        </small>
                        <strong><?php echo htmlspecialchars($log['field_name']); ?></strong>: 
                        <?php echo htmlspecialchars($log['old_value']); ?> → <?php echo htmlspecialchars($log['new_value']); ?>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php else: ?>
                <p class="text-center p-3">No audit logs found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>