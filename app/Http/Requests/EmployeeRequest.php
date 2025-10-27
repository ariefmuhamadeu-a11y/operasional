<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('employee')?->id;

        return [
            'code' => ['required', 'string', 'max:16', Rule::unique('employees', 'code')->ignore($id)],
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::in(['operasional', 'cutting', 'jahit'])],
            'payment_type' => ['required', Rule::in(['harian', 'per_pcs'])],
            'base_rate' => ['nullable', 'numeric', 'min:0'],
            'active' => ['nullable', 'boolean'],
        ];
    }

    public function prepareForValidation(): void
    {
        // Normalisasi input angka & boolean
        $this->merge([
            'base_rate' => $this->base_rate !== null && $this->base_rate !== ''
            ? (float) $this->base_rate
            : null,
            'active' => $this->has('active') ? (bool) $this->active : false,
        ]);
    }
}
