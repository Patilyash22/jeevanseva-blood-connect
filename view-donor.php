
<?php
require_once 'config.php';
require_once 'app/Services/UserCreditService.php';

// Check if user is logged in
if (!isLoggedIn()) {
    setMessage("Please login to view donor details", "warning");
    redirect('login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

// Check if donor ID is provided
if (!isset($_GET['id'])) {
    redirect('find-donor.php');
}

$donor_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Get user and create credit service
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

$creditService = new App\Services\UserCreditService();

// Check if user has paid to view this donor
$has_paid = $creditService->hasViewedDonor($user, $donor_id);

// Process payment if user hasn't paid
if (!$has_paid && !isset($_SESSION['view_donor'])) {
    // Check if user has enough credits
    $donor_view_cost = (int)getSetting('donor_view_cost', 2);
    
    if (!$creditService->hasEnoughCredits($user, $donor_view_cost)) {
        setMessage("You don't have enough credits to view this donor. Please purchase more credits.", "danger");
        redirect('buy-credits.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    }
    
    $sql = "SELECT * FROM donors WHERE id = $donor_id";
    $result = mysqli_query($conn, $sql);
    $donor = mysqli_fetch_assoc($result);
    
    if (!$donor) {
        setMessage("Donor not found", "danger");
        redirect('find-donor.php');
    }
    
    // Process credit deduction
    $success = $creditService->chargeDonorView($user, $donor_id);
    
    if (!$success) {
        setMessage("Failed to process credits. Please try again.", "danger");
        redirect('find-donor.php');
    }
    
    // Update session credits
    $_SESSION['credits'] = $user['credits'] - $donor_view_cost;
    
    // Store donor in session for this view only
    $_SESSION['view_donor'] = $donor;
    
    // Record view in analytics
    $sql = "INSERT INTO donor_views (donor_id, user_id, viewed_at) VALUES ($donor_id, $user_id, NOW())";
    mysqli_query($conn, $sql);
}

// Get donor information
$donor = null;
if (isset($_SESSION['view_donor']) && $_SESSION['view_donor']['id'] == $donor_id) {
    $donor = $_SESSION['view_donor'];
    unset($_SESSION['view_donor']); // Clear from session after retrieving
} else if ($has_paid) {
    $sql = "SELECT * FROM donors WHERE id = $donor_id";
    $result = mysqli_query($conn, $sql);
    $donor = mysqli_fetch_assoc($result);
} else {
    redirect('find-donor.php');
}

// If donor doesn't exist, redirect
if (!$donor) {
    setMessage("Donor not found", "danger");
    redirect('find-donor.php');
}

include 'includes/header.php';
?>

<section class="view-donor-section bg-white">
    <div class="container mx-auto px-4 py-8">
        <div class="back-link mb-4">
            <a href="find-donor.php" class="text-jeevanseva-red hover:text-jeevanseva-darkred flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to Search
            </a>
        </div>
        
        <div class="donor-profile bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="donor-profile-header bg-jeevanseva-light p-6 flex items-center border-b border-gray-200">
                <div class="donor-avatar bg-jeevanseva-red text-white rounded-full w-16 h-16 flex items-center justify-center text-2xl font-bold mr-4">
                    <?php echo strtoupper(substr($donor['name'], 0, 1)); ?>
                </div>
                <div class="donor-name-info">
                    <h1 class="text-2xl font-bold text-gray-800"><?php echo $donor['name']; ?></h1>
                    <div class="donor-blood-group inline-block px-3 py-1 rounded-full bg-jeevanseva-red text-white font-semibold mt-1"><?php echo $donor['blood_group']; ?></div>
                </div>
            </div>
            
            <div class="donor-contact-info p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Contact Information</h2>
                <p class="contact-note text-sm bg-blue-50 text-blue-700 p-3 rounded mb-4 flex items-center">
                    <i class="fas fa-info-circle mr-2"></i> You have been charged <?php echo getSetting('donor_view_cost', 2); ?> credits to access this information.
                </p>
                
                <div class="contact-details grid md:grid-cols-2 gap-4">
                    <div class="contact-item p-3 bg-gray-50 rounded">
                        <div class="contact-label text-gray-500 mb-1">
                            <i class="fas fa-envelope mr-2"></i> Email:
                        </div>
                        <div class="contact-value font-medium"><?php echo $donor['email']; ?></div>
                    </div>
                    
                    <div class="contact-item p-3 bg-gray-50 rounded">
                        <div class="contact-label text-gray-500 mb-1">
                            <i class="fas fa-phone mr-2"></i> Phone:
                        </div>
                        <div class="contact-value font-medium flex items-center justify-between">
                            <span><?php echo $donor['phone']; ?></span>
                            <a href="tel:<?php echo $donor['phone']; ?>" class="btn btn-sm bg-jeevanseva-red text-white px-3 py-1 rounded hover:bg-jeevanseva-darkred transition">
                                <i class="fas fa-phone-alt mr-1"></i> Call
                            </a>
                        </div>
                    </div>
                    
                    <div class="contact-item p-3 bg-gray-50 rounded md:col-span-2">
                        <div class="contact-label text-gray-500 mb-1">
                            <i class="fas fa-map-marker-alt mr-2"></i> Location:
                        </div>
                        <div class="contact-value font-medium"><?php echo $donor['location']; ?></div>
                    </div>
                </div>
            </div>
            
            <div class="donor-details p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Donor Details</h2>
                
                <div class="details-grid grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="details-item p-4 bg-gray-50 rounded text-center">
                        <div class="details-label text-gray-500 text-sm mb-1">Age</div>
                        <div class="details-value font-semibold text-lg"><?php echo $donor['age']; ?> years</div>
                    </div>
                    
                    <div class="details-item p-4 bg-gray-50 rounded text-center">
                        <div class="details-label text-gray-500 text-sm mb-1">Weight</div>
                        <div class="details-value font-semibold text-lg"><?php echo $donor['weight']; ?> kg</div>
                    </div>
                    
                    <div class="details-item p-4 bg-gray-50 rounded text-center">
                        <div class="details-label text-gray-500 text-sm mb-1">Last Donation</div>
                        <div class="details-value font-semibold text-lg">
                            <?php 
                            echo $donor['last_donation_date'] ? date('M d, Y', strtotime($donor['last_donation_date'])) : 'Not Available'; 
                            ?>
                        </div>
                    </div>
                    
                    <div class="details-item p-4 bg-gray-50 rounded text-center">
                        <div class="details-label text-gray-500 text-sm mb-1">Status</div>
                        <div class="details-value">
                            <span class="inline-block px-3 py-1 text-sm font-semibold bg-green-100 text-green-800 rounded-full">
                                Available
                            </span>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($donor['medical_conditions'])): ?>
                <div class="medical-conditions mt-4 p-4 bg-gray-50 rounded">
                    <div class="details-label text-gray-500 text-sm mb-1">Medical Conditions</div>
                    <div class="details-value"><?php echo $donor['medical_conditions']; ?></div>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="donor-actions p-6 flex flex-col sm:flex-row gap-4">
                <a href="contact_donor.php?id=<?php echo $donor_id; ?>" class="btn btn-primary bg-jeevanseva-red hover:bg-jeevanseva-darkred text-white px-6 py-3 rounded flex-1 text-center flex items-center justify-center">
                    <i class="fas fa-paper-plane mr-2"></i> Send Message
                </a>
                
                <a href="report-donor.php?id=<?php echo $donor_id; ?>" class="btn btn-outline border border-jeevanseva-gray hover:bg-gray-100 text-jeevanseva-gray px-6 py-3 rounded flex-1 text-center flex items-center justify-center">
                    <i class="fas fa-flag mr-2"></i> Report Issue
                </a>
            </div>
            
            <div class="donation-guidelines p-6 bg-jeevanseva-light">
                <h3 class="text-lg font-semibold mb-3 text-gray-800">Before Contacting the Donor</h3>
                <ul class="list-disc pl-5 space-y-2 text-gray-600">
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
