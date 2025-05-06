
<?php
require_once 'config.php';

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Redirect to find-donor page if accessed directly
    header('Location: find-donor.php');
    exit();
}

// Get form data
$donor_id = isset($_POST['donor_id']) ? (int)$_POST['donor_id'] : 0;
$requester_name = isset($_POST['requester_name']) ? sanitize($_POST['requester_name']) : '';
$requester_phone = isset($_POST['requester_phone']) ? sanitize($_POST['requester_phone']) : '';
$requester_email = isset($_POST['requester_email']) ? sanitize($_POST['requester_email']) : '';
$request_reason = isset($_POST['request_reason']) ? sanitize($_POST['request_reason']) : '';

// Validate data
$errors = [];

if (empty($donor_id)) {
    $errors[] = "Donor ID is missing.";
}

if (empty($requester_name)) {
    $errors[] = "Your name is required.";
}

if (empty($requester_phone)) {
    $errors[] = "Your phone number is required.";
}

if (empty($requester_email) || !filter_var($requester_email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "A valid email is required.";
}

if (empty($request_reason)) {
    $errors[] = "Reason for request is required.";
}

// Process the request if there are no errors
if (empty($errors)) {
    // Get donor information
    $sql = "SELECT * FROM donors WHERE id = $donor_id AND status = 'active'";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $donor = mysqli_fetch_assoc($result);
        
        // Store the request in the database
        $sql = "INSERT INTO donor_requests (donor_id, requester_name, requester_phone, requester_email, request_reason, status) 
                VALUES ($donor_id, '$requester_name', '$requester_phone', '$requester_email', '$request_reason', 'pending')";
        
        if (mysqli_query($conn, $sql)) {
            // Set success message in session
            $_SESSION['request_success'] = "Your request has been submitted successfully. We will contact the donor and get back to you soon.";
        } else {
            // Set error message in session
            $_SESSION['request_error'] = "An error occurred while submitting your request. Please try again.";
        }
    } else {
        // Set error message in session
        $_SESSION['request_error'] = "Donor not found or is not active.";
    }
} else {
    // Set error message in session
    $_SESSION['request_error'] = implode("<br>", $errors);
}

// Redirect back to find-donor page
header('Location: find-donor.php');
exit();
?>
