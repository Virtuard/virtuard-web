<?php
namespace Modules\Business\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\AdminController;
use Modules\Business\Models\Business;
use Modules\Business\Models\BusinessDate;

class AvailabilityController extends \Modules\Business\Controllers\AvailabilityController
{
    protected $businessClass;
    /**
     * @var BusinessDate
     */
    protected $businessDateClass;
    protected $indexView = 'Business::admin.availability';

    public function __construct()
    {
        parent::__construct();
        $this->setActiveMenu(route('business.admin.index'));
        $this->middleware('dashboard');
    }

}
