
<?php
require_once 'config.php';
include 'includes/header.php';

$search_performed = false;
$donors = [];
$error_message = '';

// Process search form
if (isset($_GET['search'])) {
    $search_performed = true;
    
    $blood_group = isset($_GET['blood_group']) ? sanitize($_GET['blood_group']) : '';
    $location = isset($_GET['location']) ? sanitize($_GET['location']) : '';
    
    // Build the query
    $conditions = ["status = 'active'"];
    $params = [];
    
    if (!empty($blood_group)) {
        $conditions[] = "blood_group = '$blood_group'";
    }
    
    if (!empty($location)) {
        $conditions[] = "location LIKE '%$location%'";
    }
    
    $where_clause = implode(' AND ', $conditions);
    
    // Execute the query
    $sql = "SELECT * FROM donors WHERE $where_clause ORDER BY created_at DESC";
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $donors[] = $row;
            }
        } else {
            $error_message = "No donors found matching your criteria. Please try broadening your search.";
        }
    } else {
        $error_message = "Error performing search: " . mysqli_error($conn);
    }
}
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-center">Find Blood Donors</h1>
        
        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
            <form method="GET" class="donor-search-form">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div class="form-group">
                        <label for="blood_group">Blood Group</label>
                        <select id="blood_group" name="blood_group" class="form-control">
                            <option value="">Any Blood Group</option>
                            <option value="A+" <?php echo (isset($_GET['blood_group']) && $_GET['blood_group'] === 'A+') ? 'selected' : ''; ?>>A+</option>
                            <option value="A-" <?php echo (isset($_GET['blood_group']) && $_GET['blood_group'] === 'A-') ? 'selected' : ''; ?>>A-</option>
                            <option value="B+" <?php echo (isset($_GET['blood_group']) && $_GET['blood_group'] === 'B+') ? 'selected' : ''; ?>>B+</option>
                            <option value="B-" <?php echo (isset($_GET['blood_group']) && $_GET['blood_group'] === 'B-') ? 'selected' : ''; ?>>B-</option>
                            <option value="AB+" <?php echo (isset($_GET['blood_group']) && $_GET['blood_group'] === 'AB+') ? 'selected' : ''; ?>>AB+</option>
                            <option value="AB-" <?php echo (isset($_GET['blood_group']) && $_GET['blood_group'] === 'AB-') ? 'selected' : ''; ?>>AB-</option>
                            <option value="O+" <?php echo (isset($_GET['blood_group']) && $_GET['blood_group'] === 'O+') ? 'selected' : ''; ?>>O+</option>
                            <option value="O-" <?php echo (isset($_GET['blood_group']) && $_GET['blood_group'] === 'O-') ? 'selected' : ''; ?>>O-</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" placeholder="City, State" class="form-control"
                               value="<?php echo isset($_GET['location']) ? $_GET['location'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" name="search" class="btn btn-primary w-full">
                            <i class="fas fa-search mr-2"></i> Search Donors
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <?php if ($search_performed): ?>
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="p-4 border-b">
                    <h2 class="text-xl font-semibold">Search Results</h2>
                    <?php if (!empty($error_message)): ?>
                        <p class="text-jeevanseva-gray mt-2"><?php echo $error_message; ?></p>
                    <?php else: ?>
                        <p class="text-jeevanseva-gray mt-2">Found <?php echo count($donors); ?> donor(s) matching your criteria</p>
                    <?php endif; ?>
                </div>
                
                <?php if (count($donors) > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="w-full donor-results-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Blood Group</th>
                                    <th>Location</th>
                                    <th>Last Donated</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($donors as $donor): ?>
                                    <tr>
                                        <td><?php echo $donor['name']; ?></td>
                                        <td>
                                            <span class="blood-group-badge">
                                                <?php echo $donor['blood_group']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo $donor['location']; ?></td>
                                        <td>
                                            <?php 
                                                if (!empty($donor['last_donation_date'])) {
                                                    echo date('M d, Y', strtotime($donor['last_donation_date']));
                                                } else {
                                                    echo '<span class="text-gray-400">Not available</span>';
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline" onclick="showContactModal(<?php echo $donor['id']; ?>)">
                                                Contact
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="mt-6 p-4 bg-jeevanseva-light rounded-lg">
                <h3 class="text-lg font-semibold mb-2">Need more help?</h3>
                <p class="mb-3">If you couldn't find a suitable donor, please contact us directly and we'll try to help you find a match as soon as possible.</p>
                <a href="contact.php" class="inline-flex items-center text-jeevanseva-red hover:text-jeevanseva-darkred">
                    Contact us for emergency assistance
                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </a>
            </div>
        <?php else: ?>
            <div class="text-center p-8">
                <div class="text-5xl mb-4 text-jeevanseva-red">
                    <i class="fas fa-search"></i>
                </div>
                <h2 class="text-2xl font-bold mb-2">Find Donors Near You</h2>
                <p class="text-jeevanseva-gray max-w-lg mx-auto">Use the search form above to find blood donors in your area. You can search by blood group, location, or both.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Contact Modal (Hidden by default) -->
<div id="contact-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close-button" onclick="closeContactModal()">&times;</span>
        <h3>Contact Donor</h3>
        <p class="mb-4">To contact this donor, please provide your details and reason for needing blood. We'll connect you with the donor if they are available.</p>
        
        <form id="contact-form" method="POST" action="contact_donor.php">
            <input type="hidden" name="donor_id" id="donor_id">
            
            <div class="form-group">
                <label for="requester_name">Your Name</label>
                <input type="text" id="requester_name" name="requester_name" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="requester_phone">Your Phone</label>
                <input type="tel" id="requester_phone" name="requester_phone" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="requester_email">Your Email</label>
                <input type="email" id="requester_email" name="requester_email" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="request_reason">Reason for Request</label>
                <textarea id="request_reason" name="request_reason" required class="form-control" rows="3"></textarea>
            </div>
            
            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Submit Request</button>
                <button type="button" class="btn btn-outline" onclick="closeContactModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<style>
.donor-search-form {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.donor-results-table {
    width: 100%;
    border-collapse: collapse;
}

.donor-results-table th,
.donor-results-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
}

.donor-results-table th {
    background-color: #f9fafb;
    font-weight: 600;
    color: #4a5568;
}

.donor-results-table tr:hover {
    background-color: #f9fafb;
}

.blood-group-badge {
    display: inline-block;
    padding: 2px 8px;
    background-color: #e53e3e;
    color: white;
    border-radius: 9999px;
    font-weight: 500;
    font-size: 0.875rem;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

.btn-outline {
    background-color: transparent;
    border: 1px solid #e53e3e;
    color: #e53e3e;
}

.btn-outline:hover {
    background-color: #e53e3e;
    color: white;
}

/* Modal Styles */
.modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    width: 90%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
}

.close-button {
    position: absolute;
    right: 15px;
    top: 15px;
    font-size: 24px;
    cursor: pointer;
    color: #718096;
}

.close-button:hover {
    color: #4a5568;
}

.form-buttons {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 20px;
}
</style>

<script>
function showContactModal(donorId) {
    document.getElementById('donor_id').value = donorId;
    document.getElementById('contact-modal').style.display = 'flex';
    document.body.style.overflow = 'hidden'; // Prevent scrolling
}

function closeContactModal() {
    document.getElementById('contact-modal').style.display = 'none';
    document.body.style.overflow = 'auto'; // Re-enable scrolling
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('contact-modal');
    if (event.target === modal) {
        closeContactModal();
    }
}
</script>

<?php include 'includes/footer.php'; ?>
