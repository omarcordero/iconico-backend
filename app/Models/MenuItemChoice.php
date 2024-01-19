<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItemChoice extends Model
{
    protected $table = 'menu_item_choices';

    protected $fillable = ['title', 'price', 'menu_item_group_id'];

    public function menuItemGroup()
    {
        return $this->belongsTo('App\Models\MenuItemGroup');
    }
}
