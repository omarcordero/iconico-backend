<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiStoreUpdateRequest extends FormRequest
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
            'name' => 'string',
            'tagline' => 'string',
            'image_url' => 'string',
            'delivery_time' => 'string',
            'minimum_order' => 'integer',
            'delivery_fee' => 'numeric',
            'details' => 'string',
            'delivery_limit' => 'integer',
            'area' => 'string',
            'address' => 'string',
            'longitude' => 'numeric',
            'latitude' => 'numeric',
            'preorder' => 'boolean',
            'status' => 'string',
            'opens_at' => 'string',
            'closes_at' => 'string',
            'delivery_preference' => 'in:any,favourite,owner'
        ];
    }
}
