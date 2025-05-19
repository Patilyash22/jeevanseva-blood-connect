
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_name')->unique();
            $table->text('setting_value')->nullable();
        });
        
        // Insert default settings
        $defaultSettings = [
            ['setting_name' => 'show_donor_count', 'setting_value' => '1'],
            ['setting_name' => 'show_testimonials', 'setting_value' => '1'],
            ['setting_name' => 'show_compatibility_matrix', 'setting_value' => '1'],
            ['setting_name' => 'site_title', 'setting_value' => 'JeevanSeva - Blood Donation Platform'],
            ['setting_name' => 'site_tagline', 'setting_value' => 'Donate Blood, Save Lives'],
            ['setting_name' => 'hero_description', 'setting_value' => 'JeevanSeva connects blood donors with those in need. Join our community and become a lifesaver.'],
            ['setting_name' => 'current_theme', 'setting_value' => 'light'],
            ['setting_name' => 'contact_phone', 'setting_value' => '+91 9529081894'],
            ['setting_name' => 'contact_email', 'setting_value' => 'contact@vnest.tech'],
            ['setting_name' => 'contact_address', 'setting_value' => 'VNest Technologies, Gala No. 8, Laxmi Narayan Apartment, Opposite Fish Market, Vangaon (W), Taluka Dahanu, District Palghar, Maharashtra 401103'],
            ['setting_name' => 'donor_view_cost', 'setting_value' => '2']
        ];
        
        DB::table('settings')->insert($defaultSettings);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
