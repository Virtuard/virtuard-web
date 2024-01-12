<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MediaFile;

class Business extends Model
{
    public $incrementing = true;
    protected $table = 'bravo_hotels';

    public $timestamps = false;

    public function image()
    {
        return $this->belongsTo(MediaFile::class, 'image_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'create_user');
    }
}
