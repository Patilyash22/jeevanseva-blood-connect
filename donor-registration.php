
<?php
require_once 'config.php';
include 'includes/header.php';

$success_message = '';
$error_message = '';

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
    
    // If no errors, insert into database
    if (empty($errors)) {
        $last_donation_sql = $last_donation_date ? "'$last_donation_date'" : "NULL";
        
        $sql = "INSERT INTO donors (name, email, phone, blood_group, location, age, weight, last_donation_date, medical_conditions, status) 
                VALUES ('$name', '$email', '$phone', '$blood_group', '$location', $age, $weight, $last_donation_sql, '$medical_conditions', 'pending')";
        
        if (mysqli_query($conn, $sql)) {
            $success_message = "Thank you for registering as a donor! Your information has been submitted and is pending approval.";
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
    } else {
        $error_message = implode("<br>", $errors);
    }
}
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-center">Become a Blood Donor</h1>
        
        <?php if (!empty($success_message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <div class="bg-white shadow-md rounded-lg p-6 md:p-8">
            <div class="mb-6">
                <p class="text-gray-600">Join our community of blood donors and help save lives. Please fill out the form below to register as a donor.</p>
            </div>
            
            <form action="" method="POST">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label for="name">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" required class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email <span class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email" required class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number <span class="text-red-500">*</span></label>
                        <input type="tel" id="phone" name="phone" required class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="blood_group">Blood Group <span class="text-red-500">*</span></label>
                        <select id="blood_group" name="blood_group" required class="form-control">
                            <option value="" disabled selected>Select your blood group</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="age">Age (years) <span class="text-red-500">*</span></label>
                        <input type="number" id="age" name="age" min="18" required class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="weight">Weight (kg) <span class="text-red-500">*</span></label>
                        <input type="number" id="weight" name="weight" min="50" required class="form-control">
                    </div>
                    
                    <div class="form-group md:col-span-2">
                        <label for="location">Location <span class="text-red-500">*</span></label>
                        <input type="text" id="location" name="location" required placeholder="City, State" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="last_donation_date">Last Donation Date (if applicable)</label>
                        <input type="date" id="last_donation_date" name="last_donation_date" class="form-control">
                    </div>
                    
                    <div class="form-group md:col-span-2">
                        <label for="medical_conditions">Medical Conditions (if any)</label>
                        <textarea id="medical_conditions" name="medical_conditions" rows="3" class="form-control"
                                  placeholder="Please mention any existing medical conditions or medications"></textarea>
                    </div>
                </div>
                
                <div class="form-group mt-6">
                    <div class="flex items-center">
                        <input type="checkbox" id="agree" name="agree" required class="mr-2">
                        <label for="agree" class="text-sm">
                            I agree to be contacted for blood donation and verify that the information provided is accurate.
                            <span class="text-red-500">*</span>
                        </label>
                    </div>
                </div>
                
                <div class="mt-8">
                    <button type="submit" class="btn btn-primary w-full">Register as Donor</button>
                </div>
            </form>
        </div>
        
        <div class="mt-8 text-center">
            <p class="text-gray-600">Already registered? <a href="find-donor.php" class="text-jeevanseva-red hover:underline">Find donors in your area</a></p>
        </div>
    </div>
</div>

<style>
.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #4a5568;
}

.form-control {
    display: block;
    width: 100%;
    padding: 10px;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    background-color: #fff;
    font-size: 16px;
    color: #4a5568;
}

.form-control:focus {
    outline: none;
    border-color: #e53e3e;
    box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1);
}

select.form-control {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%234a5568' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 16px;
    padding-right: 40px;
}
</style>

<?php include 'includes/footer.php'; ?>
