<?php
namespace Modules\Cultural\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\AdminController;
use Modules\Space\Models\Space;
use Modules\Space\Models\SpaceDate;

class AvailabilityController extends \Modules\Cultural\Controllers\AvailabilityController
{
    protected $spaceClass;
    /**
     * @var SpaceDate
     */
    protected $spaceDateClass;
    protected $indexView = 'Cultural::admin.availability';

    public function __construct()
    {
        parent::__construct();
        $this->setActiveMenu(route('cultural.admin.index'));
        $this->middleware('dashboard');
    }

}
