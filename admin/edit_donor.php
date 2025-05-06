
<?php
require_once '../config.php';
include 'includes/header.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    setMessage("Donor ID is required", "danger");
    redirect("manage_donors.php");
}

$id = (int)$_GET['id'];

// Get donor data
$sql = "SELECT * FROM donors WHERE id = $id";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    setMessage("Donor not found", "danger");
    redirect("manage_donors.php");
}

$donor = mysqli_fetch_assoc($result);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $blood_group = sanitize($_POST['blood_group']);
    $location = sanitize($_POST['location']);
    $age = (int)$_POST['age'];
    $weight = (int)$_POST['weight'];
    $last_donation_date = !empty($_POST['last_donation_date']) ? sanitize($_POST['last_donation_date']) : NULL;
    $medical_conditions = sanitize($_POST['medical_conditions']);
    $status = sanitize($_POST['status']);
    
    // Validate input
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    
    if (empty($phone) || strlen($phone) < 10) {
        $errors[] = "Valid phone number is required";
    }
    
    if (empty($blood_group)) {
        $errors[] = "Blood group is required";
    }
    
    if (empty($location)) {
        $errors[] = "Location is required";
    }
    
    if (empty($age) || $age < 18) {
        $errors[] = "Age must be at least 18 years";
    }
    
    if (empty($weight) || $weight < 50) {
        $errors[] = "Weight must be at least 50 kg";
    }
    
    // If no errors, update database
    if (empty($errors)) {
        $last_donation_sql = $last_donation_date ? "'$last_donation_date'" : "NULL";
        
        $sql = "UPDATE donors SET 
                name = '$name',
                email = '$email',
                phone = '$phone',
                blood_group = '$blood_group',
                location = '$location',
                age = $age,
                weight = $weight,
                last_donation_date = $last_donation_sql,
                medical_conditions = '$medical_conditions',
                status = '$status'
                WHERE id = $id";
        
        if (mysqli_query($conn, $sql)) {
            setMessage("Donor updated successfully");
            redirect("manage_donors.php");
        } else {
            $errors[] = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2>Edit Donor</h2>
    </div>
    <div class="card-body">
        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="admin-form">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Full Name <span class="required">*</span></label>
                        <input type="text" id="name" name="name" value="<?php echo $donor['name']; ?>" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">Email <span class="required">*</span></label>
                        <input type="email" id="email" name="email" value="<?php echo $donor['email']; ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="phone">Phone Number <span class="required">*</span></label>
                        <input type="tel" id="phone" name="phone" value="<?php echo $donor['phone']; ?>" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="blood_group">Blood Group <span class="required">*</span></label>
                        <select id="blood_group" name="blood_group" required>
                            <option value="A+" <?php echo ($donor['blood_group'] === 'A+') ? 'selected' : ''; ?>>A+</option>
                            <option value="A-" <?php echo ($donor['blood_group'] === 'A-') ? 'selected' : ''; ?>>A-</option>
                            <option value="B+" <?php echo ($donor['blood_group'] === 'B+') ? 'selected' : ''; ?>>B+</option>
                            <option value="B-" <?php echo ($donor['blood_group'] === 'B-') ? 'selected' : ''; ?>>B-</option>
                            <option value="AB+" <?php echo ($donor['blood_group'] === 'AB+') ? 'selected' : ''; ?>>AB+</option>
                            <option value="AB-" <?php echo ($donor['blood_group'] === 'AB-') ? 'selected' : ''; ?>>AB-</option>
                            <option value="O+" <?php echo ($donor['blood_group'] === 'O+') ? 'selected' : ''; ?>>O+</option>
                            <option value="O-" <?php echo ($donor['blood_group'] === 'O-') ? 'selected' : ''; ?>>O-</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="age">Age (years) <span class="required">*</span></label>
                        <input type="number" id="age" name="age" min="18" value="<?php echo $donor['age']; ?>" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="weight">Weight (kg) <span class="required">*</span></label>
                        <input type="number" id="weight" name="weight" min="50" value="<?php echo $donor['weight']; ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="location">Location <span class="required">*</span></label>
                <input type="text" id="location" name="location" placeholder="City, State" value="<?php echo $donor['location']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="last_donation_date">Last Donation Date (if applicable)</label>
                <input type="date" id="last_donation_date" name="last_donation_date" value="<?php echo $donor['last_donation_date']; ?>">
            </div>
            
            <div class="form-group">
                <label for="medical_conditions">Medical Conditions (if any)</label>
                <textarea id="medical_conditions" name="medical_conditions" rows="3"><?php echo $donor['medical_conditions']; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="status">Status <span class="required">*</span></label>
                <select id="status" name="status" required>
                    <option value="active" <?php echo ($donor['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo ($donor['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                    <option value="pending" <?php echo ($donor['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                </select>
            </div>
            
            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Update Donor</button>
                <a href="manage_donors.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<style>
.row {
    display: flex;
    flex-wrap: wrap;
    margin-right: -15px;
    margin-left: -15px;
}

.col-md-6 {
    width: 50%;
    padding-right: 15px;
    padding-left: 15px;
    box-sizing: border-box;
}

@media (max-width: 768px) {
    .col-md-6 {
        width: 100%;
    }
}

.required {
    color: red;
}
</style>

<?php include 'includes/footer.php'; ?>
