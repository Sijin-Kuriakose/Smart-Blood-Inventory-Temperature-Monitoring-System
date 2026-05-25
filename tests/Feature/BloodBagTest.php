<?php

namespace Tests\Feature;

use App\Models\BloodBag;
use App\Models\BloodBank;
use App\Models\Refrigerator;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BloodBagTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $staffUser;
    protected $monitorUser;
    protected $refrigerator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create(['role' => 'admin']);
        $this->staffUser = User::factory()->create(['role' => 'blood_bank_staff']);
        $this->monitorUser = User::factory()->create(['role' => 'monitoring_user']);

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
     * Test list blood bags for authenticated user.
     */
    public function test_authenticated_user_can_list_blood_bags()
    {
        for ($i = 0; $i < 3; $i++) {
            BloodBag::create([
                'refrigerator_id' => $this->refrigerator->id,
                'bag_number' => 'BAG' . time() . $i,
                'donor_name' => 'John Doe ' . $i,
                'blood_group' => 'A+',
                'quantity' => 450,
                'collection_date' => now()->toDateString(),
                'expiry_date' => now()->addDays(35)->toDateString(),
                'status' => 'available',
            ]);
        }

        $response = $this->actingAs($this->monitorUser, 'sanctum')->getJson('/api/blood-bags');

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonCount(3, 'data');
    }

    /**
     * Test admin can create blood bag.
     */
    public function test_admin_can_create_blood_bag()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')->postJson('/api/blood-bags', [
            'refrigerator_id' => $this->refrigerator->id,
            'bag_number' => 'NEWBAG123',
            'donor_name' => 'Alice Smith',
            'blood_group' => 'A+',
            'quantity' => 450,
            'collection_date' => now()->toDateString(),
            'expiry_date' => now()->addDays(35)->toDateString(),
            'status' => 'available',
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('data.blood_group', 'A+');

        $this->assertDatabaseHas('blood_bags', [
            'blood_group' => 'A+',
            'bag_number' => 'NEWBAG123',
            'quantity' => 450,
        ]);
    }

    /**
     * Test monitor cannot create blood bag.
     */
    public function test_monitor_cannot_create_blood_bag()
    {
        $response = $this->actingAs($this->monitorUser, 'sanctum')->postJson('/api/blood-bags', [
            'refrigerator_id' => $this->refrigerator->id,
            'bag_number' => 'NEWBAG999',
            'donor_name' => 'Monitor User',
            'blood_group' => 'O-',
            'quantity' => 450,
            'collection_date' => now()->toDateString(),
            'expiry_date' => now()->addDays(35)->toDateString(),
            'status' => 'available',
        ]);

        $response->assertStatus(403);
    }
}
