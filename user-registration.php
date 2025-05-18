
<?php
require_once 'config.php';

// Initialize variables
$username = '';
$email = '';
$name = '';
$phone = '';
$location = '';
$errors = [];
$success = false;
$referral_code = isset($_GET['ref']) ? sanitize($_GET['ref']) : '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $name = sanitize($_POST['name']);
    $phone = sanitize($_POST['phone']);
    $location = sanitize($_POST['location']);
    $referrer_code = sanitize($_POST['referral_code']);
    
    // Validate input
    if (empty($username)) {
        $errors[] = "Username is required";
    } else if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = "Username can only contain letters, numbers and underscores";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    } else if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    if (empty($name)) {
        $errors[] = "Full name is required";
    }
    
    if (empty($phone)) {
        $errors[] = "Phone number is required";
    }
    
    if (empty($location)) {
        $errors[] = "Location is required";
    }
    
    // Check if username or email already exists
    $sql = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if ($row['username'] === $username) {
            $errors[] = "Username already taken";
        }
        if ($row['email'] === $email) {
            $errors[] = "Email already registered";
        }
    }
    
    // If no errors, register the user
    if (empty($errors)) {
        // Begin transaction
        mysqli_begin_transaction($conn);
        
        try {
            // Generate unique referral code
            $new_referral_code = generateReferralCode($name, $username);
            
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Get initial credits from settings
            $initial_credits = 20; // Default value
            $sql = "SELECT setting_value FROM settings WHERE setting_name = 'initial_credits'";
            $result = mysqli_query($conn, $sql);
            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $initial_credits = (int)$row['setting_value'];
            }
            
            // Insert user
            $sql = "INSERT INTO users (username, password, email, credits, referral_code) 
                    VALUES ('$username', '$hashed_password', '$email', $initial_credits, '$new_referral_code')";
            
            if (!mysqli_query($conn, $sql)) {
                throw new Exception("Error registering user: " . mysqli_error($conn));
            }
            
            $user_id = mysqli_insert_id($conn);
            
            // Add initial credits transaction
            $sql = "INSERT INTO credits (user_id, amount, transaction_type, description) 
                    VALUES ($user_id, $initial_credits, 'initial', 'Initial signup credits')";
            
            if (!mysqli_query($conn, $sql)) {
                throw new Exception("Error adding initial credits: " . mysqli_error($conn));
            }
            
            // Add recipient profile
            $sql = "INSERT INTO recipients (user_id, address) 
                    VALUES ($user_id, '$location')";
            
            if (!mysqli_query($conn, $sql)) {
                throw new Exception("Error creating recipient profile: " . mysqli_error($conn));
            }
            
            // Process referral if provided
            if (!empty($referrer_code)) {
                // Check if referral code is valid
                $sql = "SELECT id FROM users WHERE referral_code = '$referrer_code'";
                $result = mysqli_query($conn, $sql);
                
                if ($result && mysqli_num_rows($result) > 0) {
                    $referrer = mysqli_fetch_assoc($result);
                    $referrer_id = $referrer['id'];
                    
                    // Get referral bonus credits from settings
                    $referral_credits = 10; // Default value
                    $sql = "SELECT setting_value FROM settings WHERE setting_name = 'referral_credits'";
                    $result = mysqli_query($conn, $sql);
                    if ($result && mysqli_num_rows($result) > 0) {
                        $row = mysqli_fetch_assoc($result);
                        $referral_credits = (int)$row['setting_value'];
                    }
                    
                    // Create referral record
                    $sql = "INSERT INTO referrals (referrer_id, referred_id, referral_code, status, completed_at) 
                            VALUES ($referrer_id, $user_id, '$referrer_code', 'completed', NOW())";
                    
                    if (!mysqli_query($conn, $sql)) {
                        throw new Exception("Error recording referral: " . mysqli_error($conn));
                    }
                    
                    $referral_id = mysqli_insert_id($conn);
                    
                    // Add credits to referrer
                    $sql = "UPDATE users SET credits = credits + $referral_credits WHERE id = $referrer_id";
                    
                    if (!mysqli_query($conn, $sql)) {
                        throw new Exception("Error updating referrer credits: " . mysqli_error($conn));
                    }
                    
                    // Record credit transaction
                    $sql = "INSERT INTO credits (user_id, amount, transaction_type, description, reference_id) 
                            VALUES ($referrer_id, $referral_credits, 'referral', 'Referral bonus for user $username', $referral_id)";
                    
                    if (!mysqli_query($conn, $sql)) {
                        throw new Exception("Error recording referral credits: " . mysqli_error($conn));
                    }
                }
            }
            
            // Commit transaction
            mysqli_commit($conn);
            
            // Set success flag
            $success = true;
            
            // Log the user in automatically
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['is_admin'] = false;
            $_SESSION['credits'] = $initial_credits;
            
            // Redirect to dashboard
            redirect('user-dashboard.php');
            
        } catch (Exception $e) {
            // Rollback transaction on error
            mysqli_rollback($conn);
            $errors[] = $e->getMessage();
        }
    }
}

// Function to generate a unique referral code
function generateReferralCode($name, $username) {
    // Take first 3 characters of name and last 3 of username
    $name_part = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $name), 0, 3));
    $username_part = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $username), -3));
    $random_part = strtoupper(substr(md5(time()), 0, 4));
    
    return $name_part . $random_part . $username_part;
}

include 'includes/header.php';
?>

<section class="registration-section">
    <div class="container">
        <div class="auth-container">
            <h1>Recipient Registration</h1>
            <p class="section-intro">Create an account to find and contact blood donors near you.</p>
            
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
                    <p>Registration successful! Welcome to JeevanSeva.</p>
                </div>
            <?php else: ?>
                <form method="POST" class="auth-form">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="<?php echo $username; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" value="<?php echo $name; ?>" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="text" id="phone" name="phone" value="<?php echo $phone; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" id="location" name="location" value="<?php echo $location; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="referral_code">Referral Code (Optional)</label>
                        <input type="text" id="referral_code" name="referral_code" value="<?php echo $referral_code; ?>">
                    </div>
                    
                    <div class="form-group form-terms">
                        <label class="checkbox-label">
                            <input type="checkbox" name="terms" required>
                            <span>I agree to the <a href="terms.php" target="_blank">Terms of Service</a> and <a href="privacy.php" target="_blank">Privacy Policy</a></span>
                        </label>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="submit" class="btn btn-primary">Register</button>
                    </div>
                </form>
                
                <div class="auth-footer">
                    <p>Already have an account? <a href="login.php">Login here</a></p>
                    <p>Want to be a donor? <a href="donor-registration.php">Register as donor</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
