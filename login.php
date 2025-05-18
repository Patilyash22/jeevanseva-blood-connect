
<?php
require_once 'config.php';

// Check if user is already logged in
if (isLoggedIn()) {
    if (isAdmin()) {
        redirect('admin/dashboard.php');
    } else {
        redirect('user-dashboard.php');
    }
}

// Initialize variables
$username = '';
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    // Validate input
    if (empty($username)) {
        $errors[] = "Username is required";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    }
    
    // If no errors, attempt login
    if (empty($errors)) {
        $sql = "SELECT * FROM users WHERE username = '$username' OR email = '$username'";
        $result = mysqli_query($conn, $sql);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            
            if (password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['is_admin'] = (bool)$user['is_admin'];
                $_SESSION['credits'] = $user['credits'];
                
                // Record last login
                $user_id = $user['id'];
                $update_sql = "UPDATE users SET last_login = NOW() WHERE id = $user_id";
                mysqli_query($conn, $update_sql);
                
                // Redirect based on user role
                if ($_SESSION['is_admin']) {
                    redirect('admin/dashboard.php');
                } else {
                    redirect('user-dashboard.php');
                }
            } else {
                $errors[] = "Invalid password";
            }
        } else {
            $errors[] = "Username or email not found";
        }
    }
}

include 'includes/header.php';
?>

<section class="login-section">
    <div class="container">
        <div class="auth-container">
            <h1>Login to JeevanSeva</h1>
            <p class="section-intro">Access your account to find donors or manage your donations.</p>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php
            // Display message if set
            $message = getMessage();
            if ($message) {
                echo '<div class="alert alert-' . $message['type'] . '">' . $message['message'] . '</div>';
            }
            ?>
            
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="username">Username or Email</label>
                    <input type="text" id="username" name="username" value="<?php echo $username; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group form-remember">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>
                    <a href="forgot-password.php" class="forgot-link">Forgot password?</a>
                </div>
                
                <div class="form-buttons">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
            </form>
            
            <div class="auth-footer">
                <p>Don't have an account yet?</p>
                <div class="auth-options">
                    <a href="user-registration.php" class="btn btn-outline">Register as Recipient</a>
                    <a href="donor-registration.php" class="btn btn-outline">Register as Donor</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
