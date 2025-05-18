
<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php?redirect=buy-credits.php');
}

// Initialize variables
$credit_options = [
    ['amount' => 20, 'price' => 49, 'badge' => ''],
    ['amount' => 50, 'price' => 99, 'badge' => ''],
    ['amount' => 100, 'price' => 179, 'badge' => 'Popular'],
    ['amount' => 200, 'price' => 299, 'badge' => 'Best Value'],
    ['amount' => 500, 'price' => 599, 'badge' => ''],
];

// THIS IS A PLACEHOLDER FOR PAYMENT PROCESSING
// For a real implementation, integrate with a payment gateway like Razorpay, PayTM, etc.

// Handle the "successful payment" for demonstration
$success = false;
$credits_added = 0;

if (isset($_POST['simulate_payment']) && isset($_POST['credit_package'])) {
    $package_index = (int)$_POST['credit_package'];
    
    if (isset($credit_options[$package_index])) {
        $package = $credit_options[$package_index];
        $amount = $package['amount'];
        $user_id = $_SESSION['user_id'];
        
        // Process the credit addition
        if (processCredits($user_id, $amount, 'purchase', "Purchased {$amount} credits")) {
            $success = true;
            $credits_added = $amount;
        }
    }
}

include 'includes/header.php';
?>

<section class="buy-credits-section">
    <div class="container">
        <h1>Buy Credits</h1>
        <p class="section-intro">Purchase credits to view donor contact information and support our platform.</p>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <h3>Payment Successful!</h3>
                <p>You have successfully purchased <?php echo $credits_added; ?> credits. Your credits have been added to your account.</p>
                <div class="alert-actions">
                    <a href="user-dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                    <a href="find-donor.php" class="btn btn-outline">Find Donors</a>
                </div>
            </div>
        <?php else: ?>
            <div class="credit-info">
                <div class="credit-balance">
                    <span>Your Current Balance</span>
                    <div class="balance-amount"><?php echo $_SESSION['credits']; ?> Credits</div>
                </div>
                <div class="credit-usage">
                    <p><i class="fas fa-info-circle"></i> Each donor contact view costs <strong>2 credits</strong>.</p>
                </div>
            </div>
            
            <form method="POST" class="credit-packages-form">
                <div class="credit-packages">
                    <?php foreach ($credit_options as $index => $option): ?>
                        <div class="credit-package <?php echo !empty($option['badge']) ? 'featured' : ''; ?>">
                            <?php if (!empty($option['badge'])): ?>
                                <div class="package-badge"><?php echo $option['badge']; ?></div>
                            <?php endif; ?>
                            
                            <div class="package-credits"><?php echo $option['amount']; ?> Credits</div>
                            <div class="package-price">₹<?php echo $option['price']; ?></div>
                            <div class="package-value">
                                <?php
                                $per_credit = round($option['price'] / $option['amount'], 2);
                                echo "₹" . $per_credit . " per credit";
                                ?>
                            </div>
                            <div class="package-select">
                                <input type="radio" name="credit_package" id="package_<?php echo $index; ?>" value="<?php echo $index; ?>" <?php echo $index === 2 ? 'checked' : ''; ?>>
                                <label for="package_<?php echo $index; ?>" class="btn btn-block <?php echo !empty($option['badge']) ? 'btn-primary' : 'btn-outline'; ?>">
                                    Select
                                </label>
                            </div>
                            <div class="package-donor-count">
                                View up to <?php echo floor($option['amount'] / 2); ?> donors
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="payment-methods">
                    <h3>Payment Methods</h3>
                    <div class="method-options">
                        <div class="method-option">
                            <input type="radio" name="payment_method" id="method_upi" value="upi" checked>
                            <label for="method_upi">UPI</label>
                        </div>
                        
                        <div class="method-option">
                            <input type="radio" name="payment_method" id="method_card" value="card">
                            <label for="method_card">Card</label>
                        </div>
                        
                        <div class="method-option">
                            <input type="radio" name="payment_method" id="method_netbanking" value="netbanking">
                            <label for="method_netbanking">Net Banking</label>
                        </div>
                    </div>
                </div>
                
                <div class="payment-actions">
                    <!-- IMPORTANT NOTE: This is just a simulation for demonstration purposes -->
                    <!-- In a real implementation, integrate with a payment gateway -->
                    <button type="submit" name="simulate_payment" class="btn btn-primary btn-lg">
                        Proceed to Payment
                    </button>
                </div>
            </form>
            
            <div class="payment-note">
                <h3>Why Buy Credits?</h3>
                <div class="note-columns">
                    <div class="note-column">
                        <div class="note-icon"><i class="fas fa-heart"></i></div>
                        <h4>Support Our Mission</h4>
                        <p>Your contribution helps us maintain this platform and connect more donors with patients.</p>
                    </div>
                    
                    <div class="note-column">
                        <div class="note-icon"><i class="fas fa-shield-alt"></i></div>
                        <h4>Verified Donors</h4>
                        <p>We verify all donors to ensure authenticity and maintain a reliable database.</p>
                    </div>
                    
                    <div class="note-column">
                        <div class="note-icon"><i class="fas fa-hand-holding-medical"></i></div>
                        <h4>Save Lives</h4>
                        <p>By supporting our platform, you're helping save lives through timely blood donations.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
