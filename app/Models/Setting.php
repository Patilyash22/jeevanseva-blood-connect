
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
        'setting_group',
        'is_public',
        'description'
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
     * @param string $group
     * @param bool $isPublic
     * @param string $description
     * @return Setting
     */
    public static function set($key, $value, $group = null, $isPublic = true, $description = null)
    {
        $data = ['setting_value' => $value];
        
        if ($group !== null) {
            $data['setting_group'] = $group;
        }
        
        if ($isPublic !== null) {
            $data['is_public'] = $isPublic;
        }
        
        if ($description !== null) {
            $data['description'] = $description;
        }
        
        return static::updateOrCreate(
            ['setting_name' => $key],
            $data
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
            'min_purchase_amount' => 10,
            'purchase_discount_percent' => 0
        ];
        
        foreach ($settings as $key => $defaultValue) {
            $value = self::get($key, $defaultValue);
            $settings[$key] = (int)$value;
        }
        
        return $settings;
    }
    
    /**
     * Get all settings by group
     *
     * @param string $group
     * @param bool $publicOnly
     * @return array
     */
    public static function getByGroup($group, $publicOnly = true)
    {
        $query = static::where('setting_group', $group);
        
        if ($publicOnly) {
            $query->where('is_public', true);
        }
        
        $settings = $query->get();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting->setting_name] = $setting->setting_value;
        }
        
        return $result;
    }
    
    /**
     * Get all settings grouped by their group
     *
     * @param bool $publicOnly
     * @return array
     */
    public static function getAllGrouped($publicOnly = true)
    {
        $query = static::select('setting_name', 'setting_value', 'setting_group', 'description');
        
        if ($publicOnly) {
            $query->where('is_public', true);
        }
        
        $settings = $query->get();
        $result = [];
        
        foreach ($settings as $setting) {
            $group = $setting->setting_group ?: 'general';
            
            if (!isset($result[$group])) {
                $result[$group] = [];
            }
            
            $result[$group][$setting->setting_name] = [
                'value' => $setting->setting_value,
                'description' => $setting->description
            ];
        }
        
        return $result;
    }
}
