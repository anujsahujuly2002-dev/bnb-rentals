<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class AddGalleryImageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'property_id'=>'required|numeric',
            'image'=>'required|mimes:png,jpg,jpeg,webp|image',
        ];
    }

    /**
        * Get the error messages for the defined validation rules.*
        * @return array
    */
    protected function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            'status'=>false,
            'msg' => $validator->errors()->first(),
        ], 422));
    }
}
