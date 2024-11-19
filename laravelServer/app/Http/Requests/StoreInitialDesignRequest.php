<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class StoreInitialDesignRequest extends FormRequest
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
            'main_color' => 'required|string',
            'second_color' => 'required|string',
            'logo' => 'required|image|mimes:jpg,jpeg,png,svg|max:500',
            'files.*' => 'required|file|mimetypes:text/plain,application/pdf,image/jpeg,image/png,image/gif|max:2048',
            'description' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'logo.mimes' => 'فرمت فایل انتخابی باید jpeg, png,jpg باشد.',
            'logo.max' => 'حجم فایل انتخابی باید کمتر از 500kb باشد.',
            'logo.image' => 'لطفا یک عکس انتخاب کنید.'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $validators = (new ValidationException($validator))->errors();
        $errors = [];
        foreach ($validators as $key => $value) {
            foreach ($value as $message) {
                array_push($errors, $message);
            }
        }
        throw new HttpResponseException(
            response()->json(['errors' => $errors], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
