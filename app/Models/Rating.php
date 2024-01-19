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
 * @property  rating
 * @property  review
 * @property  store_id
 * @property  user_id
 */
class Rating extends Model
{
    use Sortable;

    protected $table = 'ratings';

    protected $fillable = ['rating', 'review', 'store_id', 'user_id'];

    protected $hidden = ['user_id'];

    protected $with = array('user', 'store');

    public function user()
    {
        return $this->belongsTo('App\Models\Auth\User\User', 'user_id');
    }

    public function store()
    {
        return $this->belongsTo('App\Models\Store', 'store_id');
    }
}
