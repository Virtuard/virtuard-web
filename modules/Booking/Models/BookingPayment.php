<?php

namespace Modules\Booking\Models;

use App\BaseModel;
use App\Models\User;
use Modules\User\Models\Plan;

class BookingPayment extends BaseModel
{
    protected $table = 'bravo_user_plan';
    protected $casts = [
        'amount' => 'float', // 👈 ensures amount is returned as a float
    ];


    public static function getReferralHistory($userId)
    {
        return self::where('referal_user_id', $userId)
        ->where('status', 1)
        ->orderBy('created_at', 'desc');
    }
    
    
    // This function only for API `referral report`
    public static function getReferralHistoryAPI($userId)
    {
        return self::where('user_id', $userId)
            ->join("users", "users.id", "=", "bravo_user_plan.referal_user_id")
            ->join("bravo_plans","bravo_plans.id", "=", "bravo_user_plan.plan_id")
            ->select("bravo_user_plan.id AS referral_id", "bravo_user_plan.referal_amount AS amount", "bravo_user_plan.created_at AS created_at", "users.name as user_name", "bravo_plans.title as plan_name")
            ->where('bravo_user_plan.status', 1)
            ->orderBy('bravo_user_plan.created_at', 'desc');
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
