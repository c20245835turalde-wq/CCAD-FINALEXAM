<?php

namespace Tests\Feature;

use App\Models\Appliance;
use App\Models\Customer;
use App\Models\User;
use App\Models\WarrantyClaim;
use App\Models\WarrantyPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page()
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_authenticated_users_can_visit_the_dashboard()
    {
        $this->actingAs($user = User::factory()->create());

        $this->get('/dashboard')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('dashboard')
                ->where('stats.appliances', 0)
                ->where('stats.activeWarranties', 0)
                ->where('stats.pendingClaims', 0)
                ->where('stats.approvedClaims', 0)
                ->has('recentClaims', 0));
    }

    public function test_dashboard_shows_appliance_warranty_and_claim_summaries()
    {
        $this->actingAs(User::factory()->create());
        $customer = Customer::create(['name' => 'Jamie Rivera', 'phone' => '555-0100']);
        $appliance = Appliance::create([
            'customer_id' => $customer->id,
            'appliance_type' => 'Refrigerator',
            'model_number' => 'RF-100',
            'serial_number' => 'SN-100',
            'purchase_date' => today()->subYear()->toDateString(),
        ]);
        $policy = WarrantyPolicy::create([
            'appliance_id' => $appliance->id,
            'warranty_expiry' => today()->addYear()->toDateString(),
        ]);
        WarrantyClaim::create([
            'warranty_policy_id' => $policy->id,
            'claim_date' => today()->toDateString(),
            'defect_description' => 'Cooling issue',
            'claim_status' => 'pending',
        ]);

        $this->get('/dashboard')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('stats.appliances', 1)
                ->where('stats.activeWarranties', 1)
                ->where('stats.pendingClaims', 1)
                ->where('stats.approvedClaims', 0)
                ->has('recentClaims', 1)
                ->where('recentClaims.0.warranty_policy.appliance.customer.name', 'Jamie Rivera'));
    }
}
