
<?php
require_once 'config.php';
include 'includes/header.php';

// Get settings
$show_donor_count = true;
$show_testimonials = true;
$show_compatibility_matrix = true;
$site_tagline = "Donate Blood, Save Lives";
$hero_description = "JeevanSeva connects blood donors with those in need. Join our community and become a lifesaver.";

// Get settings from database
$sql = "SELECT * FROM settings WHERE setting_name IN ('show_donor_count', 'show_testimonials', 'show_compatibility_matrix', 'site_tagline', 'hero_description')";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['setting_name'] == 'show_donor_count') {
            $show_donor_count = (bool)$row['setting_value'];
        }
        if ($row['setting_name'] == 'show_testimonials') {
            $show_testimonials = (bool)$row['setting_value'];
        }
        if ($row['setting_name'] == 'show_compatibility_matrix') {
            $show_compatibility_matrix = (bool)$row['setting_value'];
        }
        if ($row['setting_name'] == 'site_tagline') {
            $site_tagline = $row['setting_value'];
        }
        if ($row['setting_name'] == 'hero_description') {
            $hero_description = $row['setting_value'];
        }
    }
}

// Get donor count
$donor_count = 0;
$sql = "SELECT COUNT(*) as total FROM donors WHERE status = 'active'";
$result = mysqli_query($conn, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $donor_count = $row['total'];
}

// Get testimonials
$testimonials = [];
if ($show_testimonials) {
    $sql = "SELECT * FROM testimonials WHERE is_approved = 1 ORDER BY id DESC LIMIT 4";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $testimonials[] = $row;
        }
    }
}

// Get FAQs
$faqs = [];
$sql = "SELECT * FROM faqs WHERE is_active = 1 ORDER BY display_order ASC LIMIT 6";
$result = mysqli_query($conn, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $faqs[] = $row;
    }
}
?>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1><?php echo $site_tagline; ?></h1>
            <p><?php echo $hero_description; ?></p>
            <div class="cta-buttons">
                <a href="donor-registration.php" class="btn btn-primary"><i class="fas fa-hand-holding-medical"></i> Become a Donor</a>
                <a href="find-donor.php" class="btn btn-outline"><i class="fas fa-search"></i> Find a Donor</a>
            </div>
        </div>
        <div class="hero-image">
            <div class="blood-drop-large"></div>
            <div class="text-overlay">Give Life</div>
        </div>
    </div>
</section>

<?php if ($show_donor_count): ?>
<section class="impact-stats">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number" data-count="<?php echo $donor_count; ?>">0</div>
                <div class="stat-label">Registered Donors</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" data-count="<?php echo intval($donor_count * 0.8); ?>">0</div>
                <div class="stat-label">Lives Saved</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" data-count="<?php echo intval($donor_count / 20); ?>">0</div>
                <div class="stat-label">Blood Camps</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" data-count="<?php echo min(intval($donor_count / 200), 100); ?>">0</div>
                <div class="stat-label">Partner Hospitals</div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>
    
<section class="how-it-works">
    <div class="container">
        <h2>How It Works</h2>
        <div class="steps">
            <div class="step">
                <div class="step-number">1</div>
                <h3>Register as a Donor</h3>
                <p>Fill out a simple form with your details and become part of our donor database.</p>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <h3>Get Matched</h3>
                <p>Those in need can search for donors by location and blood group.</p>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <h3>Save Lives</h3>
                <p>When contacted, you have the opportunity to donate and save a precious life.</p>
            </div>
        </div>
    </div>
</section>

