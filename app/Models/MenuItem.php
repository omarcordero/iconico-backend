<?php

namespace App\Models;

use App\Models\Auth\User\Traits\Ables\Protectable;
use App\Models\Auth\User\Traits\Attributes\UserAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Auth\User\Traits\Ables\Rolable;
use App\Models\Auth\User\Traits\Scopes\UserScopes;
use App\Models\Auth\User\Traits\Relations\UserRelations;
use Kyslik\ColumnSortable\Sortable;
use Laravel\Passport\HasApiTokens;

class MenuItem extends Model
{

    protected $table = 'menu_items';

    protected $fillable = ['title', 'detail', 'specification', 'image_url', 'price',
        'is_available', 'is_non_veg', 'status', 'store_id'];

    protected $with = array('categories', 'menuItemGroups');

    public function store()
    {
        return $this->belongsTo('App\Models\Store', 'store_id');
    }

    public function categories()
    {
        return $this->belongsToMany('App\Models\Category', 'menu_items_categories');
    }

    public function menuItemGroups()
    {
        return $this->hasMany('App\Models\MenuItemGroup');
    }

    public static function search($user, Request $request)
    {
        $storeIds = Store::search($user, $request)->pluck('id');
        $menuItems = MenuItem::whereIn('store_id', $storeIds)
		->where('title', 'like', '%' . $request->dish_name . '%')
		->with('store');
        return $menuItems;
    }

}
