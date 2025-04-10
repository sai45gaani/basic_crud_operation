<?php
// dashboard.php
include_once 'config/Database.php';
include_once 'models/Entry.php';
include_once 'models/User.php';
include_once 'includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

// Get entry statistics
$entry = new Entry($conn);
$stats = $entry->getStats();
$chartData = $entry->getChartData();

// Get user count
$user = new User($conn);
$users_result = $user->readAll();
$user_count = $users_result->num_rows;

// Get recent entries
$entries_result = $entry->readAll();
$recent_entries = [];
$counter = 0;
while ($row = $entries_result->fetch_assoc()) {
    if ($counter < 5) {
        $recent_entries[] = $row;
    }
    $counter++;
}

include_once 'includes/header.php';
?>

<h1 class="mb-4">Dashboard</h1>

<div class="row">
    <div class="col-md-4">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <div class="card-title">Total Entries</div>
                <div class="card-value"><?php echo number_format($stats['total_entries']); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <div class="card-title">Total Credit</div>
                <div class="card-value"><?php echo number_format($stats['total_credit'], 2); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <div class="card-title">Total Debit</div>
                <div class="card-value"><?php echo number_format($stats['total_debit'], 2); ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Credit vs Debit Trend</h5>
            </div>
            <div class="card-body">
                <canvas id="entriesChart" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Statistics by Currency</h5>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <?php foreach ($stats['entries_by_currency'] as $currency): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php echo $currency['currency']; ?>
                        <span class="badge bg-primary rounded-pill"><?php echo $currency['count']; ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Recent Entries</h5>
            </div>
            <div class="card-body">
                <?php if (count($recent_entries) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Account</th>
                                <th>Narration</th>
                                <th>Currency</th>
                                <th>Credit</th>
                                <th>Debit</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_entries as $entry): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($entry['account']); ?></td>
                                <td><?php echo htmlspecialchars($entry['narration']); ?></td>
                                <td><?php echo htmlspecialchars($entry['currency']); ?></td>
                                <td><?php echo number_format($entry['credit'], 2); ?></td>
                                <td><?php echo number_format($entry['debit'], 2); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($entry['created_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-center">No entries found. <a href="entries/add.php">Add your first entry</a>.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize chart with PHP data
document.addEventListener('DOMContentLoaded', function() {
    const chartContainer = document.getElementById('entriesChart');
    if (chartContainer) {
        const ctx = chartContainer.getContext('2d');
        
        // Prepare data from PHP
        const chartData = <?php echo json_encode($chartData); ?>;
        const labels = chartData.map(item => item.date);
        const creditData = chartData.map(item => item.total_credit);
        const debitData = chartData.map(item => item.total_debit);
        
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Credit',
                        data: creditData,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1,
                        fill: true
                    },
                    {
                        label: 'Debit',
                        data: debitData,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        tension: 0.1,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>

<?php include_once 'includes/footer.php'; ?>