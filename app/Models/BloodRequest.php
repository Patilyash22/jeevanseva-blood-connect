
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloodRequest extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'blood_group',
        'location',
        'hospital_name',
        'patient_name',
        'urgency',
        'units_required',
        'contact_phone',
        'additional_info',
        'is_public',
        'status',
        'fulfilled_date',
    ];
    
    protected $casts = [
        'is_public' => 'boolean',
        'units_required' => 'integer',
        'fulfilled_date' => 'datetime',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function getUrgencyLevelClass()
    {
        switch ($this->urgency) {
            case 'critical':
                return 'danger';
            case 'urgent':
                return 'warning';
            default:
                return 'info';
        }
    }
    
    public function isFulfilled()
    {
        return $this->status === 'fulfilled';
    }
}
