<?php

namespace App\Http\Controllers\Management;

use App\Helpers\Define;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    private static $model = 'Users';

    public function index(Request $request)
    {
        $breadcumbs = [['nama' => 'Management', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Users', 'link' => route('management.users.index'), 'active' => 'active']];
        $menuAssets = menuAssets('', 'users', $breadcumbs, true);
        if (!$request->ajax()) return view('contents.management.users.index', compact('menuAssets'));
        $input = $request->all();
        $search = strtolower($request['search']['value']);
        $constructor = User::with(['relRoles'])->when($search, function ($query, $value) {
            return $query->whereRaw("LOWER(name) LIKE '%$value%'")
            ->orwhereRaw("LOWER(email) LIKE '%$value%'");
        })->orderBy('id', 'ASC');
        $input['usedAction'] = ['edit', 'delete'];
        return Define::fetch($input, $constructor);
    }

    public function create(Request $request)
    {
        $response['render'] = view('contents.management.users.form')->render();
        return $response;
    }

    public function edit($id, Request $request)
    {
        $data = User::where('id', $id)->first();
        $response['selected'] = [
            'select_role' => [
                'id' => $data->roles_id,
                'text' => $data->roles_name
            ]
        ];
        $response['render'] = view('contents.management.users.form', compact('data', 'id'))->render();
        return $response;
    }

    public function store(Request $request)
    {
        $input = $request->all()['input'];
        $input['password'] = Hash::make($input['password']);
        DB::beginTransaction();
        return Define::store($input, self::$model);
    }

    public function update($id, Request $request)
    {
        $input = $request->all()['input'];
        $input['password'] = Hash::make($input['password']);
        if ($input['password'] == '') unset($input['password']);
        DB::beginTransaction();
        return Define::update($input, self::$model, $id);
    }

    public function destroy($id)
    {
        return Define::delete($id, self::$model);
    }
}
