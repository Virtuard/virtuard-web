<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscribeVirtuard extends Model
{
    public $incrementing = true;
    protected $table = 'subscribe_virtuard';

    protected $fillable = ['id_user', 'status', 'start_date', 'expired_date'];
    public $timestamps = true;
}
