<?php

namespace App\Http\Controllers\Database;

use App\Helpers\Define;
use App\Http\Controllers\Controller;
use App\Models\Tipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TipeController extends Controller
{
    private static $model = 'Tipe';

    public function index(Request $request)
    {
        $input = $request->all();
        $breadcumbs = [['nama' => 'Database', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Tipe', 'link' => route('database.tipe.index'), 'active' => 'active']];
        $menuAssets = menuAssets('', 'tipe', $breadcumbs, true);
        if (!$request->ajax()) return view('contents.database.tipe.index', compact('menuAssets'));
        $search = strtolower($request['search']['value']);
        $constructor = Tipe::when($search, function ($query, $value) {
            return $query->whereRaw("LOWER(name) LIKE '%$value%'")
            ->orwhereRaw("LOWER(alias) LIKE '%$value%'")
            ->orwhereRaw("LOWER(jenis) LIKE '%$value%'");
        })->orderBy('created_at', 'DESC');
        $input['usedAction'] = ['edit', 'delete'];
        return Define::fetch($input, $constructor);
    }

    public function create(Request $request)
    {
        $response['render'] = view('contents.database.tipe.form')->render();
        return $response;
    }

    public function edit($id, Request $request)
    {
        $data = Tipe::where('id', $id)->first();
        $response['selected'] = [];
        $response['render'] = view('contents.database.tipe.form', compact('data', 'id'))->render();
        return $response;
    }

    public function store(Request $request)
    {
        $input = $request->all()['input'];
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