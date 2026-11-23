<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplianceAndClaimListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_appliance_index_returns_an_empty_single_page_collection_when_no_appliances_exist(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/appliances')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('appliances/index')
                ->where('appliances.data', [])
                ->where('appliances.last_page', 1));
    }

    public function test_appliance_index_includes_the_fields_used_by_the_page(): void
    {
        $this->actingAs(User::factory()->create());
        $customer = Customer::create(['name' => 'Jamie Rivera', 'phone' => '555-0100']);
        $appliance = $customer->appliances()->create([
            'appliance_type' => 'Refrigerator',
            'model_number' => 'MODEL-100',
            'serial_number' => 'SERIAL-100',
            'purchase_date' => '2025-01-15',
        ]);
        $appliance->warrantyPolicy()->create(['warranty_expiry' => '2027-01-15']);

        $this->get('/appliances')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('appliances/index')
                ->where('appliances.data.0.appliance_type', 'Refrigerator')
                ->where('appliances.data.0.customer.name', 'Jamie Rivera')
                ->where('appliances.data.0.customer.phone', '555-0100')
                ->where('appliances.data.0.warranty_policy.warranty_expiry', fn ($value) => str_starts_with($value, '2027-01-15')));
    }

    public function test_claim_index_prompts_to_register_an_appliance_when_none_exist(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/warranty-claims')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('warranty-claims/index')
                ->where('hasAppliances', false)
                ->where('claims.data', [])
                ->where('claims.last_page', 1));
    }

    public function test_claim_index_includes_the_related_fields_used_by_the_page(): void
    {
        $this->actingAs(User::factory()->create());
        $customer = Customer::create(['name' => 'Jamie Rivera', 'phone' => '555-0101']);
        $appliance = $customer->appliances()->create([
            'appliance_type' => 'Refrigerator',
            'model_number' => 'MODEL-200',
            'serial_number' => 'SERIAL-200',
            'purchase_date' => '2025-01-15',
        ]);
        $policy = $appliance->warrantyPolicy()->create(['warranty_expiry' => '2027-01-15']);
        $claim = $policy->claims()->create([
            'claim_date' => '2026-01-15',
            'defect_description' => 'Cooling issue',
            'claim_status' => 'approved',
        ]);
        $claim->payout()->create(['approved_amount' => '125.00']);

        $this->get('/warranty-claims')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('warranty-claims/index')
                ->where('claims.data.0.warranty_policy.appliance.customer.name', 'Jamie Rivera')
                ->where('claims.data.0.warranty_policy.appliance.appliance_type', 'Refrigerator')
                ->where('claims.data.0.warranty_policy.appliance.serial_number', 'SERIAL-200')
                ->where('claims.data.0.payout.approved_amount', '125.00'));
    }

    public function test_claim_creation_only_receives_appliance_fields_used_in_the_form(): void
    {
        $this->actingAs(User::factory()->create());
        $customer = Customer::create(['name' => 'Jamie Rivera', 'phone' => '555-0102']);
        $customer->appliances()->create([
            'appliance_type' => 'Washer',
            'model_number' => 'MODEL-300',
            'serial_number' => 'SERIAL-300',
            'purchase_date' => '2025-01-15',
        ]);

        $this->get('/warranty-claims/create')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('warranty-claims/create')
                ->where('appliances.0.customer.name', 'Jamie Rivera')
                ->where('appliances.0.appliance_type', 'Washer')
                ->where('appliances.0.serial_number', 'SERIAL-300'));
    }
}
