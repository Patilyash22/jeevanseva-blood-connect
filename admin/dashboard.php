
<?php
require_once '../config.php';
include 'includes/header.php';

// Get counts for dashboard widgets
$donor_count = 0;
$pending_donors = 0;
$testimonial_count = 0;
$pending_testimonials = 0;

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
            <i class="fas fa-comment-dots"></i>
        </div>
        <div class="value"><?php echo $testimonial_count; ?></div>
        <div class="label">Testimonials</div>
    </div>
    
    <div class="widget widget-stat">
        <div class="icon" style="background-color: #4299e1;">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="value"><?php echo $pending_testimonials; ?></div>
        <div class="label">Pending Reviews</div>
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
</div>

<?php include 'includes/footer.php'; ?>
