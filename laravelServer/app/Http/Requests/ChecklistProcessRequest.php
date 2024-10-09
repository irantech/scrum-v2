<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ChecklistProcessRequest extends FormRequest
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
            'activeSection' => 'exists:App\Models\Section,id' ,
            'description'   => 'string',
            'type'          => 'required|string|in:managerConfirm,confirm,reverse,supportApprove,sign,staffSign,supportSign'
        ];
    }

    /***
     * Get the error messages for the defined validation rules.
     * @param  \Illuminate\Contracts\Validation\Validator
     * @return array
     */
    protected function failedValidation(Validator $validator)
    {
        $validators =  (new ValidationException($validator))->errors();
        $errors = [];
        foreach ($validators as $key => $value) {
            foreach ($value as $message) {
                array_push($errors , $message);
            }
        }
        throw new HttpResponseException(
            response()->json(['errors' => $errors], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
