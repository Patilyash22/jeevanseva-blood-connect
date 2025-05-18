
<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php?redirect=find-donor.php');
}

// Check if donor ID is provided
if (!isset($_GET['id'])) {
    redirect('find-donor.php');
}

$donor_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Check if user has paid to view this donor
$sql = "SELECT * FROM credits WHERE user_id = $user_id AND reference_id = $donor_id AND transaction_type = 'view_donor'";
$result = mysqli_query($conn, $sql);
$has_paid = ($result && mysqli_num_rows($result) > 0);

// If user hasn't paid and the donor isn't in session, redirect
if (!$has_paid && !isset($_SESSION['view_donor'])) {
    redirect('find-donor.php');
}

// Get donor information
$donor = null;
if (isset($_SESSION['view_donor']) && $_SESSION['view_donor']['id'] == $donor_id) {
    $donor = $_SESSION['view_donor'];
    unset($_SESSION['view_donor']); // Clear from session after retrieving
} else {
    $sql = "SELECT * FROM donors WHERE id = $donor_id";
    $result = mysqli_query($conn, $sql);
    $donor = mysqli_fetch_assoc($result);
}

// If donor doesn't exist, redirect
if (!$donor) {
    redirect('find-donor.php');
}

include 'includes/header.php';
?>

<section class="view-donor-section">
    <div class="container">
        <div class="back-link">
            <a href="find-donor.php"><i class="fas fa-arrow-left"></i> Back to Search</a>
        </div>
        
        <div class="donor-profile">
            <div class="donor-profile-header">
                <div class="donor-avatar">
                    <?php echo strtoupper(substr($donor['name'], 0, 1)); ?>
                </div>
                <div class="donor-name-info">
                    <h1><?php echo $donor['name']; ?></h1>
                    <div class="donor-blood-group"><?php echo $donor['blood_group']; ?></div>
                </div>
            </div>
            
            <div class="donor-contact-info">
                <h2>Contact Information</h2>
                <p class="contact-note">You have been charged credits to access this information.</p>
                
                <div class="contact-details">
                    <div class="contact-item">
                        <div class="contact-label">
                            <i class="fas fa-envelope"></i> Email:
                        </div>
                        <div class="contact-value"><?php echo $donor['email']; ?></div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-label">
                            <i class="fas fa-phone"></i> Phone:
                        </div>
                        <div class="contact-value">
                            <?php echo $donor['phone']; ?>
                            <a href="tel:<?php echo $donor['phone']; ?>" class="btn btn-sm btn-primary">Call</a>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-label">
                            <i class="fas fa-map-marker-alt"></i> Location:
                        </div>
                        <div class="contact-value"><?php echo $donor['location']; ?></div>
                    </div>
                </div>
            </div>
            
            <div class="donor-details">
                <h2>Donor Details</h2>
                
                <div class="details-grid">
                    <div class="details-item">
                        <div class="details-label">Age</div>
                        <div class="details-value"><?php echo $donor['age']; ?> years</div>
                    </div>
                    
                    <div class="details-item">
                        <div class="details-label">Weight</div>
                        <div class="details-value"><?php echo $donor['weight']; ?> kg</div>
                    </div>
                    
                    <div class="details-item">
                        <div class="details-label">Last Donation</div>
                        <div class="details-value">
                            <?php 
                            echo $donor['last_donation_date'] ? date('M d, Y', strtotime($donor['last_donation_date'])) : 'Not Available'; 
                            ?>
                        </div>
                    </div>
                    
                    <div class="details-item">
                        <div class="details-label">Medical Conditions</div>
                        <div class="details-value">
                            <?php echo !empty($donor['medical_conditions']) ? $donor['medical_conditions'] : 'None reported'; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="donor-actions">
                <a href="contact_donor.php?id=<?php echo $donor_id; ?>" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Send Message
                </a>
                
                <a href="report-donor.php?id=<?php echo $donor_id; ?>" class="btn btn-outline">
                    <i class="fas fa-flag"></i> Report Issue
                </a>
            </div>
            
            <div class="donation-guidelines">
                <h3>Before Contacting the Donor</h3>
                <ul>
                    <li>Be respectful of the donor's time and willingness to help.</li>
                    <li>Clearly explain the urgency and nature of your blood requirement.</li>
                    <li>Provide details about the hospital or blood bank where donation is needed.</li>
                    <li>Remember that donors must wait at least 3 months between donations.</li>
                    <li>Always verify the donor's eligibility before arranging a donation.</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
