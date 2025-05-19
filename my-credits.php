
<?php
require_once 'config.php';
require_once 'app/Helpers/CreditFunctions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    setMessage("Please login to view your credits", "warning");
    redirect('login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$user_id = $_SESSION['user_id'];

// Get credit summary
$creditSummary = App\Helpers\CreditFunctions::getUserCreditSummary($user_id);

// Handle promo code submission
$promo_message = '';
$promo_status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['redeem_code'])) {
    $promo_code = sanitize($_POST['promo_code']);
    
    if (empty($promo_code)) {
        $promo_message = "Please enter a promo code";
        $promo_status = "error";
    } else {
        // Check if the promo code is valid
        $sql = "SELECT * FROM promo_codes WHERE code = '$promo_code' AND is_active = 1 AND (expires_at IS NULL OR expires_at > NOW())";
        $result = mysqli_query($conn, $sql);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $promo = mysqli_fetch_assoc($result);
            $credit_amount = $promo['credit_amount'];
            
            // Mark the promo code as used if it's single-use
            if ($promo['is_single_use']) {
                $sql = "UPDATE promo_codes SET is_active = 0, used_at = NOW(), used_by = $user_id WHERE id = {$promo['id']}";
                mysqli_query($conn, $sql);
            }
            
            // Add credits to user
            $success = App\Helpers\CreditFunctions::processCredits(
                $user_id,
                $credit_amount,
                'promo_code',
                "Redeemed promo code: $promo_code"
            );
            
            if ($success) {
                $promo_message = "Successfully redeemed promo code for $credit_amount credits!";
                $promo_status = "success";
                // Refresh credit summary
                $creditSummary = App\Helpers\CreditFunctions::getUserCreditSummary($user_id);
            } else {
                $promo_message = "Failed to process credits. Please try again.";
                $promo_status = "error";
            }
        } else {
            $promo_message = "Invalid or expired promo code";
            $promo_status = "error";
        }
    }
}

include 'includes/header.php';
?>

