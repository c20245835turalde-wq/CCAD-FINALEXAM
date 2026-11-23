<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreWarrantyClaimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'appliance_id' => ['required', 'integer', 'exists:appliances,id'],
            'claim_date' => ['required', 'date'],
            'defect_description' => ['required', 'string', 'max:5000'],
            'claim_status' => ['required', 'in:pending,approved,rejected'],
            'approved_amount' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
        ];
    }

    public function messages(): array
    {
        return [
            'appliance_id.exists' => 'Select a registered appliance.',
            'claim_status.in' => 'The claim status must be pending, approved, or rejected.',
            'approved_amount.decimal' => 'The approved amount may have no more than two decimal places.',
        ];
    }
}
