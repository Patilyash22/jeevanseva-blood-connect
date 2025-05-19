
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
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
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
}
