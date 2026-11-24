<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreApplianceRequest;
use App\Http\Requests\UpdateApplianceRequest;
use App\Services\WarrantyService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ApplianceController extends Controller
{
    public function index(WarrantyService $service): Response
    {
        return Inertia::render('appliances/index', [
            'appliances' => $service->applianceListings(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('appliances/create');
    }

    public function store(StoreApplianceRequest $request, WarrantyService $service): RedirectResponse
    {
        $service->createAppliance($request->validated());

        return to_route('appliances.index')->with('success', 'Appliance registered successfully.');
    }

    public function update(UpdateApplianceRequest $request, int $appliance, WarrantyService $service): RedirectResponse
    {
        $service->updateAppliance($appliance, $request->validated());

        return to_route('appliances.index')->with('success', 'Appliance updated successfully.');
    }
}
