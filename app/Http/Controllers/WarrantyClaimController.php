<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWarrantyClaimRequest;
use App\Http\Requests\UpdateWarrantyClaimRequest;
use App\Services\WarrantyService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class WarrantyClaimController extends Controller
{
    public function index(WarrantyService $service): Response
    {
        return Inertia::render('warranty-claims/index', [
            'hasAppliances' => $service->hasAppliances(),
            'claims' => $service->claimListings(),
        ]);
    }

    public function create(WarrantyService $service): Response
    {
        return Inertia::render('warranty-claims/create', [
            'appliances' => $service->appliancesForClaimForm(),
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
