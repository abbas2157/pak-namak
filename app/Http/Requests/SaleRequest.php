<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleRequest extends FormRequest
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
            'shop_id'            => 'required|exists:shops,id',
            'salt_type_id'       => 'required|exists:salt_types,id',
            'product_size'       => 'required|string|max:255',
            'quantity_sold'      => 'required|integer|min:1',
            'rate_per_pack'      => 'required|numeric|min:0',
            'total_sales_amount' => 'required|numeric|min:0',
            'date'               => 'required|date',
            'remarks'            => 'nullable|string',
        ];
    }
}
