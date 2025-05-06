
<?php
require_once 'config.php';

// Check if tables already exist
$tables_exist = false;
$result = mysqli_query($conn, "SHOW TABLES");
if (mysqli_num_rows($result) > 0) {
    $tables_exist = true;
}

// Only continue if tables don't exist
if (!$tables_exist) {
    // Create users table
    $sql = "CREATE TABLE users (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        is_admin TINYINT(1) NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (mysqli_query($conn, $sql)) {
        echo "Table 'users' created successfully<br>";
    } else {
        echo "Error creating table 'users': " . mysqli_error($conn) . "<br>";
    }
    
    // Create donors table
    $sql = "CREATE TABLE donors (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
        location VARCHAR(255) NOT NULL,
        age INT(3) NOT NULL,
        weight INT(3) NOT NULL,
        last_donation_date DATE,
        medical_conditions TEXT,
        status ENUM('active', 'inactive', 'pending') NOT NULL DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (mysqli_query($conn, $sql)) {
        echo "Table 'donors' created successfully<br>";
    } else {
        echo "Error creating table 'donors': " . mysqli_error($conn) . "<br>";
    }
    
    // Create testimonials table
    $sql = "CREATE TABLE testimonials (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        role VARCHAR(100) NOT NULL,
        quote TEXT NOT NULL,
        avatar VARCHAR(255),
        is_approved TINYINT(1) NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (mysqli_query($conn, $sql)) {
        echo "Table 'testimonials' created successfully<br>";
    } else {
        echo "Error creating table 'testimonials': " . mysqli_error($conn) . "<br>";
    }
    
    // Create settings table
    $sql = "CREATE TABLE settings (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        setting_name VARCHAR(100) NOT NULL UNIQUE,
        setting_value TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if (mysqli_query($conn, $sql)) {
        echo "Table 'settings' created successfully<br>";
    } else {
        echo "Error creating table 'settings': " . mysqli_error($conn) . "<br>";
    }
    
    // Create faqs table
    $sql = "CREATE TABLE faqs (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        question TEXT NOT NULL,
        answer TEXT NOT NULL,
        display_order INT(11) NOT NULL DEFAULT 0,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (mysqli_query($conn, $sql)) {
        echo "Table 'faqs' created successfully<br>";
    } else {
        echo "Error creating table 'faqs': " . mysqli_error($conn) . "<br>";
    }
    
    // Insert default admin user
    $default_username = "admin";
    $default_password = password_hash("admin123", PASSWORD_DEFAULT);
    $default_email = "admin@jeevanseva.com";
    
    $sql = "INSERT INTO users (username, password, email, is_admin) VALUES ('$default_username', '$default_password', '$default_email', 1)";
    
    if (mysqli_query($conn, $sql)) {
        echo "Default admin user created successfully<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
    } else {
        echo "Error creating default admin user: " . mysqli_error($conn) . "<br>";
    }
    
    // Insert default settings
    $default_settings = [
        ["show_donor_count", "1"],
        ["show_testimonials", "1"],
        ["show_compatibility_matrix", "1"],
        ["site_title", "JeevanSeva - Blood Donation Platform"],
        ["site_tagline", "Donate Blood, Save Lives"],
        ["hero_description", "JeevanSeva connects blood donors with those in need. Join our community and become a lifesaver."],
        ["current_theme", "light"]
    ];
    
    foreach ($default_settings as $setting) {
        $name = $setting[0];
        $value = $setting[1];
        $sql = "INSERT INTO settings (setting_name, setting_value) VALUES ('$name', '$value')";
        
        if (mysqli_query($conn, $sql)) {
            echo "Setting '$name' created successfully<br>";
        } else {
            echo "Error creating setting '$name': " . mysqli_error($conn) . "<br>";
        }
    }
    
    // Create default FAQs
    $default_faqs = [
        [
            "How can I become a blood donor?", 
            "To become a blood donor, simply navigate to our 'Become a Donor' page and fill out the registration form. You'll need to provide basic personal information and contact details."
        ],
        [
            "Who can donate blood?", 
            "Generally, anyone between 18-65 years old, weighing at least 50 kg, and in good health can donate blood. You should not have any active infections or blood-borne diseases."
        ],
        [
            "How often can I donate blood?", 
            "Most healthy adults can donate blood every 12 weeks (3 months). This timeframe allows your body to replenish the red blood cells that are lost during donation."
        ]
    ];
    
    foreach ($default_faqs as $index => $faq) {
        $question = $faq[0];
        $answer = $faq[1];
        $order = $index + 1;
        $sql = "INSERT INTO faqs (question, answer, display_order) VALUES ('$question', '$answer', '$order')";
        
        if (mysqli_query($conn, $sql)) {
            echo "FAQ created successfully<br>";
        } else {
            echo "Error creating FAQ: " . mysqli_error($conn) . "<br>";
        }
    }
    
    echo "<br><strong>Installation completed successfully!</strong><br>";
    echo "<a href='index.php'>Go to homepage</a> | <a href='admin/login.php'>Go to admin login</a>";
} else {
    echo "Database tables already exist. Installation aborted to prevent data loss.<br>";
    echo "<a href='index.php'>Go to homepage</a>";
}
?>
