<?php

namespace App\Http\Requests;

use App\Models\Appliance;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateApplianceRequest extends FormRequest
{
    use Concerns\SanitizesInput;

    protected function prepareForValidation(): void
    {
        $this->merge($this->sanitizedStrings([
            'customer_name',
            'customer_phone',
            'appliance_type',
            'model_number',
            'serial_number',
            'purchase_date',
            'warranty_expiry',
        ]));

        if (is_string($this->input('serial_number'))) {
            $this->merge(['serial_number' => mb_strtoupper($this->input('serial_number'))]);
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $appliance = $this->route('appliance');
        $applianceId = $appliance instanceof Appliance ? $appliance->id : $appliance;
        $customerId = $appliance instanceof Appliance ? $appliance->customer_id : Appliance::findOrFail($applianceId)->customer_id;

        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30', 'regex:/^(?=.*\d)\+?[0-9\s().-]+$/', Rule::unique('customers', 'phone')->ignore($customerId)],
            'appliance_type' => ['required', 'string', 'max:100'],
            'model_number' => ['required', 'string', 'max:100'],
            'serial_number' => ['required', 'string', 'max:100', Rule::unique('appliances', 'serial_number')->ignore($applianceId)],
            'purchase_date' => ['required', 'date'],
            'warranty_expiry' => ['required', 'date', 'after_or_equal:purchase_date'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_phone.regex' => 'Enter a valid phone number.',
            'customer_phone.unique' => 'A customer with this phone number already exists.',
            'serial_number.unique' => 'This serial number is already registered.',
            'warranty_expiry.after_or_equal' => 'The warranty must expire on or after the purchase date.',
        ];
    }
}
