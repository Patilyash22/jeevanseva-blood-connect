
<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Check if user is admin (admins should use admin dashboard)
if (isAdmin()) {
    redirect('admin/dashboard.php');
}

// Get user information
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Get recipient information
$sql = "SELECT * FROM recipients WHERE user_id = $user_id";
$result = mysqli_query($conn, $sql);
$recipient = mysqli_fetch_assoc($result);

// Get credit transactions
$sql = "SELECT * FROM credits WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 5";
$result = mysqli_query($conn, $sql);
$credit_transactions = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $credit_transactions[] = $row;
    }
}

// Get referrals
$sql = "SELECT r.*, u.username FROM referrals r 
        JOIN users u ON r.referred_id = u.id 
        WHERE r.referrer_id = $user_id 
        ORDER BY r.created_at DESC LIMIT 5";
$result = mysqli_query($conn, $sql);
$referrals = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $referrals[] = $row;
    }
}

// Get blood request history
$sql = "SELECT * FROM blood_requests WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 5";
$result = mysqli_query($conn, $sql);
$blood_requests = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $blood_requests[] = $row;
    }
}

// Count total successful referrals
$sql = "SELECT COUNT(*) as total FROM referrals WHERE referrer_id = $user_id AND status = 'completed'";
$result = mysqli_query($conn, $sql);
$referral_count = 0;
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $referral_count = $row['total'];
}

include 'includes/header.php';
?>

