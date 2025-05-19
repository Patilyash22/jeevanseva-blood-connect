
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donor;
use App\Models\User;
use App\Models\BloodRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get counts for dashboard
        $donorsCount = Donor::count();
        $usersCount = User::count();
        $requestsCount = BloodRequest::count();
        $activeRequestsCount = BloodRequest::where('status', 'active')->count();
        
        // Get recent activity
        $recentDonors = Donor::orderBy('created_at', 'desc')->take(5)->get();
        $recentRequests = BloodRequest::orderBy('created_at', 'desc')->take(5)->get();
        
        // Get blood group distribution data
        $bloodGroupData = Donor::select('blood_group', DB::raw('count(*) as count'))
            ->groupBy('blood_group')
            ->get()
            ->pluck('count', 'blood_group')
            ->toArray();
        
        return view('admin.dashboard', compact(
            'donorsCount', 
            'usersCount', 
            'requestsCount', 
            'activeRequestsCount', 
            'recentDonors', 
            'recentRequests',
            'bloodGroupData'
        ));
    }
}
