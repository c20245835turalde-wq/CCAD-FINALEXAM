<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WarrantyClaim;
use App\Services\WarrantyService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WarrantyBusinessRulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_same_customer_can_register_multiple_appliances_and_model_numbers_are_uppercased(): void
    {
        $this->actingAs(User::factory()->create());

        $applianceData = [
            'customer_name' => 'Jamie Rivera',
            'customer_phone' => '+1 (555) 010-1000',
            'appliance_type' => 'Refrigerator',
            'model_number' => 'model-rf1',
            'serial_number' => 'sn-100',
            'purchase_date' => today()->subYear()->toDateString(),
            'warranty_expiry' => today()->addYear()->toDateString(),
        ];

        $this->post('/appliances', $applianceData)->assertRedirect('/appliances');
        $applianceData['appliance_type'] = 'Washer';
        $applianceData['model_number'] = 'model-ws1';
        $applianceData['serial_number'] = 'sn-101';
        $this->post('/appliances', $applianceData)->assertRedirect('/appliances');

        $this->assertDatabaseCount('customers', 1);
        $this->assertDatabaseCount('appliances', 2);
        $this->assertDatabaseHas('appliances', [
            'model_number' => 'MODEL-RF1',
            'serial_number' => 'SN-100',
        ]);
        $this->assertDatabaseHas('appliances', [
            'model_number' => 'MODEL-WS1',
            'serial_number' => 'SN-101',
        ]);
    }

    public function test_appliance_update_works_with_selected_relationship_columns(): void
    {
        $service = app(WarrantyService::class);
        $appliance = $service->createAppliance([
            'customer_name' => 'Jamie Rivera',
            'customer_phone' => '555-0100',
            'appliance_type' => 'Refrigerator',
            'model_number' => 'MODEL-RF1',
            'serial_number' => 'SN-100',
            'purchase_date' => today()->subYear()->toDateString(),
            'warranty_expiry' => today()->addYear()->toDateString(),
        ]);

        $updated = $service->updateAppliance($appliance->id, [
            'customer_name' => 'Jamie Rivera Updated',
            'customer_phone' => '555-0101',
            'appliance_type' => 'Refrigerator',
            'model_number' => 'model-rf2',
            'serial_number' => 'sn-100-updated',
            'purchase_date' => today()->subMonths(6)->toDateString(),
            'warranty_expiry' => today()->addMonths(18)->toDateString(),
        ]);

        $this->assertSame('MODEL-RF2', $updated->model_number);
        $this->assertSame('SN-100-UPDATED', $updated->serial_number);
        $this->assertSame('Jamie Rivera Updated', $updated->customer->name);
        $this->assertSame('5550101', $updated->customer->phone);
        $this->assertSame(today()->addMonths(18)->toDateString(), $updated->warrantyPolicy->warranty_expiry->toDateString());
    }

    public function test_claims_outside_the_policy_dates_are_rejected_and_expiry_date_is_included(): void
    {
        $service = app(WarrantyService::class);
        $data = [
            'customer_name' => 'Alex Morgan',
            'customer_phone' => '555-0101',
            'appliance_type' => 'Dishwasher',
            'model_number' => 'dw-200',
            'serial_number' => 'dw-200-serial',
            'purchase_date' => today()->subDays(10)->toDateString(),
            'warranty_expiry' => today()->toDateString(),
        ];
        $appliance = $service->createAppliance($data);

        $onExpiryDate = $service->createClaim([
            'appliance_id' => $appliance->id,
            'claim_date' => today()->toDateString(),
            'defect_description' => 'Claim filed on the final covered day',
            'claim_status' => 'pending',
        ]);
        $afterExpiry = $service->createClaim([
            'appliance_id' => $appliance->id,
            'claim_date' => today()->addDay()->toDateString(),
            'defect_description' => 'Claim filed after coverage ended',
            'claim_status' => 'approved',
            'approved_amount' => '125.00',
        ]);

        $this->assertSame('pending', $onExpiryDate->claim_status);
        $this->assertSame('rejected', $afterExpiry->claim_status);
        $this->assertNull($afterExpiry->payout);
    }

    public function test_claims_before_the_purchase_date_are_rejected(): void
    {
        $service = app(WarrantyService::class);
        $appliance = $service->createAppliance([
            'customer_name' => 'Taylor Lee',
            'customer_phone' => '555-0102',
            'appliance_type' => 'Microwave',
            'model_number' => 'mw-300',
            'serial_number' => 'mw-300-serial',
            'purchase_date' => today()->toDateString(),
            'warranty_expiry' => today()->addYear()->toDateString(),
        ]);

        $claim = $service->createClaim([
            'appliance_id' => $appliance->id,
            'claim_date' => today()->subDay()->toDateString(),
            'defect_description' => 'Claim predates the purchase',
            'claim_status' => 'pending',
        ]);

        $this->assertSame('rejected', $claim->claim_status);
    }

    public function test_database_rejects_claim_statuses_outside_the_supported_values(): void
    {
        $appliance = app(WarrantyService::class)->createAppliance([
            'customer_name' => 'Morgan Taylor',
            'customer_phone' => '555-0103',
            'appliance_type' => 'Oven',
            'model_number' => 'OV-400',
            'serial_number' => 'OV-400-SERIAL',
            'purchase_date' => today()->toDateString(),
            'warranty_expiry' => today()->addYear()->toDateString(),
        ]);

        $this->expectException(QueryException::class);

        WarrantyClaim::create([
            'warranty_policy_id' => $appliance->warrantyPolicy->id,
            'claim_date' => today()->toDateString(),
            'defect_description' => 'Invalid status should not persist',
            'claim_status' => 'in_review',
        ]);
    }
}
