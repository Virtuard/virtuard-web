<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Art\Models\Art;
use Modules\Boat\Models\Boat;
use Modules\Business\Models\Business;
use Modules\Cultural\Models\Cultural;
use Modules\Event\Models\Event;
use Modules\Hotel\Models\Hotel;
use Modules\Natural\Models\Natural;
use Modules\Space\Models\Space;

class CompressImageController extends Controller
{
    protected $model;
    protected $hotel;
    protected $space;
    protected $business;
    protected $boat;
    protected $event;
    protected $art;
    protected $cultural;
    protected $natural;

    public function __construct()
    {
        $this->hotel = new Hotel();
        $this->space = new Space();
        $this->business = new Business();
        $this->boat = new Boat();
        $this->event = new Event();
        $this->art = new Art();
        $this->cultural = new Cultural();
        $this->natural = new Natural();
    }

    public function index(Request $request)
    {
        try {
            $service = $request->service;
            $ids = $request->ids;

            if ($id = $request->id ?? "") {
                $ids[] = $id;
            }

            $this->model = $this->$service;
    
            foreach ($ids as $id) {
                $model = $this->model->where('slug', $id)->first();
                if(empty($model)){
                    $model = $this->model->find($id);
                }

                if ($model) {
                    $model->timestamps = false;
                    $model->image_id = resize_feature_image($model->image_id);
                    $model->save();
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'success',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'error',
            ]);
        }
    }
}