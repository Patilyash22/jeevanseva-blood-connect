
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'phone',
        'location',
        'is_admin',
        'credits',
        'permissions',
        'referral_code',
        'referred_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
        'credits' => 'integer',
        'permissions' => 'json',
        'last_login' => 'datetime',
    ];
    
    public function donor()
    {
        return $this->hasOne(Donor::class);
    }
    
    public function bloodRequests()
    {
        return $this->hasMany(BloodRequest::class);
    }
    
    public function activities()
    {
        return $this->hasMany(UserActivity::class);
    }
    
    public function creditTransactions()
    {
        return $this->hasMany(Credit::class);
    }
    
    public function hasPermission($permission)
    {
        // Admin has all permissions
        if ($this->is_admin) {
            return true;
        }
        
        if (empty($this->permissions)) {
            return false;
        }
        
        $permissions = $this->permissions;
        
        // Check for 'all' permission
        if (isset($permissions['all']) && $permissions['all']) {
            return true;
        }
        
        // Check for specific permission
        return isset($permissions[$permission]) && $permissions[$permission];
    }
    
    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }
    
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }
    
    public function processCredits($amount, $type, $description, $reference_id = null)
    {
        // Begin transaction
        \DB::beginTransaction();
        
        try {
            // Update user credits
            $this->credits += $amount;
            $this->save();
            
            // Add transaction record
            Credit::create([
                'user_id' => $this->id,
                'amount' => $amount,
                'transaction_type' => $type,
                'description' => $description,
                'reference_id' => $reference_id,
            ]);
            
            // Commit transaction
            \DB::commit();
            
            return true;
        } catch (\Exception $e) {
            // Roll back on error
            \DB::rollback();
            \Log::error($e->getMessage());
            return false;
        }
    }
}
