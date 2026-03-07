<?php

namespace App\Models;

use Chatify\Traits\UUID;
use Illuminate\Database\Eloquent\Model;

class ChMessage extends Model
{
    use UUID;

    protected $fillable = [
        'type', 'from_id', 'to_id', 'body', 'attachment', 'seen'
    ];

    public function sentMessages()
    {
        return $this->hasMany(ChMessage::class, 'from_id');
    }

    // Define the relationship for messages received by the user
    public function receivedMessages()
    {
        return $this->hasMany(ChMessage::class, 'to_id');
    }
}
