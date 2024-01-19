<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class Transaction extends Model
{

    protected $table = 'transactions';

    protected $fillable = ['title', 'description', 'status', 'amount', 'user_id', 'is_paid',
        'source', 'image_url', 'order_id'];

    protected $with = array('user', 'order');

    public function user()
    {
        return $this->belongsTo('App\Models\Auth\User\User', 'user_id');
    }

    public function order()
    {
        return $this->belongsTo('App\Models\Order', 'order_id');
    }
}
