
<?php
// Database configuration
$db_host = "localhost";
$db_user = "your_db_username";  // Change this to your database username
$db_pass = "your_db_password";  // Change this to your database password
$db_name = "jeevanseva_db";     // Change this to your database name

// Establish database connection
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Site configuration
$site_name = "JeevanSeva";
$site_url = "https://example.com"; // Replace with your actual domain
$admin_email = "admin@example.com";

// Session configuration
session_start();

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Function to check if user is admin
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

// Function to check admin permissions
function hasPermission($permission) {
    // If user is super admin, return true for any permission
    if (isLoggedIn() && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
        return true;
    }
    
    // If user has a role with specific permission
    if (isLoggedIn() && isset($_SESSION['permissions'])) {
        $permissions = json_decode($_SESSION['permissions'], true);
        
        // If user has 'all' permission
        if (isset($permissions['all']) && $permissions['all']) {
            return true;
        }
        
        // Check for specific permission
        if (isset($permissions[$permission])) {
            // Permission could be boolean or string (e.g., 'read', 'write')
            if (is_bool($permissions[$permission])) {
                return $permissions[$permission];
            } else {
                // For fine-grained permissions (read/write)
                return true;
            }
        }
    }
    
    return false;
}

// Function to sanitize input data
function sanitize($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = mysqli_real_escape_string($conn, $data);
    return $data;
}

// Function to redirect
function redirect($url) {
    header("Location: $url");
    exit();
}

// Function to display messages
function setMessage($message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}

function getMessage() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        $type = $_SESSION['message_type'];
        
        // Clear the message
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

// Function to get site setting
function getSetting($setting_name, $default = '') {
    global $conn;
    $sql = "SELECT setting_value FROM settings WHERE setting_name = '$setting_name'";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['setting_value'];
    }
    
    return $default;
}

// Function to process credit transaction
function processCredits($user_id, $amount, $type, $description, $reference_id = null) {
    global $conn;
    
    // Begin transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Update user credits
        $sql = "UPDATE users SET credits = credits + ($amount) WHERE id = $user_id";
        if (!mysqli_query($conn, $sql)) {
            throw new Exception("Failed to update user credits: " . mysqli_error($conn));
        }
        
        // Add transaction record
        $reference_part = $reference_id ? ", reference_id" : "";
        $reference_value = $reference_id ? ", $reference_id" : "";
        
        $sql = "INSERT INTO credits (user_id, amount, transaction_type, description$reference_part) 
                VALUES ($user_id, $amount, '$type', '$description'$reference_value)";
        
        if (!mysqli_query($conn, $sql)) {
            throw new Exception("Failed to record credit transaction: " . mysqli_error($conn));
        }
        
        // Commit transaction
        mysqli_commit($conn);
        
        // Update session credits if this is the current user
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id) {
            $_SESSION['credits'] = $_SESSION['credits'] + $amount;
        }
        
        return true;
    } catch (Exception $e) {
        // Roll back on error
        mysqli_rollback($conn);
        error_log($e->getMessage());
        return false;
    }
}

// Function to get translation
function __($key, $default = null) {
    // Placeholder for translation function - will be fully implemented later
    // For now, just return the key or default text
    return $default ?: $key;
}

// Load site settings
$site_theme = getSetting('theme', 'light');
$site_title = getSetting('site_title', 'JeevanSeva - Blood Donation Platform');
$site_tagline = getSetting('site_tagline', 'Donate Blood, Save Lives');
?>
