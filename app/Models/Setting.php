
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'setting_name',
        'setting_value',
    ];
    
    public $timestamps = false;
    
    /**
     * Get a setting value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        $setting = static::where('setting_name', $key)->first();
        
        if ($setting) {
            return $setting->setting_value;
        }
        
        return $default;
    }
    
    /**
     * Set a setting value
     *
     * @param string $key
     * @param mixed $value
     * @return Setting
     */
    public static function set($key, $value)
    {
        return static::updateOrCreate(
            ['setting_name' => $key],
            ['setting_value' => $value]
        );
    }
    
    /**
     * Get all credit-related settings
     *
     * @return array
     */
    public static function getCreditSettings()
    {
        $settings = [
            'signup_bonus' => 20,
            'referral_bonus' => 10,
            'donor_view_cost' => 2,
        ];
        
        foreach ($settings as $key => $defaultValue) {
            $value = self::get($key, $defaultValue);
            $settings[$key] = (int)$value;
        }
        
        return $settings;
    }
}
