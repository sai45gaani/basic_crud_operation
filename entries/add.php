<?php
// entries/add.php
include_once '../config/Database.php';
include_once '../models/Entry.php';
include_once '../includes/functions.php';

// Check if user is logged in
requireLogin();

$db = new Database();
$conn = $db->getConnection();

// Create entry object
$entry = new Entry($conn);

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
    $entry->user_id = $_SESSION['user_id'];
    
    // Validate required fields
    if (empty($entry->account)) {
        $errors[] = 'Account is required';
    }
    if (empty($entry->currency)) {
        $errors[] = 'Currency is required';
    }
    
    // Create the entry if no errors
    if (empty($errors)) {
        if ($entry->create()) {
            $_SESSION['message'] = 'Entry created successfully';
            header('Location: list.php');
            exit();
        } else {
            $errors[] = 'Unable to create entry';
        }
    }
}

include_once '../includes/header.php';
?>

<h1 class="mb-4">Create New Entry</h1>

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
                <label for="account" class="form-label">Account</label>
                <input type="text" class="form-control" id="account" name="account" value="<?php echo isset($_POST['account']) ? htmlspecialchars($_POST['account']) : ''; ?>" required>
                <div class="invalid-feedback">Please enter an account name.</div>
            </div>
            
            <div class="mb-3">
                <label for="narration" class="form-label">Narration</label>
                <textarea class="form-control" id="narration" name="narration" rows="3"><?php echo isset($_POST['narration']) ? htmlspecialchars($_POST['narration']) : ''; ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="currency" class="form-label">Currency</label>
                <select class="form-select" id="currency" name="currency" required>
                    <option value="">Select a currency</option>
                    <option value="USD" <?php echo (isset($_POST['currency']) && $_POST['currency'] === 'USD') ? 'selected' : ''; ?>>USD</option>
                    <option value="EUR" <?php echo (isset($_POST['currency']) && $_POST['currency'] === 'EUR') ? 'selected' : ''; ?>>EUR</option>
                    <option value="GBP" <?php echo (isset($_POST['currency']) && $_POST['currency'] === 'GBP') ? 'selected' : ''; ?>>GBP</option>
                    <option value="JPY" <?php echo (isset($_POST['currency']) && $_POST['currency'] === 'JPY') ? 'selected' : ''; ?>>JPY</option>
                    <option value="INR" <?php echo (isset($_POST['currency']) && $_POST['currency'] === 'INR') ? 'selected' : ''; ?>>INR</option>
                </select>
                <div class="invalid-feedback">Please select a currency.</div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="credit" class="form-label">Credit</label>
                        <input type="number" class="form-control" id="credit" name="credit" step="0.01" min="0" value="<?php echo isset($_POST['credit']) ? htmlspecialchars($_POST['credit']) : '0.00'; ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="debit" class="form-label">Debit</label>
                        <input type="number" class="form-control" id="debit" name="debit" step="0.01" min="0" value="<?php echo isset($_POST['debit']) ? htmlspecialchars($_POST['debit']) : '0.00'; ?>">
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="list.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Entry</button>
            </div>
        </form>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>