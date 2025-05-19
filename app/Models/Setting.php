
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
    
    public static function get($key, $default = null)
    {
        $setting = static::where('setting_name', $key)->first();
        
        if ($setting) {
            return $setting->setting_value;
        }
        
        return $default;
    }
    
    public static function set($key, $value)
    {
        return static::updateOrCreate(
            ['setting_name' => $key],
            ['setting_value' => $value]
        );
    }
}
