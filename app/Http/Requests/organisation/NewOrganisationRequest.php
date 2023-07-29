<?php

namespace App\Http\Requests\Organisation;

use App\Http\Traits\GeneraleTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class NewOrganisationRequest extends FormRequest
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
            "nom"             => "required|string",
            "adresse"         => "required|string",
            "code_postal"     => "required|numeric",
            "ville"           => "required|string",
            "statut"          => "required|in:Lead,Client,Prospect",
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
