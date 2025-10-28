<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreDistributorRequest extends FormRequest
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
            'distributor_code'      => 'required',
            'custom_code'           => 'required',
            'distributor_name'      => 'required',
            // 'contact_person'        => 'required',
            // 'email'                 => 'required',
            // 'phone'                 => 'required',
            // 'alt_phone'             => 'required',
            // 'address'               => 'required',
            // 'city'                  => 'required',
            // 'zip'                   => 'required',
             'zone_id'               => 'required',
            // 'note'                  => 'required',
            // 'pricing_type_id'       => 'required',
            // 'min_discount'          => 'required',
            // 'max_discount'          => 'required',
            // 'location_title'        => 'required',
            // 'location_latitude'     => 'required',
            // 'location_longitude'    => 'required',
            // 'location_radius'       => 'required',
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
