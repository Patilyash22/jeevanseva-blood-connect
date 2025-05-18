
<?php
require_once '../config.php';
include 'includes/header.php';

// Get counts for dashboard widgets
$donor_count = 0;
$pending_donors = 0;
$testimonial_count = 0;
$pending_testimonials = 0;
$user_count = 0;
$blood_request_count = 0;
$total_credits_issued = 0;
$total_credits_used = 0;

// Count total donors
$sql = "SELECT COUNT(*) as total FROM donors";
$result = mysqli_query($conn, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $donor_count = $row['total'];
}

// Count pending donors
$sql = "SELECT COUNT(*) as total FROM donors WHERE status = 'pending'";
$result = mysqli_query($conn, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $pending_donors = $row['total'];
}

// Count total testimonials
$sql = "SELECT COUNT(*) as total FROM testimonials";
$result = mysqli_query($conn, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $testimonial_count = $row['total'];
}

// Count pending testimonials
$sql = "SELECT COUNT(*) as total FROM testimonials WHERE is_approved = 0";
$result = mysqli_query($conn, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $pending_testimonials = $row['total'];
}

// Count total users (excluding admins)
$sql = "SELECT COUNT(*) as total FROM users WHERE is_admin = 0";
$result = mysqli_query($conn, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $user_count = $row['total'];
}

// Count total blood requests
$sql = "SELECT COUNT(*) as total FROM blood_requests";
$result = mysqli_query($conn, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $blood_request_count = $row['total'];
}

// Calculate total credits issued
$sql = "SELECT SUM(amount) as total FROM credits WHERE amount > 0";
$result = mysqli_query($conn, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $total_credits_issued = $row['total'] ?? 0;
}

// Calculate total credits used
$sql = "SELECT SUM(ABS(amount)) as total FROM credits WHERE amount < 0";
$result = mysqli_query($conn, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $total_credits_used = $row['total'] ?? 0;
}

// Blood inventory (for demonstration purposes)
$blood_inventory = [
    ['group' => 'A+', 'level' => 75],
    ['group' => 'A-', 'level' => 40],
    ['group' => 'B+', 'level' => 60],
    ['group' => 'B-', 'level' => 25],
    ['group' => 'AB+', 'level' => 50],
    ['group' => 'AB-', 'level' => 30],
    ['group' => 'O+', 'level' => 85],
    ['group' => 'O-', 'level' => 15],
];

// Get recent donors
$recent_donors = [];
$sql = "SELECT * FROM donors ORDER BY created_at DESC LIMIT 5";
$result = mysqli_query($conn, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $recent_donors[] = $row;
    }
}

// Get recent blood requests
$recent_requests = [];
$sql = "SELECT br.*, u.username FROM blood_requests br 
        JOIN users u ON br.user_id = u.id
        ORDER BY br.created_at DESC LIMIT 5";
$result = mysqli_query($conn, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $recent_requests[] = $row;
    }
}
?>

<!-- Dashboard Widgets -->
<div class="widget-container">
    <div class="widget widget-stat">
        <div class="icon" style="background-color: #e53e3e;">
            <i class="fas fa-heart"></i>
        </div>
        <div class="value"><?php echo $donor_count; ?></div>
        <div class="label">Total Donors</div>
    </div>
    
    <div class="widget widget-stat">
        <div class="icon" style="background-color: #f6ad55;">
            <i class="fas fa-clock"></i>
        </div>
        <div class="value"><?php echo $pending_donors; ?></div>
        <div class="label">Pending Approvals</div>
    </div>
    
    <div class="widget widget-stat">
        <div class="icon" style="background-color: #38a169;">
            <i class="fas fa-users"></i>
        </div>
        <div class="value"><?php echo $user_count; ?></div>
        <div class="label">Registered Users</div>
    </div>
    
    <div class="widget widget-stat">
        <div class="icon" style="background-color: #4299e1;">
            <i class="fas fa-heartbeat"></i>
        </div>
        <div class="value"><?php echo $blood_request_count; ?></div>
        <div class="label">Blood Requests</div>
    </div>
    
    <div class="widget widget-stat">
        <div class="icon" style="background-color: #805ad5;">
            <i class="fas fa-coins"></i>
        </div>
        <div class="value"><?php echo $total_credits_issued; ?></div>
        <div class="label">Credits Issued</div>
    </div>
    
    <div class="widget widget-stat">
        <div class="icon" style="background-color: #dd6b20;">
            <i class="fas fa-exchange-alt"></i>
        </div>
        <div class="value"><?php echo $total_credits_used; ?></div>
        <div class="label">Credits Used</div>
    </div>
