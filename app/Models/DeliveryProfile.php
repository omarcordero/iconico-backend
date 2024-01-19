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

class DeliveryProfile extends Model
{
    use Sortable;

    protected $table = 'delivery_profiles';

    protected $fillable = ['is_online', 'longitude', 'latitude', 'assigned', 'user_id', 'created_by'];

    protected $with = ['user'];

    public static function search($storeId)
    {
        $store = Store::find($storeId);
        $distanceDelta = 8000;

        $deliveryProfiles = DeliveryProfile::where('is_online', true)->where('assigned', false);


        if ($store->delivery_preference == 'owner') {
            // search for only delivery profiles that were created by the given store
            $deliveryProfiles = $deliveryProfiles->where('created_by', $store->owner_id);
        }

        if ($store->delivery_preference == 'favourite') {
            // search for only delivery profiles that were marked as favourite by the given store
            $deliveryProfileIds = FavouriteDriver::where('store_id', $storeId)->get()->pluck(['delivery_profile_id'])->all();
            $deliveryProfiles = $deliveryProfiles->whereIn('id', $deliveryProfileIds);
        }



        //SI NO HAY NINGÚN REPARTIDOR DE LOS QUE YO HE CREADO, RECIÉN LE METEMOS EL MIXTO.



        $subqueryDistance = "*, ST_Distance_Sphere(Point(delivery_profiles.longitude,"
            . " delivery_profiles.latitude),"
            . " Point($store->longitude, $store->latitude ))"
            . " as distance";



        //CREATED BY 0, DONDE 0 ES EL ID DEL USUARIO MAESTRO PIDEYA|

        $deliveryProfiles = $deliveryProfiles->selectRaw($subqueryDistance)->havingRaw('distance < ' . $distanceDelta)->get();

        return $deliveryProfiles;
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Auth\User\User', 'user_id');
    }

}
