<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'name' => 'required|max:255',
            'mobile_number' => 'required|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'min:6|confirmed',
            'role' => 'required|exists:roles,id',
            'mobile_verified' => 'sometimes|boolean'
        ];
    }
}