</div>

<div class="row">
    <!-- Blood Inventory Card -->
    <div class="card">
        <div class="card-header">
            <h2>Blood Inventory Status</h2>
        </div>
        <div class="card-body">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <?php foreach ($blood_inventory as $item): ?>
                    <div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <strong>Blood Group <?php echo $item['group']; ?></strong>
                            <span><?php echo $item['level']; ?>%</span>
                        </div>
                        <div style="height: 8px; background-color: #e2e8f0; border-radius: 4px; overflow: hidden;">
                            <?php
                            $color = "#4299e1"; // Default blue
                            if ($item['level'] < 30) {
                                $color = "#e53e3e"; // Red for low
                            } else if ($item['level'] < 60) {
                                $color = "#f6ad55"; // Orange for medium
                            } else {
                                $color = "#38a169"; // Green for good
                            }
                            ?>
                            <div style="height: 100%; width: <?php echo $item['level']; ?>%; background-color: <?php echo $color; ?>; border-radius: 4px;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Recent Donors Table -->
    <div class="card">
        <div class="card-header">
            <h2>Recent Donors</h2>
            <a href="manage_donors.php" class="btn btn-primary">View All</a>
        </div>
        <div class="card-body">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Blood Group</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Date Registered</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($recent_donors) > 0): ?>
                        <?php foreach ($recent_donors as $donor): ?>
                            <tr>
                                <td><?php echo $donor['name']; ?></td>
                                <td><?php echo $donor['blood_group']; ?></td>
                                <td><?php echo $donor['location']; ?></td>
                                <td>
                                    <?php
                                    $status_class = '';
                                    switch ($donor['status']) {
                                        case 'active':
                                            $status_class = 'status-active';
                                            break;
                                        case 'inactive':
                                            $status_class = 'status-inactive';
                                            break;
                                        case 'pending':
                                            $status_class = 'status-pending';
                                            break;
                                    }
                                    ?>
                                    <span class="status-pill <?php echo $status_class; ?>">
                                        <?php echo ucfirst($donor['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($donor['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">No donors found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Blood Requests Table -->
    <div class="card">
        <div class="card-header">
            <h2>Recent Blood Requests</h2>
            <a href="manage_requests.php" class="btn btn-primary">View All</a>
        </div>
        <div class="card-body">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Requester</th>
                        <th>Blood Group</th>
                        <th>Location</th>
                        <th>Urgency</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($recent_requests) > 0): ?>
                        <?php foreach ($recent_requests as $request): ?>
                            <tr>
                                <td><?php echo $request['username']; ?></td>
                                <td><?php echo $request['blood_group']; ?></td>
                                <td><?php echo $request['location']; ?></td>
                                <td>
                                    <?php
                                    $urgency_class = '';
                                    switch ($request['urgency']) {
                                        case 'normal':
                                            $urgency_class = 'urgency-normal';
                                            break;
                                        case 'urgent':
                                            $urgency_class = 'urgency-urgent';
                                            break;
                                        case 'critical':
                                            $urgency_class = 'urgency-critical';
                                            break;
                                    }
                                    ?>
                                    <span class="urgency-pill <?php echo $urgency_class; ?>">
                                        <?php echo ucfirst($request['urgency']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $status_class = '';
                                    switch ($request['status']) {
                                        case 'open':
                                            $status_class = 'status-pending';
                                            break;
                                        case 'fulfilled':
                                            $status_class = 'status-active';
                                            break;
                                        case 'closed':
                                            $status_class = 'status-inactive';
                                            break;
                                    }
                                    ?>
                                    <span class="status-pill <?php echo $status_class; ?>">
                                        <?php echo ucfirst($request['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($request['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No blood requests found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Credit Statistics Card -->
    <div class="card">
        <div class="card-header">
            <h2>Credit System Statistics</h2>
        </div>
        <div class="card-body">
            <?php
            // Get credit statistics
            $credit_stats = [
                ['label' => 'Initial Credits', 'value' => 0],
                ['label' => 'Referral Credits', 'value' => 0],
                ['label' => 'Purchase Credits', 'value' => 0],
                ['label' => 'Admin Adjustments', 'value' => 0]
            ];
            
            // Get credits by type
            $sql = "SELECT transaction_type, SUM(amount) as total FROM credits WHERE amount > 0 GROUP BY transaction_type";
            $result = mysqli_query($conn, $sql);
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    switch ($row['transaction_type']) {
                        case 'initial':
                            $credit_stats[0]['value'] = $row['total'];
                            break;
                        case 'referral':
                            $credit_stats[1]['value'] = $row['total'];
                            break;
                        case 'purchase':
                            $credit_stats[2]['value'] = $row['total'];
                            break;
                        case 'admin_adjustment':
                            $credit_stats[3]['value'] = $row['total'];
                            break;
                    }
                }
            }
            
            // Calculate percentages
            $total_credits = array_sum(array_column($credit_stats, 'value'));
            if ($total_credits > 0) {
                foreach ($credit_stats as &$stat) {
                    $stat['percent'] = round(($stat['value'] / $total_credits) * 100);
                }
            }
            ?>
            
            <div class="credit-stats">
                <?php foreach ($credit_stats as $stat): ?>
                    <div class="stat-item">
                        <div class="stat-header">
                            <div class="stat-label"><?php echo $stat['label']; ?></div>
                            <div class="stat-value"><?php echo $stat['value']; ?></div>
                        </div>
                        <div class="stat-progress">
                            <div class="progress-bar" style="width: <?php echo $stat['percent'] ?? 0; ?>%"></div>
                        </div>
                        <div class="stat-percent"><?php echo $stat['percent'] ?? 0; ?>%</div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="stat-summary">
                <div class="summary-item">
                    <div class="summary-label">Total Credits Issued</div>
                    <div class="summary-value"><?php echo $total_credits_issued; ?></div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Total Credits Used</div>
                    <div class="summary-value"><?php echo $total_credits_used; ?></div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Current Balance</div>
                    <div class="summary-value"><?php echo $total_credits_issued - $total_credits_used; ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add some custom styling for the new stats -->
<style>
.widget-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.urgency-pill {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    text-align: center;
}

.urgency-normal {
    background-color: #e2e8f0;
    color: #4a5568;
}

.urgency-urgent {
    background-color: #fbd38d;
    color: #744210;
}

.urgency-critical {
    background-color: #feb2b2;
    color: #822727;
}

.credit-stats {
    margin-bottom: 20px;
}

.stat-item {
    margin-bottom: 12px;
}

.stat-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
}

.stat-progress {
    height: 6px;
    background-color: #e2e8f0;
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 5px;
}

.progress-bar {
    height: 100%;
    background-color: var(--admin-primary);
    border-radius: 3px;
}

.stat-percent {
    text-align: right;
    font-size: 12px;
    color: #718096;
}

.stat-summary {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e2e8f0;
}

.summary-item {
    text-align: center;
}

.summary-label {
    font-size: 14px;
    color: #718096;
    margin-bottom: 5px;
}

.summary-value {
    font-size: 18px;
    font-weight: 600;
}
</style>

<?php include 'includes/footer.php'; ?>
