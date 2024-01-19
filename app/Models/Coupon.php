<?php

namespace App\Models;

use App\Exceptions\CouponException;
use App\Models\Auth\User\Traits\Ables\Protectable;
use App\Models\Auth\User\Traits\Attributes\UserAttributes;
use App\Models\Auth\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Auth\User\Traits\Ables\Rolable;
use App\Models\Auth\User\Traits\Scopes\UserScopes;
use App\Models\Auth\User\Traits\Relations\UserRelations;
use Kyslik\ColumnSortable\Sortable;
use Laravel\Passport\HasApiTokens;

class Coupon extends Model
{

    protected $table = 'coupons';

    protected $fillable = ['code', 'reward', 'type', 'data', 'expires_at'];

    public function isExpired()
    {
        return $this->expires_at ? Carbon::now()->gte($this->expires_at) : false;
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'coupons_users')
            ->withPivot('used_at');
    }

    /**
     * @param $coupon
     * @param $user
     * @return bool
     * @throws CouponException
     */
    public static function checkValidity($coupon, $user)
    {
        $isUsed = $coupon->users()->wherePivot('user_id', $user->id)->exists();

        if($isUsed) {
            throw new CouponException('Code already used');
        }

        $isExpired = $coupon->expires_at ? Carbon::now()->gte($coupon->expires_at) : false;

        if($isExpired) {
            throw new CouponException('Code expired');
        }

        return true;
    }
}
