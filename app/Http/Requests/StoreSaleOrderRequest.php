<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreSaleOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'distributor_id' => 'required',
            'tso_id' => 'required',
            'shop_id' => 'required',
            'invoice_no' => 'required',
            // 'invoice_no' => ['required', Rule::unique('sale_orders')->where('status', 1)],
            // 'dc_no' => 'required',
            // 'lpo_no' => 'required',
            'dc_date' => 'required',
            'payment_type' => 'required',
            'total_carton' => 'required',
            'total_pcs' => 'required',
            'cost_center' => 'required',
            // 'notes' => 'required',
            // 'transport_details' => 'required',
            'discount_percent' => 'required',
            'discount_amount' => 'required',
            'total_amount' => 'required',
            'products_subtotal' => 'required',
            'total_pcs' => 'required|min:0',
            // 'qty' => 'required|array',
            'qty.*' => 'required|integer|min:1',
            'data_total.*' => 'required'
        ];
    }
    public function failedValidation(Validator $validator)

    {
        throw new HttpResponseException(response()->json([
            'success' => 'error',
            'message' => 'error',
            'error' => $validator->errors()
        ]));
    }
}
