
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('setting_value', 'setting_name')->toArray();
        
        // Default settings if not found in database
        $defaultSettings = [
            'show_donor_count' => '1',
            'show_testimonials' => '1',
            'show_compatibility_matrix' => '1',
            'site_title' => 'JeevanSeva - Blood Donation Platform',
            'site_tagline' => 'Donate Blood, Save Lives',
            'hero_description' => 'JeevanSeva connects blood donors with those in need. Join our community and become a lifesaver.',
            'current_theme' => 'light',
            'contact_phone' => '+91 9529081894',
            'contact_email' => 'contact@vnest.tech',
            'contact_address' => 'VNest Technologies, Gala No. 8, Laxmi Narayan Apartment, Opposite Fish Market, Vangaon (W), Taluka Dahanu, District Palghar, Maharashtra 401103',
            'donor_view_cost' => '2'
        ];
        
        // Merge defaults with database settings
        $settings = array_merge($defaultSettings, $settings);
        
        return view('admin.settings.index', compact('settings'));
    }
    
    public function update(Request $request)
    {
        $settingType = $request->input('setting_type');
        
        switch ($settingType) {
            case 'general':
                $this->updateGeneralSettings($request);
                break;
                
            case 'display':
                $this->updateDisplaySettings($request);
                break;
                
            case 'contact':
                $this->updateContactSettings($request);
                break;
                
            case 'credits':
                $this->updateCreditSettings($request);
                break;
        }
        
        return redirect()->route('admin.settings.index')
            ->with('success', ucfirst($settingType) . ' settings updated successfully');
    }
    
    private function updateGeneralSettings(Request $request)
    {
        $settings = [
            'site_title' => $request->input('site_title'),
            'site_tagline' => $request->input('site_tagline'),
            'hero_description' => $request->input('hero_description'),
        ];
        
        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['setting_name' => $key],
                ['setting_value' => $value]
            );
        }
    }
    
    private function updateDisplaySettings(Request $request)
    {
        $settings = [
            'show_donor_count' => $request->has('show_donor_count') ? '1' : '0',
            'show_testimonials' => $request->has('show_testimonials') ? '1' : '0',
            'show_compatibility_matrix' => $request->has('show_compatibility_matrix') ? '1' : '0',
            'current_theme' => $request->input('current_theme'),
        ];
        
        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['setting_name' => $key],
                ['setting_value' => $value]
            );
        }
    }
    
    private function updateContactSettings(Request $request)
    {
        $settings = [
            'contact_phone' => $request->input('contact_phone'),
            'contact_email' => $request->input('contact_email'),
            'contact_address' => $request->input('contact_address'),
        ];
        
        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['setting_name' => $key],
                ['setting_value' => $value]
            );
        }
    }
    
    private function updateCreditSettings(Request $request)
    {
        $settings = [
            'donor_view_cost' => $request->input('donor_view_cost'),
        ];
        
        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['setting_name' => $key],
                ['setting_value' => $value]
            );
        }
    }
}