<?php if ($show_compatibility_matrix): ?>
<section class="blood-compatibility">
    <div class="container">
        <h2>Blood Group Compatibility</h2>
        <p class="section-intro">Understanding blood type compatibility is crucial for successful transfusions. 
            Select your blood group below to see who you can donate to and receive from.</p>
        
        <div class="compatibility-chart">
            <div class="blood-group-selector">
                <h3>Select Your Blood Group:</h3>
                <div class="blood-group-buttons">
                    <button class="blood-group-btn" data-group="A+">A+</button>
                    <button class="blood-group-btn" data-group="A-">A-</button>
                    <button class="blood-group-btn" data-group="B+">B+</button>
                    <button class="blood-group-btn" data-group="B-">B-</button>
                    <button class="blood-group-btn" data-group="AB+">AB+</button>
                    <button class="blood-group-btn" data-group="AB-">AB-</button>
                    <button class="blood-group-btn" data-group="O+">O+</button>
                    <button class="blood-group-btn" data-group="O-">O-</button>
                </div>
            </div>

            <div class="compatibility-table">
                <table>
                    <thead>
                        <tr>
                            <th>Blood Group</th>
                            <th>Can Donate To</th>
                            <th>Can Receive From</th>
                        </tr>
                    </thead>
                    <tbody id="compatibility-data">
                        <!-- Data will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>

            <div id="compatibility-summary" class="compatibility-summary hidden">
                <!-- Summary will be populated by JavaScript -->
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($show_testimonials && count($testimonials) > 0): ?>
<section class="testimonial-section">
    <div class="container">
        <h2>What Our Donors Say</h2>
        <div class="testimonial-carousel">
            <div class="testimonial-slides">
                <?php foreach ($testimonials as $index => $testimonial): ?>
                <div class="testimonial-slide <?php echo $index === 0 ? 'active' : ''; ?>">
                    <div class="testimonial-content">
                        <div class="testimonial-image">
                            <?php if (!empty($testimonial['avatar']) && file_exists($testimonial['avatar'])): ?>
                                <img src="<?php echo $testimonial['avatar']; ?>" alt="<?php echo $testimonial['name']; ?>">
                            <?php else: ?>
                                <img src="public/donor<?php echo ($index % 4) + 1; ?>.png" alt="<?php echo $testimonial['name']; ?>">
                            <?php endif; ?>
                        </div>
                        <p>"<?php echo $testimonial['quote']; ?>"</p>
                        <h4><?php echo $testimonial['name']; ?></h4>
                        <p class="testimonial-meta"><?php echo $testimonial['role']; ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="carousel-dots"></div>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="partner-section">
    <div class="container">
        <h2>Our Hospital Partners</h2>
        <p class="section-intro">We work with leading hospitals across India to ensure a reliable blood supply for patients in need.</p>
        <div class="partner-logos">
            <div class="partner-logo">
                <img src="public/hospital2.png" alt="Sunshine Hospital">
                <span>Sunshine Hospital</span>
            </div>
            <div class="partner-logo">
                <img src="public/hospital3.png" alt="City Medical Center">
                <span>City Medical Center</span>
            </div>
            <div class="partner-logo">
                <img src="public/hospital1.png" alt="Life Care Hospital">
                <span>Life Care Hospital</span>
            </div>
            <div class="partner-logo">
                <img src="public/hospital4.png" alt="Metro Healthcare">
                <span>Metro Healthcare</span>
            </div>
            <div class="partner-logo">
                <img src="public/hospital5.png" alt="Hope Medical Institute">
                <span>Hope Medical Institute</span>
            </div>
        </div>
    </div>
</section>

<?php if (count($faqs) > 0): ?>
<section class="faq-section">
    <div class="container">
        <h2>Frequently Asked Questions</h2>
        <div class="accordion">
            <?php foreach ($faqs as $faq): ?>
            <div class="accordion-item">
                <div class="accordion-header">
                    <h3><?php echo $faq['question']; ?></h3>
                    <span class="accordion-icon"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="accordion-content">
                    <p><?php echo $faq['answer']; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
    
<section class="cta-section">
    <div class="container">
        <div class="cta-box">
            <h2>Ready to Become a Lifesaver?</h2>
            <p>Join our community of donors and help save lives in your area.</p>
            <a href="donor-registration.php" class="btn btn-light">Register Now</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
