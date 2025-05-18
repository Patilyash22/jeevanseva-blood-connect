
<?php
require_once 'config.php';

// Initialize search variables
$location = isset($_GET['location']) ? sanitize($_GET['location']) : '';
$blood_group = isset($_GET['blood_group']) ? sanitize($_GET['blood_group']) : '';
$error = '';
$success = '';
$donors = [];
$search_performed = false;

// Get donor view cost from settings
$donor_view_cost = 2; // Default value
$sql = "SELECT setting_value FROM settings WHERE setting_name = 'donor_view_cost'";
$result = mysqli_query($conn, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $donor_view_cost = (int)$row['setting_value'];
}

// Handle donor search
if (isset($_GET['search'])) {
    $search_performed = true;
    
    // Build search query
    $sql = "SELECT id, name, blood_group, location, age, last_donation_date FROM donors WHERE status = 'active'";
    
    if (!empty($location)) {
        $sql .= " AND location LIKE '%$location%'";
    }
    
    if (!empty($blood_group)) {
        $sql .= " AND blood_group = '$blood_group'";
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $donors[] = $row;
        }
    }
}

// Handle view donor contact
if (isset($_POST['view_contact']) && isset($_POST['donor_id'])) {
    // Check if user is logged in
    if (!isLoggedIn()) {
        redirect('login.php?redirect=find-donor.php');
    }
    
    $donor_id = (int)$_POST['donor_id'];
    $user_id = $_SESSION['user_id'];
    
    // Check if user has enough credits
    if ($_SESSION['credits'] < $donor_view_cost) {
        $error = "You don't have enough credits to view this donor's contact information. Please buy more credits.";
    } else {
        // Check if already paid for this donor
        $sql = "SELECT * FROM credits WHERE user_id = $user_id AND reference_id = $donor_id AND transaction_type = 'view_donor'";
        $result = mysqli_query($conn, $sql);
        $already_paid = ($result && mysqli_num_rows($result) > 0);
        
        if ($already_paid) {
            // Get donor contact info without charging
            $sql = "SELECT * FROM donors WHERE id = $donor_id";
            $result = mysqli_query($conn, $sql);
            $donor = mysqli_fetch_assoc($result);
            
            if ($donor) {
                $_SESSION['view_donor'] = $donor;
                redirect('view-donor.php?id=' . $donor_id);
            } else {
                $error = "Donor not found.";
            }
        } else {
            // Deduct credits
            $description = "Viewed contact info for donor #$donor_id";
            $credit_result = processCredits($user_id, -$donor_view_cost, 'view_donor', $description, $donor_id);
            
            if ($credit_result) {
                // Get donor contact info
                $sql = "SELECT * FROM donors WHERE id = $donor_id";
                $result = mysqli_query($conn, $sql);
                $donor = mysqli_fetch_assoc($result);
                
                if ($donor) {
                    $_SESSION['view_donor'] = $donor;
                    redirect('view-donor.php?id=' . $donor_id);
                } else {
                    // This shouldn't happen, but just in case
                    $error = "Donor not found.";
                    // Refund credits since donor wasn't found
                    processCredits($user_id, $donor_view_cost, 'admin_adjustment', 'Refund for non-existent donor', $donor_id);
                }
            } else {
                $error = "Error processing credits. Please try again.";
            }
        }
    }
}

include 'includes/header.php';
?>

<section class="find-donor-section">
    <div class="container">
        <h1>Find Blood Donors</h1>
        <p class="section-intro">Search for blood donors based on location and blood group.</p>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="search-form-container">
            <form method="GET" class="search-form">
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" value="<?php echo $location; ?>" placeholder="Enter city, district, or area">
                </div>
                
                <div class="form-group">
                    <label for="blood_group">Blood Group</label>
                    <select id="blood_group" name="blood_group">
                        <option value="">Any Blood Group</option>
                        <option value="A+" <?php echo $blood_group === 'A+' ? 'selected' : ''; ?>>A+</option>
                        <option value="A-" <?php echo $blood_group === 'A-' ? 'selected' : ''; ?>>A-</option>
                        <option value="B+" <?php echo $blood_group === 'B+' ? 'selected' : ''; ?>>B+</option>
                        <option value="B-" <?php echo $blood_group === 'B-' ? 'selected' : ''; ?>>B-</option>
                        <option value="AB+" <?php echo $blood_group === 'AB+' ? 'selected' : ''; ?>>AB+</option>
                        <option value="AB-" <?php echo $blood_group === 'AB-' ? 'selected' : ''; ?>>AB-</option>
                        <option value="O+" <?php echo $blood_group === 'O+' ? 'selected' : ''; ?>>O+</option>
                        <option value="O-" <?php echo $blood_group === 'O-' ? 'selected' : ''; ?>>O-</option>
                    </select>
                </div>
                
                <div class="form-buttons">
                    <button type="submit" name="search" class="btn btn-primary">Search</button>
                </div>
            </form>
        </div>
        
        <?php if (isLoggedIn()): ?>
            <div class="credit-info">
                <p>You have <strong><?php echo $_SESSION['credits']; ?> credits</strong>. Viewing a donor's contact information costs <strong><?php echo $donor_view_cost; ?> credits</strong>.</p>
                <a href="buy-credits.php" class="btn btn-sm btn-outline">Buy Credits</a>
            </div>
        <?php endif; ?>
        
        <?php if ($search_performed): ?>
            <div class="search-results">
                <h2><?php echo count($donors); ?> Donors Found</h2>
                
                <?php if (count($donors) > 0): ?>
                    <div class="donor-grid">
                        <?php foreach ($donors as $donor): ?>
                            <div class="donor-card">
                                <div class="donor-top">
                                    <div class="donor-blood-group"><?php echo $donor['blood_group']; ?></div>
                                    <div class="donor-location">
                                        <i class="fas fa-map-marker-alt"></i> <?php echo $donor['location']; ?>
                                    </div>
                                </div>
                                
                                <div class="donor-details">
                                    <h3><?php echo substr($donor['name'], 0, 1) . '****'; ?></h3>
                                    <div class="donor-info">
                                        <p><strong>Age:</strong> <?php echo $donor['age']; ?> years</p>
                                        <p><strong>Last Donation:</strong> 
                                            <?php 
                                            echo $donor['last_donation_date'] ? date('M d, Y', strtotime($donor['last_donation_date'])) : 'Not Available'; 
                                            ?>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="donor-footer">
                                    <form method="POST">
                                        <input type="hidden" name="donor_id" value="<?php echo $donor['id']; ?>">
                                        <button type="submit" name="view_contact" class="btn btn-primary btn-block">
                                            <i class="fas fa-eye"></i> View Contact (<?php echo $donor_view_cost; ?> credits)
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-search"></i>
                        <p>No donors found matching your criteria. Try broadening your search.</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="blood-request-cta">
            <h3>Can't find a suitable donor?</h3>
            <p>Post a blood request and let donors contact you.</p>
            <a href="request-blood.php" class="btn btn-primary">Post Blood Request</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
