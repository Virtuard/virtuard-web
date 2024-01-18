<?php
namespace Modules\Cultural\Admin;

use Modules\Booking\Models\Booking;
use Modules\Cultural\Models\Cultural;
use Modules\Cultural\Models\CulturalDate;

class AvailabilityController extends \Modules\Cultural\Controllers\AvailabilityController
{
    protected $culturalClass;
    protected $culturalDateClass;
    protected $bookingClass;
    protected $indexView = 'Cultural::admin.availability';

    public function __construct(Cultural $culturalClass, CulturalDate $culturalDateClass,Booking $bookingClass)
    {
        $this->setActiveMenu(route('cultural.admin.index'));
        $this->middleware('dashboard');
        $this->culturalDateClass = $culturalDateClass;
        $this->bookingClass = $bookingClass;
        $this->culturalClass = $culturalClass;
    }

}
