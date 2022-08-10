<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuthRegisterRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'user_type' => [
                'required',
                Rule::in(['APRENDIZ', 'INSTRUCTOR'])
            ],
            'name' => 'required|string',
            'last_name' => 'required|string',
            'phone_number' => 'required|numeric',
            'identification_type' => 'required|string',
            'identification_number' => 'required|integer',
            'birth_date' => 'required|date_format:Y-m-d',
            'email' => 'required|string|unique:users,email',
        ];
    }
}
