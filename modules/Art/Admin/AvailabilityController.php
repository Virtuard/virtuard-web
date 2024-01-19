<?php
namespace Modules\Art\Admin;

use Modules\Booking\Models\Booking;
use Modules\Art\Models\Art;
use Modules\Art\Models\ArtDate;

class AvailabilityController extends \Modules\Art\Controllers\AvailabilityController
{
    protected $artClass;
    protected $artDateClass;
    protected $bookingClass;
    protected $indexView = 'Art::admin.availability';

    public function __construct(Art $artClass, ArtDate $artDateClass,Booking $bookingClass)
    {
        $this->setActiveMenu(route('art.admin.index'));
        $this->middleware('dashboard');
        $this->artDateClass = $artDateClass;
        $this->bookingClass = $bookingClass;
        $this->artClass = $artClass;
    }

}
