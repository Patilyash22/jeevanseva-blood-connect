
<?php

namespace App\Services;

use App\Models\User;
use App\Models\Credit;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserCreditService
{
    /**
     * Process credits for a user
     * 
     * @param User $user
     * @param int $amount
     * @param string $type
     * @param string $description
     * @param int|null $referenceId
     * @return bool
     */
    public function processCredits(User $user, int $amount, string $type, string $description, ?int $referenceId = null): bool
    {
        DB::beginTransaction();
        
        try {
            // Update user credits
            $user->credits += $amount;
            $user->save();
            
            // Add transaction record
            Credit::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'transaction_type' => $type,
                'description' => $description,
                'reference_id' => $referenceId,
            ]);
            
            // Record user activity
            $user->activities()->create([
                'activity_type' => 'credit_transaction',
                'description' => $description,
                'metadata' => json_encode([
                    'amount' => $amount,
                    'type' => $type,
                    'reference_id' => $referenceId
                ])
            ]);
            
            DB::commit();
            return true;
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Credit processing failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => $type,
                'exception' => $e
            ]);
            return false;
        }
    }
    
    /**
     * Add signup bonus credits
     * 
     * @param User $user
     * @return bool
     */
    public function addSignupBonus(User $user): bool
    {
        $bonusAmount = (int)Setting::get('signup_bonus', 20);
        return $this->processCredits(
            $user, 
            $bonusAmount, 
            'signup_bonus', 
            'Welcome bonus for new registration'
        );
    }
    
    /**
     * Add referral bonus credits
     * 
     * @param User $referrer
     * @param User $newUser
     * @return bool
     */
    public function addReferralBonus(User $referrer, User $newUser): bool
    {
        $bonusAmount = (int)Setting::get('referral_bonus', 10);
        return $this->processCredits(
            $referrer, 
            $bonusAmount, 
            'referral_bonus', 
            'Referral bonus for user ' . $newUser->username,
            $newUser->id
        );
    }
    
    /**
     * Charge credits for viewing a donor
     * 
     * @param User $user
     * @param int $donorId
     * @return bool
     */
    public function chargeDonorView(User $user, int $donorId): bool
    {
        $viewCost = (int)Setting::get('donor_view_cost', 2);
        
        // Check if user has enough credits
        if ($user->credits < $viewCost) {
            return false;
        }
        
        return $this->processCredits(
            $user, 
            -$viewCost, 
            'view_donor', 
            'Viewed donor #' . $donorId,
            $donorId
        );
    }
    
    /**
     * Check if user has already paid to view a donor
     * 
     * @param User $user
     * @param int $donorId
     * @return bool
     */
    public function hasViewedDonor(User $user, int $donorId): bool
    {
        return Credit::where('user_id', $user->id)
            ->where('reference_id', $donorId)
            ->where('transaction_type', 'view_donor')
            ->exists();
    }
    
    /**
     * Check if user has enough credits for an action
     * 
     * @param User $user
     * @param int $requiredAmount
     * @return bool
     */
    public function hasEnoughCredits(User $user, int $requiredAmount): bool
    {
        return $user->credits >= $requiredAmount;
    }
}
