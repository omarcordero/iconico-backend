<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItemGroup extends Model
{
    protected $table = 'menu_item_groups';

    protected $fillable = ['title', 'min_choices', 'max_choices', 'menu_item_id'];

    protected $with = array('menuItemChoices');

    public function menuItem()
    {
        return $this->belongsTo('App\Models\MenuItem');
    }

    public function menuItemChoices()
    {
        return $this->hasMany('App\Models\MenuItemChoice');
    }
}
