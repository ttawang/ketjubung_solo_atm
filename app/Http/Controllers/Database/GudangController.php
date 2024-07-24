<?php

namespace App\Http\Controllers\Database;

use App\Helpers\Define;
use App\Http\Controllers\Controller;
use App\Models\Gudang;
use Illuminate\Http\Request;

class GudangController extends Controller
{
    private static $model = 'Gudang';

    public function index(Request $request)
    {
        $input = $request->all();
        $breadcumbs = [['nama' => 'Database', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Gudang', 'link' => route('database.gudang.index'), 'active' => 'active']];
        $menuAssets = menuAssets('', 'gudang', $breadcumbs, true);
        if (!$request->ajax()) return view('contents.database.gudang.index', compact('menuAssets'));
        $search = strtolower($request['search']['value']);
        $constructor = Gudang::when($search, function ($query, $value) {
            return $query->whereRaw("LOWER(name) LIKE '%$value%'")
            ->orwhereRaw("LOWER(kode) LIKE '%$value%'");
        })->orderBy('id', 'ASC');
        $input['usedAction'] = ['edit', 'delete'];
        return Define::fetch($input, $constructor);
    }

    public function create(Request $request)
    {
        $response['render'] = view('contents.database.gudang.form')->render();
        return $response;
    }

    public function edit($id, Request $request)
    {
        $data = Gudang::where('id', $id)->first();
        $response['selected'] = [];
        $response['render'] = view('contents.database.gudang.form', compact('data', 'id'))->render();
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
