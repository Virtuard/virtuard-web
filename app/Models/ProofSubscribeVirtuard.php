<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProofSubscribeVirtuard extends Model
{
    public $incrementing = true;
    protected $table = 'proof_subscribe_virtuard';

    protected $fillable = ['id_subscribe', 'date', 'proof_url'];
    public $timestamps = true;
}
