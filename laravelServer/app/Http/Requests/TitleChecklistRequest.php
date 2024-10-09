<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TitleChecklistRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if($this->method() == 'POST') {
            return [
                'title'      =>  'required' ,
                'checklist_id' => 'required|exists:App\Models\Checklist,id' ,
                'section'      => 'required|exists:App\Models\Section,id'
            ];
        }
        return [
            'title'      =>  'required' ,
            'checklist_id' => 'required|exists:App\Models\Checklist,id',
            'section'      => 'required|exists:App\Models\Section,id'
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
