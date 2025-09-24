<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Asset;
use App\Models\User;

class AssetStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_ajax_store_creates_asset_and_returns_json()
    {
        $payload = [
            'tipe' => 'Elektronik',
            'jenis_aset' => 'Laptop',
            'pic' => 'Available',
            'merk' => 'TestBrand',
            'serial_number' => 'SN-TEST-001',
            'project' => 'Head Office',
            'lokasi' => 'Jakarta',
            'tanggal_beli' => '2025-08-26',
            'harga_beli' => 1000000,
        ];

    // authenticate as a user because routes are behind auth middleware
    $user = User::factory()->create(['role' => 'admin']);
    $response = $this->actingAs($user)
             ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
             ->post(route('assets.store'), $payload);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('assets', [
            'merk' => 'TestBrand',
            'serial_number' => 'SN-TEST-001'
        ]);

        $json = $response->json();
        $this->assertArrayHasKey('asset', $json);
        $this->assertArrayHasKey('id', $json['asset']);
    }

    public function test_global_serial_increments_across_types()
    {
        // create an admin user to call controller store route (controller assigns serial_number)
        $user = User::factory()->create(['role' => 'admin']);

        // Create first asset (Mobil) via POST without serial -> controller should assign 001/MBL
        $payload1 = [
            'tipe' => 'Kendaraan',
            'jenis_aset' => 'Mobil',
            'pic' => 'Available',
            'merk' => 'CarBrand',
        ];
        $resp1 = $this->actingAs($user)->post(route('assets.store'), $payload1);
        $resp1->assertStatus(302); // redirect to dashboard on success

        $a1 = Asset::latest('id')->first();
        $this->assertNotEmpty($a1->serial_number);
        $this->assertStringEndsWith('/MBL', $a1->serial_number);

        // Create second asset (Motor) via POST -> should get next id-based prefix
        $payload2 = [
            'tipe' => 'Kendaraan',
            'jenis_aset' => 'Motor',
            'pic' => 'Available',
            'merk' => 'MotorBrand',
        ];
        $resp2 = $this->actingAs($user)->post(route('assets.store'), $payload2);
        $resp2->assertStatus(302);

        $a2 = Asset::latest('id')->first();
        $this->assertNotEmpty($a2->serial_number);
        $this->assertStringEndsWith('/MTR', $a2->serial_number);

        // Ensure prefix increments (based on DB id)
        $id1 = $a1->id;
        $id2 = $a2->id;
        $pref1 = intval(substr($a1->serial_number, 0, 3));
        $pref2 = intval(substr($a2->serial_number, 0, 3));
        $this->assertEquals($id1, $pref1);
        $this->assertEquals($id2, $pref2);
    }
}
