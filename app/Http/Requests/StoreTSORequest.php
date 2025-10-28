<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTSORequest extends FormRequest
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
            // 'tso_code'      => 'required',
            'name'           => 'required',
            'email'           => 'required|unique:users,email',
            'emp_id'      => 'required',
            'phone'        => 'required',
            'city'                 => 'required',
            'state'                 => 'required',
          //  'country'             => 'required',
         //   'distributor_id'               => 'required',
          //  'manager'                  => 'required',
          //  'kpo'                   => 'required',
         //   'kpo_2'               => 'required',
         //   'kpo_3'                  => 'required',
         //   'department_id'       => 'required',
         //   'designation_id'          => 'required',
         //   'spot_sale'          => 'required',
         //   'auto_payment'        => 'required'
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
