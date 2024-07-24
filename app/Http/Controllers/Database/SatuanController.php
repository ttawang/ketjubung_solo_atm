<?php

namespace App\Http\Controllers\Database;

use App\Helpers\Define;
use App\Http\Controllers\Controller;
use App\Models\Satuan;
use Illuminate\Http\Request;

class SatuanController extends Controller
{
    private static $model = 'Satuan';

    public function index(Request $request)
    {
        $input = $request->all();
        $breadcumbs = [['nama' => 'Database', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Tipe', 'link' => route('database.satuan.index'), 'active' => 'active']];
        $menuAssets = menuAssets('', 'satuan', $breadcumbs, true);
        if (!$request->ajax()) return view('contents.database.satuan.index', compact('menuAssets'));
        $search = strtolower($request['search']['value']);
        $constructor = Satuan::when($search, function ($query, $value) {
            return $query->whereRaw("LOWER(name) LIKE '%$value%'");
        })->orderBy('created_at', 'DESC');
        $input['usedAction'] = ['edit', 'delete'];
        return Define::fetch($input, $constructor);
    }

    public function create(Request $request)
    {
        $response['render'] = view('contents.database.satuan.form')->render();
        return $response;
    }

    public function edit($id, Request $request)
    {
        $data = Satuan::where('id', $id)->first();
        $response['selected'] = [];
        $response['render'] = view('contents.database.satuan.form', compact('data', 'id'))->render();
        return $response;
    }

    public function store(Request $request)
    {
        $input = $request->all()['input'];
        return Define::store($input, self::$model);
    }

    public function update($id, Request $request)
    {
        $input = $request->all()['input'];
        return Define::update($input, self::$model, $id);
    }

    public function destroy($id)
    {
        return Define::delete($id, self::$model);
    }
}
