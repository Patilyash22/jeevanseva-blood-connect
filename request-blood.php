
<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    setMessage("Please log in to create a blood request", "info");
    redirect('login.php?redirect=request-blood.php');
}

// Initialize variables
$blood_group = '';
$location = '';
$hospital_name = '';
$patient_name = '';
$urgency = 'normal';
$units_required = 1;
$contact_phone = '';
$additional_info = '';
$is_public = true;
$errors = [];
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $blood_group = sanitize($_POST['blood_group']);
    $location = sanitize($_POST['location']);
    $hospital_name = sanitize($_POST['hospital_name']);
    $patient_name = sanitize($_POST['patient_name']);
    $urgency = sanitize($_POST['urgency']);
    $units_required = (int)$_POST['units_required'];
    $contact_phone = sanitize($_POST['contact_phone']);
    $additional_info = sanitize($_POST['additional_info']);
    $is_public = isset($_POST['is_public']) ? 1 : 0;
    
    // Validate input
    if (empty($blood_group)) {
        $errors[] = "Blood group is required";
    }
    
    if (empty($location)) {
        $errors[] = "Location is required";
    }
    
    if (empty($contact_phone)) {
        $errors[] = "Contact phone is required";
    }
    
    if ($units_required < 1 || $units_required > 10) {
        $errors[] = "Units required must be between 1 and 10";
    }
    
    // If no errors, create the request
    if (empty($errors)) {
        $user_id = $_SESSION['user_id'];
        
        $sql = "INSERT INTO blood_requests (user_id, blood_group, location, hospital_name, patient_name, urgency, 
                                           units_required, contact_phone, additional_info, is_public) 
                VALUES ($user_id, '$blood_group', '$location', '$hospital_name', '$patient_name', '$urgency', 
                        $units_required, '$contact_phone', '$additional_info', $is_public)";
        
        if (mysqli_query($conn, $sql)) {
            $success = true;
            $request_id = mysqli_insert_id($conn);
        } else {
            $errors[] = "Error creating blood request: " . mysqli_error($conn);
        }
    }
}

include 'includes/header.php';
?>

<section class="request-blood-section">
    <div class="container">
        <h1>Request Blood</h1>
        <p class="section-intro">Create a blood donation request for patients in need.</p>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <h3>Blood Request Created Successfully!</h3>
                <p>Your request has been posted. Donors matching your requirements will be notified.</p>
                <div class="alert-actions">
                    <a href="blood-requests.php" class="btn btn-primary">View Your Requests</a>
                    <a href="find-donor.php" class="btn btn-outline">Find Donors</a>
                </div>
            </div>
        <?php else: ?>
            <form method="POST" class="blood-request-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="blood_group">Blood Group Required*</label>
                        <select id="blood_group" name="blood_group" required>
                            <option value="">Select Blood Group</option>
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
                    
                    <div class="form-group">
                        <label for="location">Location*</label>
                        <input type="text" id="location" name="location" value="<?php echo $location; ?>" placeholder="City, District, Area" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="hospital_name">Hospital Name</label>
                        <input type="text" id="hospital_name" name="hospital_name" value="<?php echo $hospital_name; ?>" placeholder="Hospital or Blood Bank Name">
                    </div>
                    
                    <div class="form-group">
                        <label for="patient_name">Patient Name</label>
                        <input type="text" id="patient_name" name="patient_name" value="<?php echo $patient_name; ?>" placeholder="Name of the Patient">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="urgency">Urgency Level</label>
                        <select id="urgency" name="urgency">
                            <option value="normal" <?php echo $urgency === 'normal' ? 'selected' : ''; ?>>Normal</option>
                            <option value="urgent" <?php echo $urgency === 'urgent' ? 'selected' : ''; ?>>Urgent</option>
                            <option value="critical" <?php echo $urgency === 'critical' ? 'selected' : ''; ?>>Critical</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="units_required">Units Required</label>
                        <select id="units_required" name="units_required">
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo $units_required === $i ? 'selected' : ''; ?>><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="contact_phone">Contact Phone*</label>
                    <input type="text" id="contact_phone" name="contact_phone" value="<?php echo $contact_phone; ?>" placeholder="Enter your contact number" required>
                </div>
                
                <div class="form-group">
                    <label for="additional_info">Additional Information</label>
                    <textarea id="additional_info" name="additional_info" placeholder="Any additional details about the requirement"><?php echo $additional_info; ?></textarea>
                </div>
                
                <div class="form-group checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_public" <?php echo $is_public ? 'checked' : ''; ?>>
                        <span>Make this request public (visible to all donors)</span>
                    </label>
                </div>
                
                <div class="form-buttons">
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                    <a href="user-dashboard.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
            
            <div class="request-info">
                <h3>Important Information</h3>
                <ul>
                    <li>Provide accurate information to help donors determine if they can help.</li>
                    <li>Critical requests will be highlighted and may be shared with more donors.</li>
                    <li>Donors will contact you directly through the provided phone number.</li>
                    <li>You can mark your request as fulfilled once you have found donors.</li>
                    <li>Public requests will be visible to all users. Uncheck this option for privacy.</li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
