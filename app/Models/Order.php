<?php

namespace App\Models;

use App\Helpers\PushNotificationHelper;
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

class Order extends Model
{

    protected $table = 'orders';

    protected $fillable = ['subtotal', 'taxes', 'delivery_fee', 'total', 'discount', 'status', 'delivery_status',
        'payment_status', 'special_instructions', 'address_id', 'store_id', 'user_id', 'delivery_profile_id',
        'payment_method_id', 'type', 'scheduled_on', 'reject_reason','ruc_comp','dni_comp','direccion_comp','rsocial_comp','tipo_comprobante','pago_con'];

        

    protected $with = ['orderitems', 'user', 'store', 'address', 'deliveryProfile', 'orderitemchoices'];

    public function orderitems()
    {
        return $this->hasMany('App\Models\OrderItem');
    }

    public function orderitemchoices()
    {
        return $this->hasMany('App\Models\OrderItemChoice');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Auth\User\User', 'user_id');
    }

    public function store()
    {
        return $this->belongsTo('App\Models\Store', 'store_id');
    }

    public function deliveryProfile()
    {
        return $this->belongsTo('App\Models\DeliveryProfile', 'delivery_profile_id');
    }

    public function address()
    {
        return $this->belongsTo('App\Models\Address', 'address_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo('App\Models\PaymentMethod', 'payment_method_id');
    }

    public function allotDeliveryPerson()
    {
        $deliveryProfiles = DeliveryProfile::search($this->store_id);
        // $deliveryProfiles = DeliveryProfile::where('assigned', 0)->get();
        if (count($deliveryProfiles) > 0) {
            $deliveryProfile = $deliveryProfiles[0];
            $this->delivery_profile_id = $deliveryProfile->id;
            $this->delivery_status = 'allotted';
            $this->save();

            // set the assigned field of delivery boy to true implying delivery boy is not available for pickup
            $deliveryProfile->assigned = true;
            $deliveryProfile->save();

            // send notification to delivery person
            PushNotificationHelper::send($deliveryProfile->user->fcm_registration_id,
            'Nuevo Pedido', 'Has recibido un nuevo pedido', ["order_id" => $this->id]);
                //'New Delivery', 'You have received new delivery order', ["order_id" => $this->id]);

            return true;
        }

        return false;
    }
}
