
<?php
require_once 'config.php';
require_once 'app/Services/UserCreditService.php';

// Initialize search variables
$location = isset($_GET['location']) ? sanitize($_GET['location']) : '';
$blood_group = isset($_GET['blood_group']) ? sanitize($_GET['blood_group']) : '';
$error = '';
$success = '';
$donors = [];
$search_performed = false;

// Get donor view cost from settings
$donor_view_cost = (int)getSetting('donor_view_cost', 2);

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
    
    // Get user
    $sql = "SELECT * FROM users WHERE id = $user_id";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);
    
    $creditService = new App\Services\UserCreditService();
    
    // Check if already paid for this donor
    if ($creditService->hasViewedDonor($user, $donor_id)) {
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
        // Check if user has enough credits
        if (!$creditService->hasEnoughCredits($user, $donor_view_cost)) {
            $error = "You don't have enough credits to view this donor's contact information. Please buy more credits.";
        } else {
            // Deduct credits
            $success = $creditService->chargeDonorView($user, $donor_id);
            
            if ($success) {
                // Update session credits
                $_SESSION['credits'] = $user['credits'] - $donor_view_cost;
                
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
                    $creditService->processCredits($user, $donor_view_cost, 'admin_adjustment', 'Refund for non-existent donor', $donor_id);
                    $_SESSION['credits'] = $user['credits'] + $donor_view_cost;
                }
            } else {
                $error = "Error processing credits. Please try again.";
            }
        }
    }
}

include 'includes/header.php';
?>

