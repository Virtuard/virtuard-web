<?php

namespace App\Http\Controllers\Admin;

use App\Models\Ipanorama;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class Virtuard360Controller extends Controller
{
    protected $model;

    public function __construct()
    {
        $this->model = new Ipanorama();  
    }

    public function index(Request $request)
    {
        $query = $this->model->query();
        $query->orderBy('id', 'desc');
        if (!empty($s = $request->input('s'))) {
            $query->where('title', 'LIKE', '%' . $s . '%');
            $query->orderBy('title', 'asc');
        }
        $data = [
            'rows'                => $query->with(['author'])->paginate(20),
            'breadcrumbs'         => [
                [
                    'name' => __('Virtuard 360'),
                    'url'  => route('admin.virtuard360.index')
                ],
                [
                    'name'  => __('All'),
                    'class' => 'active'
                ],
            ],
            'page_title'          => __("Virtuard360 Management")
        ];
        return view('admin.virtuard360.index', $data);
    }

    public function show($id)
    {
        $data = [
            'panorama' => Ipanorama::find($id),
        ];

        return view('admin.virtuard360.show', $data);
    }

    public function create(Request $request)
    {
        $id = $request->id;
        if ($id) {
            $row = $this->model->find($id);
        } else {
            $row = new $this->model();
            $row->fill([
                'status' => 'publish'
            ]);
        }

        $data = [
            'row'               => $row,
            'breadcrumbs'       => [
                [
                    'name' => __('Virtuard 360'),
                    'url'  => route('admin.virtuard360.index')
                ],
                [
                    'name'  => __('Add Virtuard 360'),
                    'class' => 'active'
                ],
            ],
            'page_title'        => __("Add Virtuard 360"),
        ];
        return view('admin.virtuard360.create', $data);
    }

    public function edit(Request $request)
    {
        $id = $request->id;
        $row = $this->model->find($id);
        $data = [
            'row'               => $row,
            'breadcrumbs'       => [
                [
                    'name' => __('Virtuard 360'),
                    'url'  => route('admin.virtuard360.index')
                ],
                [
                    'name'  => __('Edit Virtuard 360'),
                    'class' => 'active'
                ],
            ],
            'page_title'        => __("Edit Virtuard 360"),
        ];
        return view('admin.virtuard360.edit', $data);
    }

    public function store(Request $request)
    {
        $attr = $request->all();

        $id = $request->id;
        if ($id) {
            $row = $this->model->find($id);
            if (empty($row)) {
                return redirect(route('admin.virtuard360.index'));
            }
            $row->update($attr);
        } else {
            $row = $this->model->create($attr);
        }

        return redirect(route('admin.virtuard360.edit', ['id' => $row->id,['user_id' => $row->create_user]]))->with('success', __('Virtuard 360 updated'));
    }
}