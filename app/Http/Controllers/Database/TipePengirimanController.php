<?php

namespace App\Http\Controllers\Database;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TipePengiriman;
use App\Models\Gudang;
use App\Helpers\Define;

class TipePengirimanController extends Controller
{
    private static $model = 'TipePengiriman';

    public function index(Request $request)
    {

        $input = $request->all();
        $breadcumbs = [['nama' => 'Database', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Tipe Pengiriman', 'link' => route('database.tipe_pengiriman.index'), 'active' => 'active']];
        $menuAssets = menuAssets('', 'tipe pengiriman', $breadcumbs, true);
        if (!$request->ajax()) return view('contents.database.tipe_pengiriman.index', compact('menuAssets'));
        $search = strtolower($request['search']['value']);
        $constructor = TipePengiriman::when($search, function ($query, $value) {
            return $query->whereRaw("LOWER(name) LIKE '%$value%'")
            ->orwhereRaw("LOWER(title) LIKE '%$value%'");
        })->orderBy('created_at', 'DESC');
        $attribute = ['relGudangAsal', 'relGudangTujuan'];
        $input['usedAction'] = ['edit', 'delete'];
        return Define::fetch($input, $constructor, $attribute);
        // $data = TipePengiriman::join('tbl_gudang', 'tbl_gudang.id', '=', 'tbl_tipe_pengiriman.id_gudang_asal')
        //     ->get(['tbl_tipe_pengiriman.*', 'tbl_gudang.name']);

        // return view('contents.database.tipe_pengiriman.index', compact('menuAssets', ''));
    }
    public function create()
    {

        $response['render'] = view('contents.database.tipe_pengiriman.form')->render();
        return $response;
    }
    public function edit($id)
    {
        $data = TipePengiriman::where('id', $id)->first();
        $response['selected'] = [
            'select_gudang_asal' => [
                'id'   => $data->id_gudang_asal,
                'text' => $data->relGudangAsal()->value('name')
            ],
            'select_gudang_tujuan' => [
                'id'   => $data->id_gudang_tujuan,
                'text' => $data->relGudangTujuan()->value('name')
            ]
        ];
        $response['render'] = view('contents.database.tipe_pengiriman.form', compact('data', 'id'))->render();
        return $response;
    }
    public function store(Request $request)
    {
        $input = $request->all();
        return Define::store($input, self::$model);
    }
    public function update(Request $request, $id)
    {
        $input = $request->except(['_method']);
        return Define::update($input, self::$model, $id);
    }
    public function destroy($id)
    {
        return Define::delete($id, self::$model);
    }
}
