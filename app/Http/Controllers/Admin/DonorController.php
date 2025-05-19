
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donor;
use Illuminate\Http\Request;

class DonorController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $query = Donor::query();
        
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('blood_group', 'LIKE', "%{$search}%")
                    ->orWhere('location', 'LIKE', "%{$search}%");
            });
        }
        
        $donors = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.donors.index', compact('donors', 'search'));
    }
    
    public function create()
    {
        return view('admin.donors.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:donors',
            'phone' => 'required|string|max:20',
            'blood_group' => 'required|string',
            'location' => 'required|string|max:255',
        ]);
        
        Donor::create($request->all());
        
        return redirect()->route('admin.donors.index')
            ->with('success', 'Donor created successfully');
    }
    
    public function edit(Donor $donor)
    {
        return view('admin.donors.edit', compact('donor'));
    }
    
    public function update(Request $request, Donor $donor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:donors,email,' . $donor->id,
            'phone' => 'required|string|max:20',
            'blood_group' => 'required|string',
            'location' => 'required|string|max:255',
        ]);
        
        $donor->update($request->all());
        
        return redirect()->route('admin.donors.index')
            ->with('success', 'Donor updated successfully');
    }
    
    public function updateStatus(Request $request, Donor $donor)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,pending',
        ]);
        
        $donor->status = $request->status;
        $donor->save();
        
        return redirect()->route('admin.donors.index')
            ->with('success', 'Donor status updated successfully');
    }
    
    public function destroy(Donor $donor)
    {
        $donor->delete();
        
        return redirect()->route('admin.donors.index')
            ->with('success', 'Donor deleted successfully');
    }
    
    public function export()
    {
        $donors = Donor::all();
        $filename = 'donors-' . date('Y-m-d') . '.csv';
        
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        
        $columns = ['ID', 'Name', 'Email', 'Phone', 'Blood Group', 'Location', 'Age', 'Gender', 'Status', 'Created At'];
        
        $callback = function() use ($donors, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            foreach ($donors as $donor) {
                fputcsv($file, [
                    $donor->id,
                    $donor->name,
                    $donor->email,
                    $donor->phone,
                    $donor->blood_group,
                    $donor->location,
                    $donor->age,
                    $donor->gender,
                    $donor->status,
                    $donor->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
