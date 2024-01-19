<?php

namespace App\Models;

use App\Models\Auth\User\Traits\Ables\Protectable;
use App\Models\Auth\User\Traits\Attributes\UserAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Auth\User\Traits\Ables\Rolable;
use App\Models\Auth\User\Traits\Scopes\UserScopes;
use App\Models\Auth\User\Traits\Relations\UserRelations;
use Kyslik\ColumnSortable\Sortable;
use Laravel\Passport\HasApiTokens;

/**
 * @property  user_id
 * @property mixed store_id
 */
class Favourite extends Model
{

    protected $table = 'favourites';

    protected $fillable = ['store_id', 'user_id'];

    protected $hidden = ['store_id'];

    protected $with = array('store');

    public function store()
    {
        return $this->belongsTo('App\Models\Store', 'store_id');
    }
}
