<?php

namespace Tests\Feature;

use App\Models\BloodBag;
use App\Models\BloodBank;
use App\Models\Refrigerator;
use App\Models\TemperatureLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $refrigerator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'admin']);
        
        $bloodBank = BloodBank::create([
            'name' => 'Test Blood Bank',
            'location' => 'Test Location',
            'contact_number' => '1234567890',
        ]);
        
        $this->refrigerator = Refrigerator::create([
            'blood_bank_id' => $bloodBank->id,
            'refrigerator_code' => 'R01',
            'location' => 'Room 1',
            'is_active' => true,
        ]);
    }

    /**
     * Test dashboard endpoint.
     */
    public function test_can_fetch_dashboard_data()
    {
        for ($i = 0; $i < 2; $i++) {
            BloodBag::create([
                'refrigerator_id' => $this->refrigerator->id,
                'bag_number' => 'DASH' . time() . $i,
                'donor_name' => 'Dash User',
                'blood_group' => 'A+',
                'quantity' => 450,
                'collection_date' => now()->toDateString(),
                'expiry_date' => now()->addDays(35)->toDateString(),
                'status' => 'available',
            ]);
        }

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/dashboard');

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'total_blood_bags',
                         'available_stock_by_blood_group',
                         'total_expired_bags',
                         'active_refrigerators',
                         'average_temperature_today',
                         'critical_alerts_today',
                         'expiring_within_24h',
                         'near_risk_percentage',
                     ]
                 ]);
    }

    /**
     * Test refrigerator analysis endpoint.
     */
    public function test_can_fetch_refrigerator_analysis()
    {
        for ($i = 0; $i < 5; $i++) {
            TemperatureLog::create([
                'refrigerator_id' => $this->refrigerator->id,
                'recorded_at' => now(),
                'temperature' => 4.0,
            ]);
        }

        $response = $this->actingAs($this->user, 'sanctum')
                         ->getJson("/api/refrigerators/{$this->refrigerator->id}/analysis");

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.average_temperature', 4);
    }
}
