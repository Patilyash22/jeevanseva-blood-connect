
<?php
require_once 'config.php';

// Get settings from database
$site_title = "JeevanSeva - Blood Donation Platform"; // Default title
$current_theme = "light"; // Default theme

$sql = "SELECT * FROM settings WHERE setting_name IN ('site_title', 'current_theme')";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['setting_name'] == 'site_title') {
            $site_title = $row['setting_value'];
        }
        if ($row['setting_name'] == 'current_theme') {
            $current_theme = $row['setting_value'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $site_title; ?></title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <body class="theme-<?php echo $current_theme; ?>">
</head>
<body>
  <header>
    <div class="container">
      <div class="logo">
        <div class="blood-drop"></div>
        <span>JeevanSeva</span>
      </div>
      <nav id="navbar">
        <ul>
          <li><a href="index.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'class="active"' : ''; ?>>Home</a></li>
          <li><a href="donor-registration.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'donor-registration.php') ? 'class="active"' : ''; ?>>Become a Donor</a></li>
          <li><a href="find-donor.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'find-donor.php') ? 'class="active"' : ''; ?>>Find a Donor</a></li>
          <li><a href="about.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'about.php') ? 'class="active"' : ''; ?>>About</a></li>
          <li><a href="contact.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'class="active"' : ''; ?>>Contact</a></li>
        </ul>
        <div class="mobile-menu-btn" onclick="toggleMobileMenu()">
          <div class="bar"></div>
          <div class="bar"></div>
          <div class="bar"></div>
        </div>
      </nav>
    </div>
  </header>
