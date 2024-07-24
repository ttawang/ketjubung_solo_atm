<?php

namespace App\Http\Controllers\Database;

use App\Helpers\Define;
use App\Http\Controllers\Controller;
use App\Models\NomorBeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NomorBeamController extends Controller
{
    private static $model = 'NomorBeam';

    public function index(Request $request)
    {
        $input = $request->all();
        $breadcumbs = [['nama' => 'Database', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Nomor Beam', 'link' => route('database.nomor_beam.index'), 'active' => 'active']];
        $menuAssets = menuAssets('', 'nomor beam', $breadcumbs, true);
        if (!$request->ajax()) return view('contents.database.nomor_beam.index', compact('menuAssets'));
        $search = strtolower($request['search']['value']);
        $constructor = NomorBeam::when($search, function ($query, $value) {
            return $query->whereRaw("LOWER(name) LIKE '%$value%'")
            ->orwhereRaw("LOWER(alias) LIKE '%$value%'");
        })->orderBy('created_at', 'DESC');
        $input['usedAction'] = ['edit', 'delete'];
        $attributes = ['relStatusNomorBeam'];
        return Define::fetch($input, $constructor, $attributes, [] , [], ['aksi', 'status_beam']);
    }

    public function create(Request $request)
    {
        $response['render'] = view('contents.database.nomor_beam.form')->render();
        return $response;
    }

    public function edit($id, Request $request)
    {
        $data = NomorBeam::where('id', $id)->first();
        $response['selected'] = [];
        $response['render'] = view('contents.database.nomor_beam.form', compact('data', 'id'))->render();
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
