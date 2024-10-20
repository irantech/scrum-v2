<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class MeetingsDetailsStoreRequest extends FormRequest
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
            'approval_status'=>'required|in:accept,reject,close',
            'description'=>'required_if:approval_status,not_confirmed|string ',
            'date_time'=>'required_if:approval_status,confirmed|date',
            'result'=>'required_if:approval_status,close',
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
