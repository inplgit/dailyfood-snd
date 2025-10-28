<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\UOM;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class UpdateUOMRequest extends FormRequest
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
            'uom_name' => [
                'required',
                Rule::unique('uom')->where('status', 1)->whereNot('id',$this->uom->id),
             //   'unique:categories,name,'.$this->category->id,
            ],
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
