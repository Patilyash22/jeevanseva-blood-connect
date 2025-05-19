
<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DonorController extends Controller
{
    public function index(Request $request)
    {
        $location = $request->input('location', '');
        $blood_group = $request->input('blood_group', '');
        
        $query = Donor::where('status', 'active');
        
        if (!empty($location)) {
            $query->where('location', 'LIKE', "%{$location}%");
        }
        
        if (!empty($blood_group)) {
            $query->where('blood_group', $blood_group);
        }
        
        $donors = $query->orderBy('created_at', 'desc')->get();
        
        // Get donor view cost from settings
        $donor_view_cost = config('jeevanseva.donor_view_cost', 2);
        
        return view('donors.find', compact('donors', 'location', 'blood_group', 'donor_view_cost'));
    }
    
    public function showRegistrationForm()
    {
        return view('donors.register');
    }
    
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:donors',
            'phone' => 'required|string|max:20',
            'blood_group' => 'required|string',
            'location' => 'required|string|max:255',
            'age' => 'required|integer|min:18|max:65',
            'gender' => 'required|in:male,female,other',
        ]);
        
        $donor = Donor::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'blood_group' => $request->blood_group,
            'location' => $request->location,
            'age' => $request->age,
            'gender' => $request->gender,
            'weight' => $request->weight,
            'last_donation_date' => $request->last_donation_date,
            'user_id' => Auth::id(),
            'status' => 'pending',
        ]);
        
        return redirect()->route('donor.register.success')->with('success', 'Registration successful! Your information will be reviewed shortly.');
    }
    
    public function viewDonor(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('info', 'Please log in to view donor details');
        }
        
        $donor = Donor::findOrFail($id);
        
        // Check if user has already paid for this donor
        $has_paid = $user->creditTransactions()
            ->where('reference_id', $donor->id)
            ->where('transaction_type', 'view_donor')
            ->exists();
            
        $donor_view_cost = config('jeevanseva.donor_view_cost', 2);
        
        if (!$has_paid) {
            if ($user->credits < $donor_view_cost) {
                return redirect()->route('donors.find')
                    ->with('error', 'You don\'t have enough credits to view this donor\'s contact information. Please buy more credits.');
            }
            
            // Process credit transaction
            $result = $user->processCredits(
                -$donor_view_cost, 
                'view_donor', 
                "Viewed contact info for donor #{$donor->id}", 
                $donor->id
            );
            
            if (!$result) {
                return redirect()->route('donors.find')
                    ->with('error', 'Error processing credits. Please try again.');
            }
        }
        
        return view('donors.view', compact('donor'));
    }
}
