
<?php

namespace App\Helpers;

use App\Models\Setting;
use App\Services\UserCreditService;

/**
 * Common credit-related functions for PHP pages
 */
class CreditFunctions
{
    /**
     * Process credit transaction for a user
     *
     * @param int $userId
     * @param int $amount
     * @param string $type
     * @param string $description
     * @param int|null $referenceId
     * @return bool
     */
    public static function processCredits($userId, $amount, $type, $description, $referenceId = null)
    {
        global $conn;
        
        // Begin transaction
        mysqli_begin_transaction($conn);
        
        try {
            // Update user credits
            $sql = "UPDATE users SET credits = credits + ($amount) WHERE id = $userId";
            if (!mysqli_query($conn, $sql)) {
                throw new \Exception("Failed to update user credits: " . mysqli_error($conn));
            }
            
            // Add transaction record
            $refIdPart = $referenceId ? ", reference_id" : "";
            $refIdValue = $referenceId ? ", $referenceId" : "";
            
            $sql = "INSERT INTO credits (user_id, amount, transaction_type, description$refIdPart) 
                    VALUES ($userId, $amount, '$type', '$description'$refIdValue)";
            
            if (!mysqli_query($conn, $sql)) {
                throw new \Exception("Failed to record credit transaction: " . mysqli_error($conn));
            }
            
            // Record user activity
            $metadata = json_encode([
                'amount' => $amount,
                'type' => $type,
                'reference_id' => $referenceId
            ]);
            
            $sql = "INSERT INTO user_activities (user_id, activity_type, description, metadata, created_at) 
                    VALUES ($userId, 'credit_transaction', '$description', '$metadata', NOW())";
            
            if (!mysqli_query($conn, $sql)) {
                throw new \Exception("Failed to record user activity: " . mysqli_error($conn));
            }
            
            // Commit transaction
            mysqli_commit($conn);
            
            // Update session credits if this is the current user
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $userId) {
                $_SESSION['credits'] += $amount;
            }
            
            return true;
        } catch (\Exception $e) {
            // Roll back on error
            mysqli_rollback($conn);
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if user has enough credits
     *
     * @param int $userId
     * @param int $requiredCredits
     * @return bool
     */
    public static function hasEnoughCredits($userId, $requiredCredits)
    {
        global $conn;
        
        $sql = "SELECT credits FROM users WHERE id = $userId";
        $result = mysqli_query($conn, $sql);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return $row['credits'] >= $requiredCredits;
        }
        
        return false;
    }
    
    /**
     * Check if user has already viewed a donor
     *
     * @param int $userId
     * @param int $donorId
     * @return bool
     */
    public static function hasViewedDonor($userId, $donorId)
    {
        global $conn;
        
        $sql = "SELECT * FROM credits 
                WHERE user_id = $userId 
                AND reference_id = $donorId 
                AND transaction_type = 'view_donor'";
        
        $result = mysqli_query($conn, $sql);
        return ($result && mysqli_num_rows($result) > 0);
    }
    
    /**
     * Process signup bonus
     *
     * @param int $userId
     * @return bool
     */
    public static function addSignupBonus($userId)
    {
        $bonusAmount = (int)getSetting('signup_bonus', 20);
        return self::processCredits(
            $userId,
            $bonusAmount,
            'signup_bonus',
            'Welcome bonus for new registration'
        );
    }
    
    /**
     * Process referral bonus
     *
     * @param int $referrerId
     * @param int $newUserId
     * @param string $newUserName
     * @return bool
     */
    public static function addReferralBonus($referrerId, $newUserId, $newUserName)
    {
        $bonusAmount = (int)getSetting('referral_bonus', 10);
        return self::processCredits(
            $referrerId,
            $bonusAmount,
            'referral_bonus',
            "Referral bonus for user $newUserName",
            $newUserId
        );
    }
    
    /**
     * Get user credit summary
     *
     * @param int $userId
     * @return array
     */
    public static function getUserCreditSummary($userId)
    {
        global $conn;
        
        $total = 0;
        $spent = 0;
        $earned = 0;
        $transactions = [];
        
        // Get total credits
        $sql = "SELECT credits FROM users WHERE id = $userId";
        $result = mysqli_query($conn, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $total = (int)$row['credits'];
        }
        
        // Get credit metrics
        $sql = "SELECT 
                SUM(CASE WHEN amount < 0 THEN ABS(amount) ELSE 0 END) as spent,
                SUM(CASE WHEN amount > 0 THEN amount ELSE 0 END) as earned
                FROM credits 
                WHERE user_id = $userId";
        
        $result = mysqli_query($conn, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $spent = (int)$row['spent'];
            $earned = (int)$row['earned'];
        }
        
        // Get recent transactions
        $sql = "SELECT * FROM credits 
                WHERE user_id = $userId 
                ORDER BY created_at DESC 
                LIMIT 10";
        
        $result = mysqli_query($conn, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $transactions[] = $row;
            }
        }
        
        return [
            'total' => $total,
            'spent' => $spent,
            'earned' => $earned,
            'transactions' => $transactions
        ];
    }
}
