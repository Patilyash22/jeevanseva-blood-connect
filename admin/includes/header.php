
<?php
require_once '../config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Get admin details
$admin_id = $_SESSION['admin_id'];
$sql = "SELECT username FROM users WHERE id = $admin_id";
$result = mysqli_query($conn, $sql);
$admin = mysqli_fetch_assoc($result);
$admin_username = $admin['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JeevanSeva Admin Panel</title>
    <link rel="stylesheet" href="../styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-sidebar">
            <div class="admin-sidebar-header">
                <div class="logo">
                    <div class="blood-drop"></div>
                    <span>JeevanSeva</span>
                </div>
                <p class="admin-subtitle">Admin Panel</p>
            </div>
            
            <div class="admin-user-info">
                <div class="admin-avatar">
                    <?php echo strtoupper(substr($admin_username, 0, 1)); ?>
                </div>
                <div>
                    <p class="admin-username"><?php echo $admin_username; ?></p>
                    <p class="admin-role">Administrator</p>
                </div>
            </div>
            
            <ul class="admin-menu">
                <li><a href="dashboard.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'class="active"' : ''; ?>>
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a></li>
                <li><a href="manage_donors.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_donors.php') ? 'class="active"' : ''; ?>>
                    <i class="fas fa-heart"></i> Donor Management
                </a></li>
                <li><a href="manage_users.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_users.php') ? 'class="active"' : ''; ?>>
                    <i class="fas fa-users"></i> User Management
                </a></li>
                <li><a href="manage_testimonials.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_testimonials.php') ? 'class="active"' : ''; ?>>
                    <i class="fas fa-comment-dots"></i> Testimonials
                </a></li>
                <li><a href="manage_faqs.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_faqs.php') ? 'class="active"' : ''; ?>>
                    <i class="fas fa-question-circle"></i> FAQs
                </a></li>
                <li><a href="settings.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'settings.php') ? 'class="active"' : ''; ?>>
                    <i class="fas fa-cog"></i> Settings
                </a></li>
            </ul>
            
            <div class="admin-sidebar-footer">
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
        
        <div class="admin-content">
            <div class="admin-header">
                <div class="mobile-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </div>
                
                <div class="admin-header-title">
                    <?php
                    $page_title = "Dashboard";
                    $current_page = basename($_SERVER['PHP_SELF']);
                    
                    if ($current_page == 'dashboard.php') $page_title = "Dashboard";
                    elseif ($current_page == 'manage_donors.php') $page_title = "Donor Management";
                    elseif ($current_page == 'manage_users.php') $page_title = "User Management";
                    elseif ($current_page == 'manage_testimonials.php') $page_title = "Testimonials";
                    elseif ($current_page == 'manage_faqs.php') $page_title = "FAQs";
                    elseif ($current_page == 'settings.php') $page_title = "Settings";
                    ?>
                    <h1><?php echo $page_title; ?></h1>
                </div>
            </div>
            
            <?php
            // Display alert messages
            $message = getMessage();
            if ($message) {
                echo '<div class="alert alert-' . $message['type'] . '">' . $message['message'] . '</div>';
            }
            ?>
            
            <div class="admin-content-body">
