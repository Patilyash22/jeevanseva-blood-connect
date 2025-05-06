
<?php
require_once '../config.php';
include 'includes/header.php';

// Default settings
$settings = [
    'show_donor_count' => '1',
    'show_testimonials' => '1',
    'show_compatibility_matrix' => '1',
    'site_title' => 'JeevanSeva - Blood Donation Platform',
    'site_tagline' => 'Donate Blood, Save Lives',
    'hero_description' => 'JeevanSeva connects blood donors with those in need. Join our community and become a lifesaver.',
    'current_theme' => 'light',
    'contact_phone' => '+91 9529081894',
    'contact_email' => 'contact@vnest.tech',
    'contact_address' => 'VNest Technologies, Gala No. 8, Laxmi Narayan Apartment, Opposite Fish Market, Vangaon (W), Taluka Dahanu, District Palghar, Maharashtra 401103'
];

// Get current settings from database
$sql = "SELECT * FROM settings";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $settings[$row['setting_name']] = $row['setting_value'];
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update general settings
    if (isset($_POST['update_general'])) {
        $site_title = sanitize($_POST['site_title']);
        $site_tagline = sanitize($_POST['site_tagline']);
        $hero_description = sanitize($_POST['hero_description']);
        
        // Update each setting
        $sql = "UPDATE settings SET setting_value = '$site_title' WHERE setting_name = 'site_title'";
        mysqli_query($conn, $sql);
        
        $sql = "UPDATE settings SET setting_value = '$site_tagline' WHERE setting_name = 'site_tagline'";
        mysqli_query($conn, $sql);
        
        $sql = "UPDATE settings SET setting_value = '$hero_description' WHERE setting_name = 'hero_description'";
        mysqli_query($conn, $sql);
        
        setMessage("General settings updated successfully");
        redirect("settings.php");
    }
    
    // Update display settings
    if (isset($_POST['update_display'])) {
        $show_donor_count = isset($_POST['show_donor_count']) ? '1' : '0';
        $show_testimonials = isset($_POST['show_testimonials']) ? '1' : '0';
        $show_compatibility_matrix = isset($_POST['show_compatibility_matrix']) ? '1' : '0';
        $current_theme = sanitize($_POST['current_theme']);
        
        // Update each setting
        $sql = "UPDATE settings SET setting_value = '$show_donor_count' WHERE setting_name = 'show_donor_count'";
        mysqli_query($conn, $sql);
        
        $sql = "UPDATE settings SET setting_value = '$show_testimonials' WHERE setting_name = 'show_testimonials'";
        mysqli_query($conn, $sql);
        
        $sql = "UPDATE settings SET setting_value = '$show_compatibility_matrix' WHERE setting_name = 'show_compatibility_matrix'";
        mysqli_query($conn, $sql);
        
        $sql = "UPDATE settings SET setting_value = '$current_theme' WHERE setting_name = 'current_theme'";
        mysqli_query($conn, $sql);
        
        setMessage("Display settings updated successfully");
        redirect("settings.php");
    }
    
    // Update contact settings
    if (isset($_POST['update_contact'])) {
        $contact_phone = sanitize($_POST['contact_phone']);
        $contact_email = sanitize($_POST['contact_email']);
        $contact_address = sanitize($_POST['contact_address']);
        
        // Update each setting
        $sql = "UPDATE settings SET setting_value = '$contact_phone' WHERE setting_name = 'contact_phone'";
        mysqli_query($conn, $sql);
        
        $sql = "UPDATE settings SET setting_value = '$contact_email' WHERE setting_name = 'contact_email'";
        mysqli_query($conn, $sql);
        
        $sql = "UPDATE settings SET setting_value = '$contact_address' WHERE setting_name = 'contact_address'";
        mysqli_query($conn, $sql);
        
        setMessage("Contact settings updated successfully");
        redirect("settings.php");
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2>Site Settings</h2>
    </div>
    <div class="card-body">
        <div class="tabs">
            <div class="tab-header">
                <button class="tab-btn active" onclick="openTab(event, 'general')">General Settings</button>
                <button class="tab-btn" onclick="openTab(event, 'display')">Display Settings</button>
                <button class="tab-btn" onclick="openTab(event, 'contact')">Contact Information</button>
            </div>
            
            <!-- General Settings Tab -->
            <div id="general" class="tab-content" style="display: block;">
                <form class="admin-form" method="POST">
                    <div class="form-group">
                        <label for="site_title">Site Title</label>
                        <input type="text" id="site_title" name="site_title" value="<?php echo $settings['site_title']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="site_tagline">Site Tagline</label>
                        <input type="text" id="site_tagline" name="site_tagline" value="<?php echo $settings['site_tagline']; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="hero_description">Hero Description</label>
                        <textarea id="hero_description" name="hero_description"><?php echo $settings['hero_description']; ?></textarea>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="submit" name="update_general" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
            
            <!-- Display Settings Tab -->
            <div id="display" class="tab-content">
                <form class="admin-form" method="POST">
                    <div class="form-group checkbox-group">
                        <label class="toggle-switch">
                            <input type="checkbox" name="show_donor_count" <?php echo $settings['show_donor_count'] === '1' ? 'checked' : ''; ?>>
                            <span class="toggle-slider"></span>
                        </label>
                        <label>Show Donor Count Section on Homepage</label>
                    </div>
                    
                    <div class="form-group checkbox-group">
                        <label class="toggle-switch">
                            <input type="checkbox" name="show_testimonials" <?php echo $settings['show_testimonials'] === '1' ? 'checked' : ''; ?>>
                            <span class="toggle-slider"></span>
                        </label>
                        <label>Show Testimonials Section</label>
                    </div>
                    
                    <div class="form-group checkbox-group">
                        <label class="toggle-switch">
                            <input type="checkbox" name="show_compatibility_matrix" <?php echo $settings['show_compatibility_matrix'] === '1' ? 'checked' : ''; ?>>
                            <span class="toggle-slider"></span>
                        </label>
                        <label>Show Blood Compatibility Matrix</label>
                    </div>
                    
                    <div class="form-group">
                        <label for="current_theme">Current Theme</label>
                        <select id="current_theme" name="current_theme">
                            <option value="light" <?php echo $settings['current_theme'] === 'light' ? 'selected' : ''; ?>>Light Theme</option>
                            <option value="dark" <?php echo $settings['current_theme'] === 'dark' ? 'selected' : ''; ?>>Dark Theme</option>
                            <option value="grey" <?php echo $settings['current_theme'] === 'grey' ? 'selected' : ''; ?>>Grey Flat Theme</option>
                        </select>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="submit" name="update_display" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
            
            <!-- Contact Information Tab -->
            <div id="contact" class="tab-content">
                <form class="admin-form" method="POST">
                    <div class="form-group">
                        <label for="contact_phone">Contact Phone</label>
                        <input type="text" id="contact_phone" name="contact_phone" value="<?php echo $settings['contact_phone']; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="contact_email">Contact Email</label>
                        <input type="email" id="contact_email" name="contact_email" value="<?php echo $settings['contact_email']; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="contact_address">Contact Address</label>
                        <textarea id="contact_address" name="contact_address"><?php echo $settings['contact_address']; ?></textarea>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="submit" name="update_contact" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openTab(evt, tabId) {
    // Hide all tab contents
    var tabcontents = document.getElementsByClassName("tab-content");
    for (var i = 0; i < tabcontents.length; i++) {
        tabcontents[i].style.display = "none";
    }
    
    // Remove active class from all tab buttons
    var tablinks = document.getElementsByClassName("tab-btn");
    for (var i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    
    // Show the current tab and add an "active" class to the button
    document.getElementById(tabId).style.display = "block";
    evt.currentTarget.className += " active";
}
</script>

<style>
.tabs {
    border: 1px solid var(--admin-border);
    border-radius: 4px;
    overflow: hidden;
}

.tab-header {
    display: flex;
    background-color: #f9fafb;
    border-bottom: 1px solid var(--admin-border);
    overflow-x: auto;
}

.tab-btn {
    padding: 12px 15px;
    border: none;
    background-color: transparent;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s;
    white-space: nowrap;
}

.tab-btn:hover {
    background-color: #edf2f7;
}

.tab-btn.active {
    border-bottom: 2px solid var(--admin-primary);
    color: var(--admin-primary);
}

.tab-content {
    display: none;
    padding: 20px;
}

.tab-content.active {
    display: block;
}
</style>

<?php include 'includes/footer.php'; ?>
