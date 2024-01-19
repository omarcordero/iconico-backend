<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemChoice extends Model
{
    protected $table = 'order_item_choices';

    protected $fillable = ['order_id', 'menu_item_id', 'menu_item_choice_id', 'total'];

    protected $with = ['menuitemchoice'];

    public function menuitemchoice()
    {
        return $this->belongsTo('App\Models\MenuItemChoice', 'menu_item_choice_id');
    }
}
