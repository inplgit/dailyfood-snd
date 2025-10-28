<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserRequest extends FormRequest
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
            'name'      => 'required|string',
            'email'     => 'required|unique:users,email,'.$this->user->id,
            // 'password'  => 'required',
            'user_type' => 'required',
        ];
        // return [
        //     'uom_name' => [
        //         'required',
        //         Rule::unique('uom')->where('status', 1)->whereNot('id',$this->uom->id),
        //      //   'unique:categories,name,'.$this->category->id,
        //     ],
        // ];
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
