
<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BloodRequestController extends Controller
{
    public function index()
    {
        // Get public blood requests or those belonging to the current user
        $user = Auth::user();
        
        $query = BloodRequest::where(function($q) use ($user) {
            $q->where('is_public', true);
            
            if ($user) {
                $q->orWhere('user_id', $user->id);
            }
        })
        ->orderBy('urgency', 'desc')
        ->orderBy('created_at', 'desc');
        
        $requests = $query->paginate(10);
        
        return view('blood-requests.index', compact('requests'));
    }
    
    public function showRequestForm()
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('info', 'Please log in to create a blood request')
                ->with('redirect', route('blood-requests.create'));
        }
        
        return view('blood-requests.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'blood_group' => 'required|string',
            'location' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'units_required' => 'required|integer|min:1|max:10',
        ]);
        
        $bloodRequest = BloodRequest::create([
            'user_id' => Auth::id(),
            'blood_group' => $request->blood_group,
            'location' => $request->location,
            'hospital_name' => $request->hospital_name,
            'patient_name' => $request->patient_name,
            'urgency' => $request->urgency ?: 'normal',
            'units_required' => $request->units_required,
            'contact_phone' => $request->contact_phone,
            'additional_info' => $request->additional_info,
            'is_public' => $request->has('is_public'),
            'status' => 'active',
        ]);
        
        return redirect()->route('blood-requests.show', $bloodRequest->id)
            ->with('success', 'Blood request created successfully');
    }
    
    public function show(BloodRequest $bloodRequest)
    {
        // Check if the user can view this request
        if (!$bloodRequest->is_public && Auth::id() !== $bloodRequest->user_id) {
            abort(403, 'Unauthorized access');
        }
        
        return view('blood-requests.show', compact('bloodRequest'));
    }
}
