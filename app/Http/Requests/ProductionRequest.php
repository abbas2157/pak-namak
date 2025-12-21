<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
         return [
            'production_date' => 'required|date',
            'raw_salt_used' => 'required|numeric',
            'finished_salt' => 'required|numeric',
            'wastage' => 'required|numeric',
            'machine_used' => 'required|string',
            'electricity_fuel_cost' => 'required|numeric',
            'remarks' => 'nullable|string',
        ];
    }
}
