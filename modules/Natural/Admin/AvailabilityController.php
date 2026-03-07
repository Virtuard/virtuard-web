<?php
namespace Modules\Natural\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\AdminController;
use Modules\Space\Models\Space;
use Modules\Space\Models\SpaceDate;

class AvailabilityController extends \Modules\Natural\Controllers\AvailabilityController
{
    protected $spaceClass;
    /**
     * @var SpaceDate
     */
    protected $spaceDateClass;
    protected $indexView = 'Natural::admin.availability';

    public function __construct()
    {
        parent::__construct();
        $this->setActiveMenu(route('natural.admin.index'));
        $this->middleware('dashboard');
    }

}
