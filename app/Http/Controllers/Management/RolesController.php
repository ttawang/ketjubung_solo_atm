<?php

namespace App\Http\Controllers\Management;

use App\Helpers\Define;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolesController extends Controller
{
    private static $model = 'Roles';
    private static $division = 'Management';

    public function index(Request $request)
    {
        $breadcumbs = [['nama' => 'Management', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Roles', 'link' => route('management.roles.index'), 'active' => 'active']];
        $menuAssets = menuAssets('', 'roles', $breadcumbs, true);
        if (!$request->ajax()) return view('contents.management.roles.index', compact('menuAssets'));
        $input = $request->all();
        $search = strtolower($request['search']['value']);
        $constructor = Role::when($search, function ($query, $value) {
            return $query->whereRaw("LOWER(name) LIKE '%$value%'");
        })->orderBy('id', 'ASC');
        $input['btnExtras'] = ['<button type="button" onclick="mapingMenuForm(%id);" class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic">
            <i class="icon md-wrench mr-2"></i>
        </button>'];
        $input['usedAction'] = ['edit', 'delete'];
        return Define::fetch($input, $constructor);
    }

    public function create(Request $request)
    {
        $response['render'] = view('contents.management.roles.form')->render();
        return $response;
    }

    public function edit($id, Request $request)
    {
        $data = Role::where('id', $id)->first();
        $response['selected'] = [];
        $response['render'] = view('contents.management.roles.form', compact('data', 'id'))->render();
        return $response;
    }

    public function store(Request $request)
    {
        $input = $request->all()['input'];
        $input['initial_name'] = ucwords($input['name']);
        DB::beginTransaction();
        return Define::store($input, self::$model);
    }

    public function update($id, Request $request)
    {
        $input = $request->all()['input'];
        DB::beginTransaction();
        return Define::update($input, self::$model, $id);
    }

    public function destroy($id)
    {
        return Define::delete($id, self::$model);
    }
}
