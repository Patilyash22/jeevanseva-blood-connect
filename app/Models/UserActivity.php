
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'activity_type',
        'ip_address',
        'user_agent',
        'description',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Log user activity
     *
     * @param int $userId
     * @param string $activityType
     * @param string $description
     * @return UserActivity
     */
    public static function log($userId, $activityType, $description = null)
    {
        return self::create([
            'user_id' => $userId,
            'activity_type' => $activityType,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
    
    /**
     * Get recent activities for user
     *
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getRecentForUser($userId, $limit = 10)
    {
        return self::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
    }
}
