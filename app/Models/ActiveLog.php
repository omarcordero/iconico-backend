<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class ActiveLog extends Model
{
    protected $table = 'active_logs';

    protected $fillable = ['user_id'];
}
