<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreApplianceRequest;
use App\Http\Requests\UpdateApplianceRequest;
use App\Models\Appliance;
use App\Services\WarrantyService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ApplianceController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('appliances/index', [
            'appliances' => Appliance::query()
                ->select(['id', 'customer_id', 'appliance_type', 'model_number', 'serial_number', 'purchase_date'])
                ->with([
                    'customer:id,name,phone',
                    'warrantyPolicy:id,appliance_id,warranty_expiry',
                ])
                ->orderByDesc('id')
                ->paginate(15)
                ->withQueryString(),
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
