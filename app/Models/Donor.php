
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'email',
        'phone',
        'blood_group',
        'location',
        'age',
        'gender',
        'weight',
        'last_donation_date',
        'user_id',
        'status',
        'additional_info',
        'avatar'
    ];
    
    protected $casts = [
        'last_donation_date' => 'date',
        'age' => 'integer',
        'weight' => 'integer',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function isEligibleToDonate()
    {
        if ($this->last_donation_date === null) {
            return true;
        }
        
        // Check if last donation was more than 3 months ago
        return $this->last_donation_date->diffInMonths(now()) >= 3;
    }
    
    public function getDaysUntilEligible()
    {
        if ($this->isEligibleToDonate()) {
            return 0;
        }
        
        $eligibleDate = $this->last_donation_date->addMonths(3);
        return now()->diffInDays($eligibleDate, false);
    }
}
