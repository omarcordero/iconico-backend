<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class ApiOrderCreateRequest extends FormRequest
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
            'discount' => 'required|numeric',
            'special_instructions' => 'string|nullable',
            'address_id' => '',
            'store_id' => 'required|exists:stores,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'items' => 'required|array',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.choices' => 'array',
            'items.*.choices.*.menu_item_choice_id' => 'required|exists:menu_item_choices,id',
            'type' => 'in:ASAP,LATER|nullable',
            'scheduled_on' => 'required_if:type,LATER|date_format:Y-m-d H:i:s',
            'ruc_comp' => '',
            'dni_comp' => '',
            'direccion_comp' => '',
            'rsocial_comp' => '',
            'tipo_comprobante' => '',
            'pago_con' => ''

        ];
    }
}
