<?php

namespace App\Http\Controllers\Database;

use App\Helpers\Define;
use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    private static $model = 'Barang';

    public function index(Request $request)
    {
        $input = $request->all();
        $breadcumbs = [['nama' => 'Database', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Barang', 'link' => route('database.barang.index'), 'active' => 'active']];
        $menuAssets = menuAssets('', 'barang', $breadcumbs, true);
        if (!$request->ajax()) return view('contents.database.barang.index', compact('menuAssets'));
        $search = strtolower($request['search']['value']);
        $constructor = Barang::when($search, function ($query, $value) {
            return $query->whereRaw("LOWER(name) LIKE '%$value%'")
            ->orwhereRaw("LOWER(alias) LIKE '%$value%'")
            ->orwhereRaw("LOWER(kode) LIKE '%$value%'");
        })->orderBy('id', 'DESC');
        $attribute = ['relTipe'];
        $input['usedAction'] = ['edit', 'delete'];
        return Define::fetch($input, $constructor, $attribute);
    }

    public function create(Request $request)
    {
        $response['render'] = view('contents.database.barang.form')->render();
        return $response;
    }

    public function edit($id, Request $request)
    {
        $data = Barang::where('id', $id)->first();
        $response['selected'] = [
            'select_tipe' => [
                'id'   => $data->id_tipe,
                'text' => $data->relTipe()->value('name')
            ]
        ];
        $response['render'] = view('contents.database.barang.form', compact('data', 'id'))->render();
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
