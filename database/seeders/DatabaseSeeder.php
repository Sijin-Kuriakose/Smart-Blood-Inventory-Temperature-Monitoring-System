<?php

namespace Database\Seeders;

use App\Models\BloodBag;
use App\Models\BloodBank;
use App\Models\Refrigerator;
use App\Models\TemperatureLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        try {
            // Create admin user
            $admin = User::create([
                'name' => 'Admin User',
                'email' => 'admin@test.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]);

            // Create blood bank staff user
            $staff = User::create([
                'name' => 'Staff User',
                'email' => 'staff@test.com',
                'password' => Hash::make('password'),
                'role' => 'blood_bank_staff',
            ]);

            // Create monitoring user
            $monitor = User::create([
                'name' => 'Monitor User',
                'email' => 'monitor@test.com',
                'password' => Hash::make('password'),
                'role' => 'monitoring_user',
            ]);

            // Create blood bank
            $bloodBank = BloodBank::create([
                'name' => 'Central Blood Bank',
                'location' => 'Downtown',
                'contact_number' => '1234567890',
            ]);

            // Assign users to blood bank
            $bloodBank->users()->attach([$admin->id, $staff->id, $monitor->id]);

            // Create refrigerators
            $fridge1 = Refrigerator::create([
                'blood_bank_id' => $bloodBank->id,
                'refrigerator_code' => 'REF-001',
                'location' => 'Storage Room A',
            ]);

            $fridge2 = Refrigerator::create([
                'blood_bank_id' => $bloodBank->id,
                'refrigerator_code' => 'REF-002',
                'location' => 'Storage Room B',
            ]);

            // Create sample blood bags
            BloodBag::create([
                'refrigerator_id' => $fridge1->id,
                'bag_number' => 'BAG-001',
                'blood_group' => 'A+',
                'donor_name' => 'John Doe',
                'collection_date' => now()->subDays(5),
                'expiry_date' => now()->addDays(35),
                'quantity' => 450,
                'status' => 'available',
            ]);

            BloodBag::create([
                'refrigerator_id' => $fridge1->id,
                'bag_number' => 'BAG-002',
                'blood_group' => 'O-',
                'donor_name' => 'Jane Smith',
                'collection_date' => now()->subDays(10),
                'expiry_date' => now()->addHours(20),
                'quantity' => 350,
                'status' => 'available',
            ]);

            BloodBag::create([
                'refrigerator_id' => $fridge2->id,
                'bag_number' => 'BAG-003',
                'blood_group' => 'B+',
                'donor_name' => 'Mike Johnson',
                'collection_date' => now()->subDays(40),
                'expiry_date' => now()->subDays(1),
                'quantity' => 400,
                'status' => 'available',
            ]);

            // Create sample temperature logs for fridge1
            foreach (range(1, 15) as $i) {
                TemperatureLog::withoutEvents(function () use ($fridge1, $i) {
                    TemperatureLog::create([
                        'refrigerator_id' => $fridge1->id,
                        'temperature' => rand(20, 60) / 10, // 2.0 to 6.0°C (safe range)
                        'recorded_at' => now()->subMinutes(15 - $i),
                    ]);
                });
            }

            $this->command->info('Database seeded successfully!');
        } catch (\Exception $e) {
            Log::error('Database seeding failed: ' . $e->getMessage());

            $this->command->error('Seeding failed: ' . $e->getMessage());
        }
    }
}
