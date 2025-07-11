<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rules;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Role;

class RegisterRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:users,name',
            'email' => 'required|string|email|max:255|unique:users,email',
            'city' => ['required', 'in:La Paz,Cochabamba'],
            'password' => ['required', 'confirmed', 'min:8', Rules\Password::defaults()],
            'password_confirmation' => 'required|same:password',
            'role_id' => 'required|exists:roles,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'role_id.exists' => 'El rol seleccionado no es válido.',
            'role_id.required' => 'Debe seleccionar un rol.'
        ];
    }
}