<section class="find-donor-section bg-white py-8">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold mb-2 text-jeevanseva-darkred">Find Blood Donors</h1>
        <p class="section-intro text-jeevanseva-gray mb-6">Search for blood donors based on location and blood group.</p>
        
        <?php if ($error): ?>
            <div class="alert alert-danger bg-red-100 text-red-700 p-4 rounded mb-6"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success bg-green-100 text-green-700 p-4 rounded mb-6"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="search-form-container bg-white p-6 rounded-lg shadow-lg mb-8">
            <form method="GET" class="search-form">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="form-group col-span-1 md:col-span-2">
                        <label for="location" class="block text-gray-700 font-medium mb-2">Location</label>
                        <input type="text" id="location" name="location" value="<?php echo $location; ?>" placeholder="Enter city, district, or area" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-jeevanseva-red focus:border-jeevanseva-red">
                    </div>
                    
                    <div class="form-group">
                        <label for="blood_group" class="block text-gray-700 font-medium mb-2">Blood Group</label>
                        <select id="blood_group" name="blood_group" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-jeevanseva-red focus:border-jeevanseva-red">
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
                    
                    <div class="form-buttons col-span-1 md:col-span-3">
                        <button type="submit" name="search" class="bg-jeevanseva-red hover:bg-jeevanseva-darkred text-white py-2 px-6 rounded-md font-medium transition">
                            <i class="fas fa-search mr-2"></i> Search Donors
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <?php if (isLoggedIn()): ?>
            <div class="credit-info bg-blue-50 p-4 rounded-lg mb-8 flex flex-wrap justify-between items-center">
                <p class="font-medium">
                    You have <span class="text-jeevanseva-red font-bold"><?php echo $_SESSION['credits']; ?> credits</span>. 
                    Viewing a donor's contact information costs <span class="text-jeevanseva-red font-bold"><?php echo $donor_view_cost; ?> credits</span>.
                </p>
                <a href="buy-credits.php" class="mt-2 md:mt-0 bg-jeevanseva-red hover:bg-jeevanseva-darkred text-white px-4 py-2 rounded-md text-sm font-medium transition">
                    <i class="fas fa-coins mr-1"></i> Buy Credits
                </a>
            </div>
        <?php endif; ?>
        
        <?php if ($search_performed): ?>
            <div class="search-results">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-800"><?php echo count($donors); ?> Donors Found</h2>
                    
                    <?php if (!empty($donors)): ?>
                        <div class="text-sm text-gray-500">
                            <?php if (!empty($blood_group)): ?>
                                <span class="inline-block px-2 py-1 bg-jeevanseva-light text-jeevanseva-darkred rounded-full mr-2">
                                    <i class="fas fa-tint mr-1"></i> <?php echo $blood_group; ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if (!empty($location)): ?>
                                <span class="inline-block px-2 py-1 bg-jeevanseva-light text-jeevanseva-darkred rounded-full">
                                    <i class="fas fa-map-marker-alt mr-1"></i> <?php echo $location; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if (count($donors) > 0): ?>
                    <div class="donor-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($donors as $donor): ?>
                            <div class="donor-card bg-white shadow-md rounded-lg overflow-hidden border border-gray-200 hover:shadow-lg transition">
                                <div class="donor-top p-4 bg-jeevanseva-light flex justify-between items-center">
                                    <div class="donor-blood-group inline-block px-3 py-1 rounded-full bg-jeevanseva-red text-white font-semibold">
                                        <?php echo $donor['blood_group']; ?>
                                    </div>
                                    <div class="donor-location text-gray-600 text-sm">
                                        <i class="fas fa-map-marker-alt mr-1"></i> <?php echo $donor['location']; ?>
                                    </div>
                                </div>
                                
                                <div class="donor-details p-4">
                                    <h3 class="text-lg font-semibold mb-2 flex items-center">
                                        <span class="bg-gray-200 text-gray-700 rounded-full w-8 h-8 flex items-center justify-center mr-2">
                                            <?php echo strtoupper(substr($donor['name'], 0, 1)); ?>
                                        </span>
                                        <?php echo substr($donor['name'], 0, 1) . '****'; ?>
                                    </h3>
                                    <div class="donor-info grid grid-cols-2 gap-2 text-sm">
                                        <div>
                                            <span class="text-gray-500">Age:</span> 
                                            <span class="font-medium"><?php echo $donor['age']; ?> years</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Last Donation:</span> 
                                            <span class="font-medium">
                                                <?php 
                                                echo $donor['last_donation_date'] ? date('M d, Y', strtotime($donor['last_donation_date'])) : 'Not Available'; 
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="donor-footer p-4 border-t border-gray-200">
                                    <form method="POST">
                                        <input type="hidden" name="donor_id" value="<?php echo $donor['id']; ?>">
                                        <button type="submit" name="view_contact" class="w-full bg-jeevanseva-red hover:bg-jeevanseva-darkred text-white py-2 px-4 rounded flex items-center justify-center transition">
                                            <i class="fas fa-eye mr-2"></i> View Contact (<?php echo $donor_view_cost; ?> credits)
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state bg-white p-12 rounded-lg shadow-md text-center">
                        <i class="fas fa-search text-jeevanseva-light text-5xl mb-4"></i>
                        <h3 class="text-xl font-medium text-gray-800 mb-2">No donors found</h3>
                        <p class="text-gray-600 mb-6">No donors found matching your criteria. Try broadening your search.</p>
                        <div class="space-y-2 max-w-md mx-auto text-sm text-left">
                            <p class="font-medium">Try:</p>
                            <ul class="list-disc pl-5 space-y-1 text-gray-600">
                                <li>Searching for a different location</li>
                                <li>Searching for any blood group</li>
                                <li>Using a broader location (e.g., city name instead of specific area)</li>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="blood-request-cta mt-8 bg-jeevanseva-light p-6 rounded-lg text-center">
            <h3 class="text-xl font-semibold mb-2 text-gray-800">Can't find a suitable donor?</h3>
            <p class="text-gray-600 mb-4">Post a blood request and let donors contact you.</p>
            <a href="request-blood.php" class="inline-block bg-jeevanseva-red hover:bg-jeevanseva-darkred text-white py-3 px-8 rounded-md font-medium transition">
                <i class="fas fa-plus-circle mr-2"></i> Post Blood Request
            </a>
        </div>
    </div>
</section>

<style>
.donor-card:hover {
    transform: translateY(-3px);
}
</style>

<?php include 'includes/footer.php'; ?>
