
<?php
require_once '../config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="donors_' . date('Y-m-d') . '.csv"');

// Create a file handle for output
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for Excel compatibility
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Set column headers
fputcsv($output, [
    'ID', 
    'Name', 
    'Email', 
    'Phone',
    'Blood Group',
    'Location',
    'Age',
    'Weight',
    'Last Donation Date',
    'Medical Conditions',
    'Status',
    'Date Registered'
]);

// Get all donors
$sql = "SELECT * FROM donors ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    // Output each row
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            $row['id'],
            $row['name'],
            $row['email'],
            $row['phone'],
            $row['blood_group'],
            $row['location'],
            $row['age'],
            $row['weight'],
            $row['last_donation_date'],
            $row['medical_conditions'],
            $row['status'],
            $row['created_at']
        ]);
    }
}

// Close the file handle
fclose($output);
exit;
?>
