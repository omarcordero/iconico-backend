<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiMenuItemCreateRequest extends FormRequest
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
            'title' => 'required|string',
            'detail' => 'required|string',
            'specification' => 'required|string',
            'image_url' => 'required|string',
            'price' => 'required|numeric',
            'delivery_fee' => 'integer',
            'is_available' => 'required|boolean',
            'is_non_veg' => 'required|boolean',
            'categories' => 'required|array|exists:categories,id',
            'groups' => 'array',
            'groups.*.title' => 'required',
            'groups.*.min_choices' => 'required|integer',
            'groups.*.max_choices' => 'required|integer',
            'groups.*.choices' => 'array|required',
            'groups.*.choices.*.title' => 'required',
            'groups.*.choices.*.price' => 'required',
        ];
    }
}
