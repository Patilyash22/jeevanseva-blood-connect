
<?php
require_once 'config.php';
require_once 'app/Services/UserCreditService.php';

// Initialize variables
$username = '';
$email = '';
$name = '';
$phone = '';
$location = '';
$errors = [];
$success = false;
$referral_code = isset($_GET['ref']) ? sanitize($_GET['ref']) : '';

// Get signup bonus from settings
$signup_bonus = (int)getSetting('signup_bonus', 20);
$referral_bonus = (int)getSetting('referral_bonus', 10);

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
            $new_referral_code = generateReferralCode();
            
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user with initial credits
            $sql = "INSERT INTO users (username, password, email, name, phone, location, credits, referral_code) 
                    VALUES ('$username', '$hashed_password', '$email', '$name', '$phone', '$location', $signup_bonus, '$new_referral_code')";
            
            if (!mysqli_query($conn, $sql)) {
                throw new Exception("Error registering user: " . mysqli_error($conn));
            }
            
            $user_id = mysqli_insert_id($conn);
            
            // Create new user object with Eloquent-like approach
            $user = [
                'id' => $user_id,
                'username' => $username,
                'email' => $email,
                'name' => $name,
                'phone' => $phone,
                'location' => $location,
                'credits' => $signup_bonus,
                'referral_code' => $new_referral_code
            ];
            
            // Create credit service
            $creditService = new App\Services\UserCreditService();
            
            // Add initial credits transaction
            $sql = "INSERT INTO credits (user_id, amount, transaction_type, description) 
                    VALUES ($user_id, $signup_bonus, 'signup_bonus', 'Initial signup bonus')";
            
            if (!mysqli_query($conn, $sql)) {
                throw new Exception("Error adding initial credits: " . mysqli_error($conn));
            }
            
            // Record user activity
            $sql = "INSERT INTO user_activities (user_id, activity_type, description, created_at) 
                    VALUES ($user_id, 'registration', 'User registered', NOW())";
            
            if (!mysqli_query($conn, $sql)) {
                throw new Exception("Error recording user activity: " . mysqli_error($conn));
            }
            
            // Process referral if provided
            if (!empty($referrer_code)) {
                // Check if referral code is valid
                $sql = "SELECT id FROM users WHERE referral_code = '$referrer_code'";
                $result = mysqli_query($conn, $sql);
                
                if ($result && mysqli_num_rows($result) > 0) {
                    $referrer = mysqli_fetch_assoc($result);
                    $referrer_id = $referrer['id'];
                    
                    // Create referral record
                    $sql = "INSERT INTO referrals (referrer_id, referred_id, referral_code, status, completed_at) 
                            VALUES ($referrer_id, $user_id, '$referrer_code', 'completed', NOW())";
                    
                    if (!mysqli_query($conn, $sql)) {
                        throw new Exception("Error recording referral: " . mysqli_error($conn));
                    }
                    
                    $referral_id = mysqli_insert_id($conn);
                    
                    // Add credits to referrer
                    $sql = "UPDATE users SET credits = credits + $referral_bonus WHERE id = $referrer_id";
                    
                    if (!mysqli_query($conn, $sql)) {
                        throw new Exception("Error updating referrer credits: " . mysqli_error($conn));
                    }
                    
                    // Record credit transaction for referrer
                    $sql = "INSERT INTO credits (user_id, amount, transaction_type, description, reference_id) 
                            VALUES ($referrer_id, $referral_bonus, 'referral', 'Referral bonus for user $username', $referral_id)";
                    
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
            $_SESSION['name'] = $name;
            $_SESSION['is_admin'] = false;
            $_SESSION['credits'] = $signup_bonus;
            
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
function generateReferralCode() {
    $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $code = "";
    
    for ($i = 0; $i < 8; $i++) {
        $code .= $chars[rand(0, strlen($chars) - 1)];
    }
    
    global $conn;
    $sql = "SELECT id FROM users WHERE referral_code = '$code'";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        // Code already exists, generate a new one
        return generateReferralCode();
    }
    
    return $code;
}

include 'includes/header.php';
?>

<section class="registration-section bg-white py-8">
    <div class="container mx-auto px-4">
        <div class="auth-container max-w-md mx-auto bg-white p-6 rounded-lg shadow-lg">
            <h1 class="text-2xl font-bold mb-2 text-center text-jeevanseva-darkred">Recipient Registration</h1>
            <p class="section-intro text-center text-gray-600 mb-6">Create an account to find and contact blood donors near you.</p>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                    <ul class="list-disc pl-5">
                        <?php foreach ($errors as $error): ?>
                            <li class="mb-1"><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
                    <p>Registration successful! Welcome to JeevanSeva.</p>
                </div>
            <?php else: ?>
                <form method="POST" class="auth-form space-y-4">
                    <div class="form-group">
                        <label for="username" class="block text-gray-700 font-medium mb-1">Username</label>
                        <input type="text" id="username" name="username" value="<?php echo $username; ?>" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-jeevanseva-red focus:border-jeevanseva-red">
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="block text-gray-700 font-medium mb-1">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo $email; ?>" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-jeevanseva-red focus:border-jeevanseva-red">
                    </div>
                    
                    <div class="form-row grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="password" class="block text-gray-700 font-medium mb-1">Password</label>
                            <input type="password" id="password" name="password" required 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-jeevanseva-red focus:border-jeevanseva-red">
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password" class="block text-gray-700 font-medium mb-1">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-jeevanseva-red focus:border-jeevanseva-red">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="name" class="block text-gray-700 font-medium mb-1">Full Name</label>
                        <input type="text" id="name" name="name" value="<?php echo $name; ?>" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-jeevanseva-red focus:border-jeevanseva-red">
                    </div>
                    
                    <div class="form-row grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="phone" class="block text-gray-700 font-medium mb-1">Phone Number</label>
                            <input type="text" id="phone" name="phone" value="<?php echo $phone; ?>" required 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-jeevanseva-red focus:border-jeevanseva-red">
                        </div>
                        
                        <div class="form-group">
                            <label for="location" class="block text-gray-700 font-medium mb-1">Location</label>
                            <input type="text" id="location" name="location" value="<?php echo $location; ?>" required 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-jeevanseva-red focus:border-jeevanseva-red">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="referral_code" class="block text-gray-700 font-medium mb-1">Referral Code (Optional)</label>
                        <div class="flex">
                            <input type="text" id="referral_code" name="referral_code" value="<?php echo $referral_code; ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-l-md focus:ring-2 focus:ring-jeevanseva-red focus:border-jeevanseva-red">
                            <div class="bg-gray-100 text-gray-700 px-3 py-2 rounded-r-md border border-l-0 border-gray-300 flex items-center">
                                <i class="fas fa-gift"></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Enter a referral code to get additional credits</p>
                    </div>
                    
                    <div class="form-group p-3 bg-blue-50 rounded-md">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" id="agree" name="agree" required class="h-4 w-4 text-jeevanseva-red focus:ring-jeevanseva-red border-gray-300 rounded">
                            </div>
                            <label for="agree" class="ml-2 text-sm text-gray-700">
                                I agree to the <a href="terms.php" target="_blank" class="text-jeevanseva-red hover:underline">Terms of Service</a> and <a href="privacy.php" target="_blank" class="text-jeevanseva-red hover:underline">Privacy Policy</a>
                                <span class="text-red-500">*</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="submit" class="w-full bg-jeevanseva-red hover:bg-jeevanseva-darkred text-white py-3 px-6 rounded-md font-medium transition">Register</button>
                    </div>
                    
                    <div class="signup-bonus text-center p-3 bg-jeevanseva-light rounded-md mt-4">
                        <p class="text-sm font-medium text-gray-700">
                            <i class="fas fa-gift text-jeevanseva-red mr-1"></i>
                            Sign up and receive <span class="text-jeevanseva-darkred font-bold"><?php echo $signup_bonus; ?> credits</span> instantly!
                        </p>
                    </div>
                </form>
                
                <div class="auth-footer mt-6 text-center">
                    <p class="text-gray-600">Already have an account? <a href="login.php" class="text-jeevanseva-red hover:underline">Login here</a></p>
                    <p class="text-gray-600 mt-2">Want to be a donor? <a href="donor-registration.php" class="text-jeevanseva-red hover:underline">Register as donor</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
