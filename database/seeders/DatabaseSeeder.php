
<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Donor;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@jeevanseva.com',
            'password' => Hash::make('admin123'),
            'is_admin' => true,
            'credits' => 100,
        ]);
        
        // Create some regular users
        User::create([
            'name' => 'John Doe',
            'username' => 'john',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'credits' => 20,
        ]);
        
        User::create([
            'name' => 'Jane Smith',
            'username' => 'jane',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
            'credits' => 15,
        ]);
        
        // Create some donors
        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $locations = ['Mumbai', 'Delhi', 'Bangalore', 'Chennai', 'Kolkata'];
        
        for ($i = 1; $i <= 20; $i++) {
            Donor::create([
                'name' => "Donor $i",
                'email' => "donor{$i}@example.com",
                'phone' => "999000{$i}",
                'blood_group' => $bloodGroups[array_rand($bloodGroups)],
                'location' => $locations[array_rand($locations)],
                'age' => rand(18, 55),
                'gender' => rand(0, 1) ? 'male' : 'female',
                'weight' => rand(45, 95),
                'status' => 'active',
            ]);
        }
    }
}
