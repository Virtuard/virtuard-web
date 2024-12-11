<?php

namespace Modules\Booking\Models;

use App\BaseModel;
use App\Models\User;
use Modules\User\Models\Plan;

class BookingPayment extends BaseModel
{
    protected $table = 'bravo_user_plan';


    public static function getReferralHistory($userId)
    {
        return self::where('referal_user_id', $userId)
        ->where('status', 1)
        ->orderBy('created_at', 'desc');
    }

    public static function getReferralAdminHistory()
    {
        return self::query()
        ->where('status', 1)
        ->paginate(10);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'create_user');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function referalName()
    {
        return $this->belongsTo(User::class, 'referal_user_id');
    }
}
