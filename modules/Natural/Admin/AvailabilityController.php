<?php
namespace Modules\Natural\Admin;

use Modules\Booking\Models\Booking;
use Modules\Natural\Models\Natural;
use Modules\Natural\Models\NaturalDate;

class AvailabilityController extends \Modules\Natural\Controllers\AvailabilityController
{
    protected $naturalClass;
    protected $naturalDateClass;
    protected $bookingClass;
    protected $indexView = 'Natural::admin.availability';

    public function __construct(Natural $naturalClass, NaturalDate $naturalDateClass,Booking $bookingClass)
    {
        $this->setActiveMenu(route('natural.admin.index'));
        $this->middleware('dashboard');
        $this->naturalDateClass = $naturalDateClass;
        $this->bookingClass = $bookingClass;
        $this->naturalClass = $naturalClass;
    }

}
