
<?php
require_once 'config.php';

// Check if user is already logged in as admin
if (!isAdmin()) {
    echo "This script must be run by an administrator.";
    exit;
}

// Begin transaction
mysqli_begin_transaction($conn);

try {
    // Create recipients table
    $sql = "CREATE TABLE IF NOT EXISTS recipients (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) NOT NULL,
        address VARCHAR(255),
        hospital_name VARCHAR(255),
        patient_name VARCHAR(255),
        patient_age INT(3),
        reason_for_blood TEXT,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    
    if (!mysqli_query($conn, $sql)) {
        throw new Exception("Error creating recipients table: " . mysqli_error($conn));
    }
    
    // Create credits table
    $sql = "CREATE TABLE IF NOT EXISTS credits (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) NOT NULL,
        amount INT(11) NOT NULL,
        transaction_type ENUM('initial', 'view_donor', 'referral', 'admin_adjustment', 'purchase') NOT NULL,
        description TEXT,
        reference_id INT(11),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    
    if (!mysqli_query($conn, $sql)) {
        throw new Exception("Error creating credits table: " . mysqli_error($conn));
    }
    
    // Create referrals table
    $sql = "CREATE TABLE IF NOT EXISTS referrals (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        referrer_id INT(11) NOT NULL,
        referred_id INT(11) NOT NULL,
        referral_code VARCHAR(20) NOT NULL,
        status ENUM('pending', 'completed') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        completed_at TIMESTAMP NULL,
        FOREIGN KEY (referrer_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (referred_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    
    if (!mysqli_query($conn, $sql)) {
        throw new Exception("Error creating referrals table: " . mysqli_error($conn));
    }
    
    // Create blood_requests table
    $sql = "CREATE TABLE IF NOT EXISTS blood_requests (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) NOT NULL,
        blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
        location VARCHAR(255) NOT NULL,
        hospital_name VARCHAR(255),
        patient_name VARCHAR(255),
        urgency ENUM('normal', 'urgent', 'critical') DEFAULT 'normal',
        units_required INT(2) NOT NULL DEFAULT 1,
        contact_phone VARCHAR(20) NOT NULL,
        additional_info TEXT,
        status ENUM('open', 'fulfilled', 'closed') DEFAULT 'open',
        is_public BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    
    if (!mysqli_query($conn, $sql)) {
        throw new Exception("Error creating blood_requests table: " . mysqli_error($conn));
    }
    
    // Create donations table
    $sql = "CREATE TABLE IF NOT EXISTS donations (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        donor_id INT(11) NOT NULL,
        donation_date DATE NOT NULL,
        hospital_name VARCHAR(255),
        location VARCHAR(255),
        units INT(2) DEFAULT 1,
        certificate_image VARCHAR(255),
        verification_status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
        verified_by INT(11),
        verification_date TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (donor_id) REFERENCES donors(id) ON DELETE CASCADE,
        FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL
    )";
    
    if (!mysqli_query($conn, $sql)) {
        throw new Exception("Error creating donations table: " . mysqli_error($conn));
    }
    
    // Create achievements table
    $sql = "CREATE TABLE IF NOT EXISTS achievements (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(100) NOT NULL,
        description TEXT NOT NULL,
        badge_image VARCHAR(255),
        points INT(11) DEFAULT 0,
        achievement_type ENUM('donation_count', 'referral_count', 'donation_streak', 'special') NOT NULL,
        condition_value INT(11) DEFAULT 0,
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!mysqli_query($conn, $sql)) {
        throw new Exception("Error creating achievements table: " . mysqli_error($conn));
    }
    
    // Create user_achievements table to track achievements earned by users
    $sql = "CREATE TABLE IF NOT EXISTS user_achievements (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) NOT NULL,
        achievement_id INT(11) NOT NULL,
        earned_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_user_achievement (user_id, achievement_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (achievement_id) REFERENCES achievements(id) ON DELETE CASCADE
    )";
    
    if (!mysqli_query($conn, $sql)) {
        throw new Exception("Error creating user_achievements table: " . mysqli_error($conn));
    }
    
    // Create languages table
    $sql = "CREATE TABLE IF NOT EXISTS languages (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(10) NOT NULL UNIQUE,
        name VARCHAR(50) NOT NULL,
        is_active BOOLEAN DEFAULT TRUE,
        is_default BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!mysqli_query($conn, $sql)) {
        throw new Exception("Error creating languages table: " . mysqli_error($conn));
    }
    
    // Create translations table
    $sql = "CREATE TABLE IF NOT EXISTS translations (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        language_id INT(11) NOT NULL,
        translation_key VARCHAR(255) NOT NULL,
        translation_value TEXT NOT NULL,
        UNIQUE KEY unique_translation (language_id, translation_key),
        FOREIGN KEY (language_id) REFERENCES languages(id) ON DELETE CASCADE
    )";
    
    if (!mysqli_query($conn, $sql)) {
        throw new Exception("Error creating translations table: " . mysqli_error($conn));
    }
    
    // Create admin_roles table
    $sql = "CREATE TABLE IF NOT EXISTS admin_roles (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL UNIQUE,
        permissions TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!mysqli_query($conn, $sql)) {
        throw new Exception("Error creating admin_roles table: " . mysqli_error($conn));
    }
    
    // Create blogs table
    $sql = "CREATE TABLE IF NOT EXISTS blogs (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL UNIQUE,
        content TEXT NOT NULL,
        excerpt TEXT,
        featured_image VARCHAR(255),
        author_id INT(11) NOT NULL,
        status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
        view_count INT(11) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    
    if (!mysqli_query($conn, $sql)) {
        throw new Exception("Error creating blogs table: " . mysqli_error($conn));
    }
    
    // Create carousel table
    $sql = "CREATE TABLE IF NOT EXISTS carousel (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        subtitle TEXT,
        image_path VARCHAR(255) NOT NULL,
        link_url VARCHAR(255),
        display_order INT(11) DEFAULT 0,
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!mysqli_query($conn, $sql)) {
        throw new Exception("Error creating carousel table: " . mysqli_error($conn));
    }
    
    // Alter users table to add credits and role
    $sql = "ALTER TABLE users 
            ADD COLUMN credits INT(11) NOT NULL DEFAULT 0,
            ADD COLUMN role_id INT(11) DEFAULT NULL,
            ADD COLUMN referral_code VARCHAR(20) UNIQUE,
            ADD COLUMN preferred_language VARCHAR(10) DEFAULT 'en',
            ADD COLUMN last_login TIMESTAMP NULL,
            ADD FOREIGN KEY (role_id) REFERENCES admin_roles(id) ON DELETE SET NULL";
    
    if (!mysqli_query($conn, $sql)) {
        throw new Exception("Error altering users table: " . mysqli_error($conn));
    }
    
    // Insert default admin role
    $sql = "INSERT INTO admin_roles (name, permissions) VALUES 
            ('Administrator', '{\"all\":true}'),
            ('Moderator', '{\"donors\":true,\"testimonials\":true,\"faqs\":true}'),
            ('Volunteer', '{\"donors\":\"read\",\"testimonials\":\"read\"}')";
    
    if (!mysqli_query($conn, $sql)) {
        throw new Exception("Error inserting admin roles: " . mysqli_error($conn));
    }
    
    // Update existing admin users to Administrator role
    $sql = "UPDATE users SET role_id = 1 WHERE is_admin = 1";
    if (!mysqli_query($conn, $sql)) {
        throw new Exception("Error updating admin users: " . mysqli_error($conn));
    }
    
    // Insert default languages
    $sql = "INSERT INTO languages (code, name, is_active, is_default) VALUES 
            ('en', 'English', 1, 1),
            ('hi', 'Hindi', 1, 0),
            ('mr', 'Marathi', 1, 0)";
    
    if (!mysqli_query($conn, $sql)) {
        throw new Exception("Error inserting languages: " . mysqli_error($conn));
    }
    
    // Insert default achievements
    $sql = "INSERT INTO achievements (title, description, achievement_type, condition_value, points) VALUES 
            ('First Blood', 'Complete your first blood donation', 'donation_count', 1, 10),
            ('Regular Donor', 'Donate blood 3 times', 'donation_count', 3, 30),
            ('Silver Saver', 'Donate blood 5 times', 'donation_count', 5, 50),
            ('Golden Heart', 'Donate blood 10 times', 'donation_count', 10, 100),
            ('Platinum Hero', 'Donate blood 25 times', 'donation_count', 25, 250),
            ('Referral Star', 'Refer 5 friends who register', 'referral_count', 5, 50),
            ('Community Builder', 'Refer 10 friends who register', 'referral_count', 10, 100),
            ('Dedication Award', 'Donate blood consistently for a year', 'donation_streak', 365, 150)";
    
    if (!mysqli_query($conn, $sql)) {
        throw new Exception("Error inserting achievements: " . mysqli_error($conn));
    }
    
    // Add new settings
    $settings = [
        ["theme", "light"],
        ["enable_credits", "1"],
        ["enable_multilingual", "1"],
        ["enable_achievements", "1"],
        ["initial_credits", "20"],
        ["donor_view_cost", "2"],
        ["referral_credits", "10"]
    ];
    
    foreach ($settings as $setting) {
        $name = $setting[0];
        $value = $setting[1];
        $check_sql = "SELECT * FROM settings WHERE setting_name = '$name'";
        $result = mysqli_query($conn, $check_sql);
        
        if (mysqli_num_rows($result) > 0) {
            $sql = "UPDATE settings SET setting_value = '$value' WHERE setting_name = '$name'";
        } else {
            $sql = "INSERT INTO settings (setting_name, setting_value) VALUES ('$name', '$value')";
        }
        
        if (!mysqli_query($conn, $sql)) {
            throw new Exception("Error updating setting '$name': " . mysqli_error($conn));
        }
    }
    
    // Commit the transaction
    mysqli_commit($conn);
    
    echo "<div style='background-color: #dff0d8; color: #3c763d; padding: 15px; margin: 20px 0; border: 1px solid #d6e9c6; border-radius: 4px;'>";
    echo "<h4 style='margin-top: 0;'>Database Update Successful</h4>";
    echo "<p>All required tables and data have been created successfully.</p>";
    echo "<a href='admin/dashboard.php' style='display: inline-block; margin-top: 15px; padding: 8px 12px; background-color: #3c763d; color: white; text-decoration: none; border-radius: 4px;'>Go to Admin Dashboard</a>";
    echo "</div>";
    
} catch (Exception $e) {
    // Roll back the transaction on error
    mysqli_rollback($conn);
    
    echo "<div style='background-color: #f2dede; color: #a94442; padding: 15px; margin: 20px 0; border: 1px solid #ebccd1; border-radius: 4px;'>";
    echo "<h4 style='margin-top: 0;'>Database Update Failed</h4>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<a href='index.php' style='display: inline-block; margin-top: 15px; padding: 8px 12px; background-color: #a94442; color: white; text-decoration: none; border-radius: 4px;'>Go to Homepage</a>";
    echo "</div>";
}
?>
