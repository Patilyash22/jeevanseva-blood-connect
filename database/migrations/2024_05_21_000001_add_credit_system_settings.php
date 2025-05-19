
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = [
            ['setting_name' => 'signup_bonus', 'setting_value' => '20'],
            ['setting_name' => 'referral_bonus', 'setting_value' => '10'],
            ['setting_name' => 'donor_view_cost', 'setting_value' => '2'],
        ];
        
        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['setting_name' => $setting['setting_name']],
                ['setting_value' => $setting['setting_value']]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')
            ->whereIn('setting_name', ['signup_bonus', 'referral_bonus', 'donor_view_cost'])
            ->delete();
    }
};
