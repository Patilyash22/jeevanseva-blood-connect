
<?php
require_once 'config.php';
include 'includes/header.php';

$success_message = '';
$error_message = '';

// Process contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $subject = sanitize($_POST['subject']);
    $message = sanitize($_POST['message']);
    
    // Simple validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } else {
        // Get admin email from settings
        $admin_email = "contact@vnest.tech";
        $sql = "SELECT setting_value FROM settings WHERE setting_name = 'contact_email'";
        $result = mysqli_query($conn, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $admin_email = $row['setting_value'];
        }
        
        // In a real application, you would send an email here
        // For demo purposes, we'll just simulate a successful submission
        
        // Record the contact message in database (optional)
        $sql = "INSERT INTO contact_messages (name, email, subject, message) 
                VALUES ('$name', '$email', '$subject', '$message')";
        
        if (mysqli_query($conn, $sql)) {
            $success_message = "Thank you for your message! We will get back to you shortly.";
        } else {
            $error_message = "An error occurred while submitting your message. Please try again.";
        }
    }
}

// Get FAQs for the page
$faqs = [];
$sql = "SELECT * FROM faqs WHERE is_active = 1 ORDER BY display_order ASC";
$result = mysqli_query($conn, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $faqs[] = $row;
    }
}

// Get contact information
$phone = "+91 9529081894";
$email = "contact@vnest.tech";
$address = "VNest Technologies, Gala No. 8, Laxmi Narayan Apartment, Opposite Fish Market, Vangaon (W), Taluka Dahanu, District Palghar, Maharashtra 401103";

// Get contact info from settings
$sql = "SELECT * FROM settings WHERE setting_name IN ('contact_phone', 'contact_email', 'contact_address')";
$result = mysqli_query($conn, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['setting_name'] == 'contact_phone') {
            $phone = $row['setting_value'];
        }
        if ($row['setting_name'] == 'contact_email') {
            $email = $row['setting_value'];
        }
        if ($row['setting_name'] == 'contact_address') {
            $address = $row['setting_value'];
        }
    }
}
?>

<div class="container mx-auto px-4 md:px-6 py-8">
    <div class="max-w-5xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-center text-jeevanseva-darkred">Contact Us</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
            <!-- Contact Form -->
            <div class="bg-white p-6 md:p-8 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-6 text-jeevanseva-darkred">Send Us a Message</h2>
                
                <?php if (!empty($success_message)): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($error_message)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <form action="" method="POST" class="space-y-4">
                    <div class="form-group">
                        <label for="name">Your Name <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Your Email <span class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject <span class="text-red-500">*</span></label>
                        <input type="text" id="subject" name="subject" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Your Message <span class="text-red-500">*</span></label>
                        <textarea id="message" name="message" rows="5" class="form-control" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-full">Send Message</button>
                </form>
            </div>
            
            <!-- Contact Information -->
            <div>
                <div class="bg-white p-6 md:p-8 rounded-lg shadow-md mb-8">
                    <h2 class="text-xl font-semibold mb-6 text-jeevanseva-darkred">Contact Information</h2>
                    
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="mt-1 mr-4 text-jeevanseva-red">
                                <i class="fas fa-map-marker-alt text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-medium mb-1">Address</h3>
                                <p class="text-jeevanseva-gray"><?php echo $address; ?></p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="mt-1 mr-4 text-jeevanseva-red">
                                <i class="fas fa-phone text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-medium mb-1">Phone</h3>
                                <p class="text-jeevanseva-gray">
                                    <a href="tel:<?php echo $phone; ?>" class="hover:text-jeevanseva-red"><?php echo $phone; ?></a>
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="mt-1 mr-4 text-jeevanseva-red">
                                <i class="fas fa-envelope text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-medium mb-1">Email</h3>
                                <p class="text-jeevanseva-gray">
                                    <a href="mailto:<?php echo $email; ?>" class="hover:text-jeevanseva-red"><?php echo $email; ?></a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-jeevanseva-light p-6 md:p-8 rounded-lg">
                    <h3 class="font-medium mb-3 text-jeevanseva-darkred">Emergency Blood Request?</h3>
                    <p class="mb-4 text-jeevanseva-gray">
                        For urgent blood requirements, please call our emergency hotline or visit the Find Donor page to locate donors near you.
                    </p>
                    <div class="flex space-x-3">
                        <a href="tel:<?php echo $phone; ?>" class="bg-jeevanseva-red text-white py-2 px-4 rounded-md inline-flex items-center hover:bg-jeevanseva-darkred transition">
                            <i class="fas fa-phone mr-2"></i> Call Hotline
                        </a>
                        <a href="find-donor.php" class="border border-jeevanseva-red text-jeevanseva-red py-2 px-4 rounded-md inline-flex items-center hover:bg-jeevanseva-red hover:text-white transition">
                            <i class="fas fa-search mr-2"></i> Find Donor
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- FAQ Section -->
        <div id="faq" class="bg-white p-6 md:p-8 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-6 text-center text-jeevanseva-darkred">Frequently Asked Questions</h2>
            
            <div class="accordion">
                <?php if (count($faqs) > 0): ?>
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
                <?php else: ?>
                    <p class="text-center text-jeevanseva-gray">No FAQs available at the moment.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.form-group {
    margin-bottom: 1rem;
}

label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #4a5568;
}

.form-control {
    display: block;
    width: 100%;
    padding: 0.625rem;
    background-color: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 0.25rem;
    font-size: 1rem;
}

.form-control:focus {
    outline: none;
    border-color: #e53e3e;
    box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1);
}

.grid {
    display: grid;
}

.grid-cols-1 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
}

@media (min-width: 768px) {
    .md\:grid-cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

.gap-8 {
    gap: 2rem;
}

.space-y-4 > * + * {
    margin-top: 1rem;
}

.space-x-3 > * + * {
    margin-left: 0.75rem;
}

/* Accordion Styles (for FAQs) */
.accordion-item {
    border: 1px solid #e2e8f0;
    margin-bottom: 0.75rem;
    border-radius: 0.25rem;
    overflow: hidden;
}

.accordion-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background-color: #f9fafb;
    cursor: pointer;
}

.accordion-header h3 {
    margin: 0;
    font-size: 1rem;
    font-weight: 500;
}

.accordion-icon {
    transition: transform 0.3s ease;
}

.accordion-content {
    padding: 0;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease, padding 0.3s ease;
}

.accordion-item.active .accordion-icon {
    transform: rotate(180deg);
}

.accordion-item.active .accordion-content {
    padding: 1rem;
    max-height: 500px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Accordion functionality for FAQs
    const accordionItems = document.querySelectorAll('.accordion-item');
    
    accordionItems.forEach(item => {
        const header = item.querySelector('.accordion-header');
        
        header.addEventListener('click', () => {
            item.classList.toggle('active');
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>
