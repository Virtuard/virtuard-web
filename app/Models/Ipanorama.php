<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\User\Models\User;
use Modules\Hotel\Models\Hotel;
use Modules\Space\Models\Space;
use Modules\Business\Models\Business;

class Ipanorama extends Model
{
    use SoftDeletes;

    protected $table = 'ipanoramas';

    protected $fillable = [
        'user_id', 
        'title', 
        'code',
        'json_data',
        'thumb',
        'status',
        'create_user',
        'update_user',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = \Illuminate\Support\Str::ulid();
            }
        });
    }

    public function author()
    {
        return $this->belongsTo(User::class, "user_id", "id")->withDefault();
    }

    public function listing()
    {
        return [
            'hotels' => $this->hotels(),
            'spaces' => $this->spaces(),
            'businesses' => $this->businesses(),
        ];
    }

    public function hotels()
    {
        return $this->hasMany(Hotel::class, 'ipanorama_id');
    }

    public function spaces()
    {
        return $this->hasMany(Space::class, 'ipanorama_id');
    }

    public function businesses()
    {
        return $this->hasMany(Business::class, 'ipanorama_id');
    }
    
    
}
