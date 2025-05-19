
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Credit extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'amount',
        'transaction_type',
        'description',
        'reference_id',
    ];
    
    protected $casts = [
        'amount' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * The transaction types available
     */
    const TYPE_SIGNUP_BONUS = 'signup_bonus';
    const TYPE_REFERRAL_BONUS = 'referral_bonus';
    const TYPE_VIEW_DONOR = 'view_donor';
    const TYPE_PURCHASE = 'purchase';
    const TYPE_PROMO_CODE = 'promo_code';
    const TYPE_ADMIN_ADJUSTMENT = 'admin_adjustment';
    
    /**
     * Get the user that owns the credit transaction
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the referenced entity if applicable
     */
    public function reference()
    {
        if ($this->transaction_type === self::TYPE_VIEW_DONOR && $this->reference_id) {
            return Donor::find($this->reference_id);
        }
        
        if ($this->transaction_type === self::TYPE_REFERRAL_BONUS && $this->reference_id) {
            return User::find($this->reference_id);
        }
        
        return null;
    }
    
    /**
     * Get formatted transaction type
     */
    public function getFormattedTypeAttribute()
    {
        $labels = [
            self::TYPE_SIGNUP_BONUS => 'Signup Bonus',
            self::TYPE_REFERRAL_BONUS => 'Referral Bonus',
            self::TYPE_VIEW_DONOR => 'Donor View',
            self::TYPE_PURCHASE => 'Purchase',
            self::TYPE_PROMO_CODE => 'Promo Code',
            self::TYPE_ADMIN_ADJUSTMENT => 'Admin Adjustment',
        ];
        
        return $labels[$this->transaction_type] ?? ucfirst(str_replace('_', ' ', $this->transaction_type));
    }
    
    /**
     * Get credits summary for a user
     *
     * @param int $userId
     * @return array
     */
    public static function getUserSummary($userId)
    {
        $total = self::where('user_id', $userId)->sum('amount');
        $spent = self::where('user_id', $userId)->where('amount', '<', 0)->sum('amount') * -1;
        $earned = self::where('user_id', $userId)->where('amount', '>', 0)->sum('amount');
        
        return [
            'total' => $total,
            'spent' => $spent,
            'earned' => $earned,
            'transactions' => self::where('user_id', $userId)
                                ->orderBy('created_at', 'desc')
                                ->limit(10)
                                ->get()
        ];
    }
    
    /**
     * Get full transaction history for a user with pagination
     *
     * @param int $userId
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public static function getUserHistory($userId, $perPage = 20)
    {
        return self::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);
    }
    
    /**
     * Check if a user has viewed a particular donor
     *
     * @param int $userId
     * @param int $donorId
     * @return bool
     */
    public static function hasViewedDonor($userId, $donorId)
    {
        return self::where('user_id', $userId)
                ->where('reference_id', $donorId)
                ->where('transaction_type', self::TYPE_VIEW_DONOR)
                ->exists();
    }
    
    /**
     * Create a transaction record for viewing a donor
     *
     * @param int $userId
     * @param int $donorId
     * @param int $cost
     * @return Credit
     */
    public static function recordDonorView($userId, $donorId, $cost)
    {
        return self::create([
            'user_id' => $userId,
            'amount' => -$cost,
            'transaction_type' => self::TYPE_VIEW_DONOR,
            'description' => 'Viewed donor #' . $donorId,
            'reference_id' => $donorId
        ]);
    }
    
    /**
     * Create a transaction record for signup bonus
     *
     * @param int $userId
     * @param int $amount
     * @return Credit
     */
    public static function recordSignupBonus($userId, $amount)
    {
        return self::create([
            'user_id' => $userId,
            'amount' => $amount,
            'transaction_type' => self::TYPE_SIGNUP_BONUS,
            'description' => 'Welcome bonus for new registration'
        ]);
    }
    
    /**
     * Create a transaction record for referral bonus
     *
     * @param int $referrerId
     * @param int $referredId
     * @param int $amount
     * @param string $referredUsername
     * @return Credit
     */
    public static function recordReferralBonus($referrerId, $referredId, $amount, $referredUsername)
    {
        return self::create([
            'user_id' => $referrerId,
            'amount' => $amount,
            'transaction_type' => self::TYPE_REFERRAL_BONUS,
            'description' => 'Referral bonus for user ' . $referredUsername,
            'reference_id' => $referredId
        ]);
    }
}
