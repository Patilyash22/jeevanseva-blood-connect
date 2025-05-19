
<?php

namespace App\Helpers;

use App\Models\Credit;
use App\Models\UserActivity;
use App\Models\Setting;

class CreditHelper
{
    /**
     * Process credit transaction
     *
     * @param int $userId
     * @param int $amount
     * @param string $transactionType
     * @param string $description
     * @param int|null $referenceId
     * @return Credit|bool
     */
    public static function processCredits($userId, $amount, $transactionType, $description = null, $referenceId = null)
    {
        if ($amount == 0) {
            return true;
        }

        // If spending credits, check if user has enough
        if ($amount < 0) {
            $currentBalance = Credit::where('user_id', $userId)->sum('amount');
            if ($currentBalance < abs($amount)) {
                return false;
            }
        }

        $credit = Credit::create([
            'user_id' => $userId,
            'amount' => $amount,
            'transaction_type' => $transactionType,
            'description' => $description,
            'reference_id' => $referenceId,
        ]);

        // Log activity
        $activityDescription = sprintf(
            '%s credits %s. New balance: %d',
            abs($amount),
            $amount > 0 ? 'added' : 'deducted',
            Credit::where('user_id', $userId)->sum('amount')
        );

        UserActivity::log($userId, 'credit_transaction', $activityDescription);

        return $credit;
    }

    /**
     * Process signup bonus
     *
     * @param int $userId
     * @return Credit|bool
     */
    public static function processSignupBonus($userId)
    {
        $signupBonus = (int)Setting::get('signup_bonus', 20);
        
        if ($signupBonus <= 0) {
            return true;
        }
        
        return self::processCredits(
            $userId,
            $signupBonus,
            'signup_bonus',
            'Welcome bonus for new registration'
        );
    }

    /**
     * Process referral bonus
     *
     * @param int $referrerId
     * @param int $referredId
     * @return Credit|bool
     */
    public static function processReferralBonus($referrerId, $referredId)
    {
        $referralBonus = (int)Setting::get('referral_bonus', 10);
        
        if ($referralBonus <= 0) {
            return true;
        }
        
        return self::processCredits(
            $referrerId,
            $referralBonus,
            'referral_bonus',
            'Referral bonus for new user',
            $referredId
        );
    }

    /**
     * Get user's credit balance
     *
     * @param int $userId
     * @return int
     */
    public static function getUserBalance($userId)
    {
        return Credit::where('user_id', $userId)->sum('amount');
    }
}