<section class="credits-section py-8 bg-white">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold mb-2 text-jeevanseva-darkred">My Credits</h1>
        <p class="text-jeevanseva-gray mb-8">Manage your JeevanSeva credits and view transaction history.</p>
        
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Credits Summary -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                    <div class="text-center">
                        <div class="inline-flex justify-center items-center w-16 h-16 bg-jeevanseva-light rounded-full mb-4">
                            <i class="fas fa-coins text-2xl text-jeevanseva-red"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800"><?php echo $creditSummary['total']; ?></h2>
                        <p class="text-gray-500 text-sm">Available Credits</p>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Earned</span>
                            <span class="font-semibold text-green-600">+<?php echo $creditSummary['earned']; ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Spent</span>
                            <span class="font-semibold text-red-600">-<?php echo $creditSummary['spent']; ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Buy Credits -->
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                    <h3 class="font-semibold mb-4 flex items-center">
                        <i class="fas fa-shopping-cart text-jeevanseva-red mr-2"></i> Buy Credits
                    </h3>
                    <p class="text-gray-600 text-sm mb-4">Need more credits? Purchase credits to view more donors.</p>
                    <a href="buy-credits.php" class="bg-jeevanseva-red hover:bg-jeevanseva-darkred text-white py-2 px-4 rounded-md font-medium transition block text-center">
                        Buy Now
                    </a>
                </div>
                
                <!-- Redeem Code -->
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                    <h3 class="font-semibold mb-4 flex items-center">
                        <i class="fas fa-gift text-jeevanseva-red mr-2"></i> Redeem Code
                    </h3>
                    
                    <?php if ($promo_message): ?>
                        <div class="mb-4 p-3 rounded text-sm <?php echo $promo_status === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                            <?php echo $promo_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="space-y-3">
                        <div class="form-group">
                            <label for="promo_code" class="block text-gray-700 text-sm mb-1">Promo Code</label>
                            <input type="text" id="promo_code" name="promo_code" placeholder="Enter code" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-jeevanseva-red focus:border-jeevanseva-red">
                        </div>
                        <button type="submit" name="redeem_code" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 py-2 px-4 rounded-md font-medium transition">
                            Redeem
                        </button>
                    </form>
                </div>
                
                <!-- Referral -->
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                    <h3 class="font-semibold mb-4 flex items-center">
                        <i class="fas fa-user-plus text-jeevanseva-red mr-2"></i> Refer Friends
                    </h3>
                    <p class="text-gray-600 text-sm mb-4">Share your referral code and earn <?php echo getSetting('referral_bonus', 10); ?> credits for each new signup!</p>
                    
                    <?php
                    // Get user's referral code
                    $sql = "SELECT referral_code FROM users WHERE id = $user_id";
                    $result = mysqli_query($conn, $sql);
                    $referral_code = '';
                    
                    if ($result && mysqli_num_rows($result) > 0) {
                        $row = mysqli_fetch_assoc($result);
                        $referral_code = $row['referral_code'];
                    }
                    ?>
                    
                    <div class="bg-gray-100 p-3 rounded-md flex items-center justify-between mb-3">
                        <span id="referral-code" class="font-mono font-medium"><?php echo $referral_code; ?></span>
                        <button onclick="copyReferralCode()" class="text-jeevanseva-red hover:text-jeevanseva-darkred">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    
                    <div class="flex space-x-2">
                        <a href="https://wa.me/?text=Use%20my%20referral%20code%20<?php echo $referral_code; ?>%20to%20get%20started%20on%20JeevanSeva%21%20<?php echo $site_url; ?>%2Fuser-registration.php%3Fref%3D<?php echo $referral_code; ?>" 
                           target="_blank" class="flex-1 bg-green-500 hover:bg-green-600 text-white py-2 px-3 rounded-md font-medium transition text-center text-sm">
                            <i class="fab fa-whatsapp mr-1"></i> WhatsApp
                        </a>
                        <button onclick="shareReferral()" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 px-3 rounded-md font-medium transition text-center text-sm">
                            <i class="fas fa-share-alt mr-1"></i> Share
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Transaction History -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="font-semibold text-lg flex items-center">
                            <i class="fas fa-history text-jeevanseva-red mr-2"></i> Transaction History
                        </h3>
                    </div>
                    
                    <?php if (count($creditSummary['transactions']) > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction</th>
                                        <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th class="py-3 px-6 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($creditSummary['transactions'] as $transaction): ?>
                                        <tr class="border-t border-gray-200 hover:bg-gray-50">
                                            <td class="py-4 px-6 text-sm text-gray-500">
                                                <?php echo date('M d, Y H:i', strtotime($transaction['created_at'])); ?>
                                            </td>
                                            <td class="py-4 px-6 text-sm">
                                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                                    <?php echo getTransactionTypeClass($transaction['transaction_type']); ?>">
                                                    <?php echo formatTransactionType($transaction['transaction_type']); ?>
                                                </span>
                                            </td>
                                            <td class="py-4 px-6 text-sm text-gray-800">
                                                <?php echo $transaction['description']; ?>
                                            </td>
                                            <td class="py-4 px-6 text-sm text-right font-medium <?php echo $transaction['amount'] > 0 ? 'text-green-600' : 'text-red-600'; ?>">
                                                <?php echo ($transaction['amount'] > 0 ? '+' : '') . $transaction['amount']; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php if (count($creditSummary['transactions']) >= 10): ?>
                            <div class="p-4 text-center border-t border-gray-200">
                                <a href="credit-history.php" class="text-jeevanseva-red hover:text-jeevanseva-darkred text-sm font-medium">
                                    View Full History <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="p-12 text-center">
                            <div class="inline-flex justify-center items-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                                <i class="fas fa-receipt text-gray-400 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">No transactions yet</h3>
                            <p class="text-gray-500 max-w-md mx-auto">
                                You haven't made any credit transactions yet. Start by searching for donors or refer your friends to earn credits.
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Usage Guide -->
                <div class="mt-6 bg-jeevanseva-light p-6 rounded-lg">
                    <h3 class="text-lg font-semibold mb-3 text-gray-800">How Credits Work</h3>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-jeevanseva-red mt-1 mr-2"></i>
                            <span>New users receive <strong><?php echo getSetting('signup_bonus', 20); ?> credits</strong> upon registration</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-jeevanseva-red mt-1 mr-2"></i>
                            <span>Viewing a donor's contact information costs <strong><?php echo getSetting('donor_view_cost', 2); ?> credits</strong></span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-jeevanseva-red mt-1 mr-2"></i>
                            <span>Earn <strong><?php echo getSetting('referral_bonus', 10); ?> credits</strong> for each friend who signs up using your referral code</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-jeevanseva-red mt-1 mr-2"></i>
                            <span>Purchase additional credits if needed to continue using the service</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function copyReferralCode() {
    var code = document.getElementById('referral-code').innerText;
    navigator.clipboard.writeText(code).then(function() {
        alert('Referral code copied to clipboard!');
    });
}

function shareReferral() {
    var code = document.getElementById('referral-code').innerText;
    var shareUrl = '<?php echo $site_url; ?>/user-registration.php?ref=' + code;
    var shareText = 'Use my referral code ' + code + ' to get started on JeevanSeva!';
    
    if (navigator.share) {
        navigator.share({
            title: 'JeevanSeva Referral',
            text: shareText,
            url: shareUrl
        })
        .catch(console.error);
    } else {
        window.open('mailto:?subject=Join JeevanSeva&body=' + encodeURIComponent(shareText + ' ' + shareUrl), '_blank');
    }
}
</script>

<?php
// Helper functions for transaction display
function formatTransactionType($type) {
    $labels = [
        'signup_bonus' => 'Signup Bonus',
        'referral_bonus' => 'Referral Bonus',
        'view_donor' => 'Donor View',
        'purchase' => 'Purchase',
        'promo_code' => 'Promo Code',
        'admin_adjustment' => 'Adjustment',
    ];
    
    return isset($labels[$type]) ? $labels[$type] : ucfirst(str_replace('_', ' ', $type));
}

function getTransactionTypeClass($type) {
    switch ($type) {
        case 'signup_bonus':
        case 'referral_bonus':
        case 'promo_code':
            return 'bg-green-100 text-green-800';
        case 'view_donor':
            return 'bg-blue-100 text-blue-800';
        case 'purchase':
            return 'bg-purple-100 text-purple-800';
        case 'admin_adjustment':
            return 'bg-yellow-100 text-yellow-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

include 'includes/footer.php';
?>