<section class="dashboard-section">
    <div class="container">
        <div class="dashboard-header">
            <div class="user-welcome">
                <h1>Welcome, <?php echo $user['username']; ?>!</h1>
                <p>Manage your account, find donors, and track your activity.</p>
            </div>
            <div class="credit-display">
                <div class="credit-amount"><?php echo $user['credits']; ?></div>
                <div class="credit-label">Credits</div>
                <a href="buy-credits.php" class="btn btn-sm btn-outline">Buy Credits</a>
            </div>
        </div>
        
        <div class="dashboard-stats">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-search"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo count($blood_requests); ?></div>
                    <div class="stat-label">Blood Requests</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $referral_count; ?></div>
                    <div class="stat-label">Referrals</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo array_sum(array_column($credit_transactions, 'amount')); ?></div>
                    <div class="stat-label">Total Credits Earned</div>
                </div>
            </div>
        </div>
        
        <div class="dashboard-actions">
            <a href="find-donor.php" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-search"></i>
                </div>
                <div class="action-content">
                    <h3>Find a Donor</h3>
                    <p>Search for blood donors by location and blood group.</p>
                </div>
            </a>
            
            <a href="request-blood.php" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <div class="action-content">
                    <h3>Request Blood</h3>
                    <p>Create an emergency blood request for urgent needs.</p>
                </div>
            </a>
            
            <a href="refer-friend.php" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-share-alt"></i>
                </div>
                <div class="action-content">
                    <h3>Refer a Friend</h3>
                    <p>Share your referral code and earn 10 credits per signup.</p>
                </div>
            </a>
        </div>
        
        <div class="dashboard-grid">
            <!-- Referral Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2>Your Referral Code</h2>
                </div>
                <div class="card-body">
                    <div class="referral-box">
                        <div class="referral-code"><?php echo $user['referral_code']; ?></div>
                        <p>Share this code with friends to earn 10 credits per signup!</p>
                        
                        <div class="referral-actions">
                            <button class="btn btn-secondary btn-sm copy-btn" data-clipboard-text="<?php echo $user['referral_code']; ?>">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                            
                            <a href="https://wa.me/?text=Join%20JeevanSeva%20blood%20donation%20platform%20with%20my%20referral%20code%20<?php echo $user['referral_code']; ?>%20and%20help%20save%20lives!%20Register%20at%20<?php echo $site_url; ?>/user-registration.php?ref=<?php echo $user['referral_code']; ?>" 
                               class="btn btn-secondary btn-sm" target="_blank">
                                <i class="fab fa-whatsapp"></i> Share
                            </a>
                        </div>
                    </div>
                    
                    <?php if (!empty($referrals)): ?>
                        <div class="referral-list">
                            <h3>Recent Referrals</h3>
                            <ul>
                                <?php foreach ($referrals as $referral): ?>
                                    <li>
                                        <span class="referral-name"><?php echo $referral['username']; ?></span>
                                        <span class="referral-date"><?php echo date('M d, Y', strtotime($referral['created_at'])); ?></span>
                                        <span class="referral-status <?php echo $referral['status']; ?>"><?php echo ucfirst($referral['status']); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <a href="referrals.php" class="card-link">View all referrals</a>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <p>You haven't referred anyone yet. Share your code to start earning credits!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Credit Transactions -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2>Credit History</h2>
                </div>
                <div class="card-body">
                    <?php if (!empty($credit_transactions)): ?>
                        <div class="transaction-list">
                            <ul>
                                <?php foreach ($credit_transactions as $transaction): ?>
                                    <li class="transaction-item">
                                        <div class="transaction-details">
                                            <div class="transaction-type">
                                                <?php 
                                                $icon_class = '';
                                                switch ($transaction['transaction_type']) {
                                                    case 'initial':
                                                        $icon_class = 'fa-star';
                                                        break;
                                                    case 'view_donor':
                                                        $icon_class = 'fa-eye';
                                                        break;
                                                    case 'referral':
                                                        $icon_class = 'fa-user-plus';
                                                        break;
                                                    case 'admin_adjustment':
                                                        $icon_class = 'fa-wrench';
                                                        break;
                                                    case 'purchase':
                                                        $icon_class = 'fa-shopping-cart';
                                                        break;
                                                    default:
                                                        $icon_class = 'fa-exchange-alt';
                                                }
                                                ?>
                                                <i class="fas <?php echo $icon_class; ?>"></i>
                                            </div>
                                            <div>
                                                <div class="transaction-description"><?php echo $transaction['description']; ?></div>
                                                <div class="transaction-date"><?php echo date('M d, Y H:i', strtotime($transaction['created_at'])); ?></div>
                                            </div>
                                        </div>
                                        <div class="transaction-amount <?php echo $transaction['amount'] >= 0 ? 'positive' : 'negative'; ?>">
                                            <?php echo ($transaction['amount'] >= 0 ? '+' : '') . $transaction['amount']; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <a href="credit-history.php" class="card-link">View complete history</a>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <p>No credit transactions yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Blood Request History -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2>Blood Request History</h2>
                </div>
                <div class="card-body">
                    <?php if (!empty($blood_requests)): ?>
                        <div class="request-list">
                            <ul>
                                <?php foreach ($blood_requests as $request): ?>
                                    <li>
                                        <div class="request-details">
                                            <div class="blood-group"><?php echo $request['blood_group']; ?></div>
                                            <div>
                                                <div class="request-location"><?php echo $request['location']; ?></div>
                                                <div class="request-date"><?php echo date('M d, Y', strtotime($request['created_at'])); ?></div>
                                            </div>
                                        </div>
                                        <div class="request-status <?php echo $request['status']; ?>">
                                            <?php echo ucfirst($request['status']); ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <a href="blood-requests.php" class="card-link">View all requests</a>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <p>No blood requests yet.</p>
                            <a href="request-blood.php" class="btn btn-primary btn-sm">Create Request</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Recent Donors View -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2>Recently Viewed Donors</h2>
                </div>
                <div class="card-body">
                    <?php
                    // Get recently viewed donors
                    $sql = "SELECT d.id, d.name, d.blood_group, d.location, d.last_donation_date, c.created_at as view_date 
                            FROM credits c 
                            JOIN donors d ON c.reference_id = d.id 
                            WHERE c.user_id = $user_id AND c.transaction_type = 'view_donor' 
                            ORDER BY c.created_at DESC LIMIT 5";
                    $result = mysqli_query($conn, $sql);
                    $viewed_donors = [];
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $viewed_donors[] = $row;
                        }
                    }
                    ?>
                    
                    <?php if (!empty($viewed_donors)): ?>
                        <div class="donor-list">
                            <ul>
                                <?php foreach ($viewed_donors as $donor): ?>
                                    <li>
                                        <div class="donor-details">
                                            <div class="donor-blood-group"><?php echo $donor['blood_group']; ?></div>
                                            <div>
                                                <div class="donor-name"><?php echo $donor['name']; ?></div>
                                                <div class="donor-location"><?php echo $donor['location']; ?></div>
                                            </div>
                                        </div>
                                        <div class="donor-view-date">
                                            <?php echo date('M d, Y', strtotime($donor['view_date'])); ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <a href="donor-history.php" class="card-link">View all</a>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <p>You haven't viewed any donor profiles yet.</p>
                            <a href="find-donor.php" class="btn btn-primary btn-sm">Find Donors</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Include clipboard.js for copy functionality -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize clipboard.js
        var clipboard = new ClipboardJS('.copy-btn');
        
        clipboard.on('success', function(e) {
            // Add a temporary "Copied!" text or effect
            var originalText = e.trigger.innerHTML;
            e.trigger.innerHTML = '<i class="fas fa-check"></i> Copied!';
            
            // Restore original button text after a short delay
            setTimeout(function() {
                e.trigger.innerHTML = originalText;
            }, 2000);
            
            e.clearSelection();
        });
    });
</script>

<?php include 'includes/footer.php'; ?>
