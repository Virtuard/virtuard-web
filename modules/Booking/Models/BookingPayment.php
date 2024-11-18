<?php

namespace Modules\Booking\Models;

use App\BaseModel;
use App\Models\User;
use Modules\User\Models\Plan;

class BookingPayment extends BaseModel
{
    protected $table = 'bravo_user_plan';


    public static function getReferralHistory($ref_id = false)
{
    $list_booking = parent::query()->orderBy('id', 'desc');
    
    if (!empty($ref_id)) {
        $list_booking->where("ref_id", $ref_id);
    }

    // Add any other conditions as needed
    $list_booking->where('status', '!=', 'draft');
    $list_booking->whereIn('object_model', array_keys(get_bookable_services()));

    return $list_booking->paginate(10); // Pagination applied here
}

    public static function getReferralAdminHistory()
    {
        return self::query()->paginate(10); 
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
