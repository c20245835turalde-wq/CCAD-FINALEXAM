<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreApplianceRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $phone = trim(strip_tags((string) $this->input('customer_phone', '')));
        $serialNumber = mb_strtoupper(trim(strip_tags((string) $this->input('serial_number', ''))));
        $digits = preg_replace('/\D+/', '', $phone);

        $this->merge([
            'customer_phone' => (str_starts_with($phone, '+') ? '+' : '').$digits,
            'serial_number' => $serialNumber,
        ]);
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
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30', 'regex:/^\+?[0-9\s().-]+$/'],
            'appliance_type' => ['required', 'string', 'max:100'],
            'model_number' => ['required', 'string', 'max:100'],
            'serial_number' => ['required', 'string', 'max:100', Rule::unique('appliances', 'serial_number')],
            'purchase_date' => ['required', 'date'],
            'warranty_expiry' => ['required', 'date', 'after_or_equal:purchase_date'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_phone.regex' => 'Enter a valid phone number.',
            'serial_number.unique' => 'This serial number is already registered.',
            'warranty_expiry.after_or_equal' => 'The warranty must expire on or after the purchase date.',
        ];
    }
}
