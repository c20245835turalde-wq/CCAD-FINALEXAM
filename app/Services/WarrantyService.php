<?php

namespace App\Services;

use App\Models\Appliance;
use App\Models\Customer;
use App\Models\WarrantyClaim;
use App\Models\WarrantyPolicy;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WarrantyService
{
    public function createAppliance(array $data): Appliance
    {
        return DB::transaction(function () use ($data) {
            $phone = $this->formatPhone($data['customer_phone']);
            $customer = Customer::firstOrCreate(
                ['phone' => $phone],
                ['name' => $this->cleanText($data['customer_name'])],
            );

            $appliance = $customer->appliances()->create([
                'appliance_type' => $this->cleanText($data['appliance_type']),
                'model_number' => $this->uppercaseText($data['model_number']),
                'serial_number' => $this->uppercaseText($data['serial_number']),
                'purchase_date' => $data['purchase_date'],
            ]);

            $appliance->warrantyPolicy()->create([
                'warranty_expiry' => $data['warranty_expiry'],
            ]);

            return $appliance->load(['customer', 'warrantyPolicy']);
        });
    }

    public function updateAppliance(int $id, array $data): Appliance
    {
        return DB::transaction(function () use ($id, $data) {
            $appliance = Appliance::query()
                ->select(['id', 'customer_id'])
                ->with([
                    'customer:id',
                    'warrantyPolicy:id,appliance_id',
                ])
                ->findOrFail($id);

            $appliance->customer->update([
                'name' => $this->cleanText($data['customer_name']),
                'phone' => $this->formatPhone($data['customer_phone']),
            ]);

            $appliance->update([
                'appliance_type' => $this->cleanText($data['appliance_type']),
                'model_number' => $this->uppercaseText($data['model_number']),
                'serial_number' => $this->uppercaseText($data['serial_number']),
                'purchase_date' => $data['purchase_date'],
            ]);

            $appliance->warrantyPolicy->update([
                'warranty_expiry' => $data['warranty_expiry'],
            ]);

            return $appliance->refresh()->load(['customer', 'warrantyPolicy']);
        });
    }

    public function createClaim(array $data): WarrantyClaim
    {
        return DB::transaction(function () use ($data) {
            $policy = WarrantyPolicy::where('appliance_id', $data['appliance_id'])->firstOrFail();

            return $this->saveClaim($policy, null, $data);
        });
    }

    public function updateClaim(int $id, array $data): WarrantyClaim
    {
        return DB::transaction(function () use ($id, $data) {
            $claim = WarrantyClaim::findOrFail($id);
            $policy = WarrantyPolicy::where('appliance_id', $data['appliance_id'])->firstOrFail();

            return $this->saveClaim($policy, $claim, $data);
        });
    }

    private function saveClaim(WarrantyPolicy $policy, ?WarrantyClaim $claim, array $data): WarrantyClaim
    {
        $claimDate = CarbonImmutable::parse($data['claim_date'])->startOfDay();
        $purchaseDate = CarbonImmutable::parse($policy->appliance->purchase_date)->startOfDay();
        $warrantyExpiry = CarbonImmutable::parse($policy->warranty_expiry)->startOfDay();
        $withinPolicy = $claimDate->betweenIncluded($purchaseDate, $warrantyExpiry);
        $status = $withinPolicy ? $data['claim_status'] : 'rejected';

        if ($status === 'approved' && ($data['approved_amount'] ?? null) === null) {
            throw ValidationException::withMessages([
                'approved_amount' => 'An approved claim requires an approved amount.',
            ]);
        }

        $attributes = [
            'warranty_policy_id' => $policy->id,
            'claim_date' => $claimDate->toDateString(),
            'defect_description' => $this->cleanText($data['defect_description']),
            'claim_status' => $status,
        ];

        if ($claim === null) {
            $claim = WarrantyClaim::create($attributes);
        } else {
            $claim->update($attributes);
        }

        if ($status === 'approved') {
            $claim->payout()->updateOrCreate([], [
                'approved_amount' => number_format((float) $data['approved_amount'], 2, '.', ''),
            ]);
        } else {
            $claim->payout()->delete();
        }

        return $claim->load(['warrantyPolicy.appliance.customer', 'payout']);
    }

    private function cleanText(string $value): string
    {
        return trim(strip_tags($value));
    }

    private function uppercaseText(string $value): string
    {
        return mb_strtoupper($this->cleanText($value));
    }

    private function formatPhone(string $value): string
    {
        $value = $this->cleanText($value);
        $hasInternationalPrefix = str_starts_with($value, '+');
        $digits = preg_replace('/\D+/', '', $value);

        return ($hasInternationalPrefix ? '+' : '').$digits;
    }
}
