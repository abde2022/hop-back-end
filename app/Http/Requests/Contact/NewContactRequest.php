<?php

namespace App\Http\Requests\Contact;

use App\Http\Traits\GeneraleTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class NewContactRequest extends FormRequest
{
    use GeneraleTrait;
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "cle"             => "required|alpha_num",
            "organisation_id" => "required|numeric",
            "e_mail"          => "required||email",
            "nom"             => "required|string",
            "prenom"          => "required|string",
            'created_at'      => "required|date|date_format:Y-m-d H:i:s",
            'updated_at'      => "required|date|date_format:Y-m-d H:i:s",
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->returnError(422, "The given data was invalid."));
    }
}
