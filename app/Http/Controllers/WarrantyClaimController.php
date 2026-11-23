<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWarrantyClaimRequest;
use App\Http\Requests\UpdateWarrantyClaimRequest;
use App\Models\Appliance;
use App\Models\WarrantyClaim;
use App\Services\WarrantyService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class WarrantyClaimController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('warranty-claims/index', [
            'hasAppliances' => Appliance::exists(),
            'claims' => WarrantyClaim::query()
                ->select(['id', 'warranty_policy_id', 'claim_date', 'defect_description', 'claim_status'])
                ->with([
                    'warrantyPolicy:id,appliance_id',
                    'warrantyPolicy.appliance:id,customer_id,appliance_type,serial_number',
                    'warrantyPolicy.appliance.customer:id,name',
                    'payout:id,warranty_claim_id,approved_amount',
                ])
                ->orderByDesc('claim_date')
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('warranty-claims/create', [
            'appliances' => Appliance::query()
                ->select(['id', 'customer_id', 'appliance_type', 'serial_number'])
                ->with('customer:id,name')
                ->orderBy('serial_number')
                ->get(),
        ]);
    }

    public function store(StoreWarrantyClaimRequest $request, WarrantyService $service): RedirectResponse
    {
        $service->createClaim($request->validated());

        return to_route('warranty-claims.index')->with('success', 'Warranty claim recorded successfully.');
    }

    public function update(UpdateWarrantyClaimRequest $request, int $warrantyClaim, WarrantyService $service): RedirectResponse
    {
        $service->updateClaim($warrantyClaim, $request->validated());

        return to_route('warranty-claims.index')->with('success', 'Warranty claim updated successfully.');
    }
}
