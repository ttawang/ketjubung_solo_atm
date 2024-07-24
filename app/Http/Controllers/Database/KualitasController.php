<?php

namespace App\Http\Controllers\Database;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kualitas;
use App\Helpers\Define;
use App\Models\MappingKualitas;

class KualitasController extends Controller
{
    private static $model = 'Kualitas';
    private static $modelDetail = 'MappingKualitas';
    public function index(Request $request)
    {

        $input = $request->all();
        $input['isDetail'] = 'false';
        $breadcumbs = [['nama' => 'Database', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Kualitas', 'link' => route('database.kualitas.index'), 'active' => 'active']];
        $menuAssets = menuAssets('', 'kualitas', $breadcumbs, true);
        if (!$request->ajax()) return view('contents.database.kualitas.index', compact('menuAssets'));
        $search = strtolower($request['search']['value']);
        $constructor = Kualitas::when($search, function ($query, $value) {
            return $query->whereRaw("LOWER(grade) LIKE '%$value%'")
            ->orwhereRaw("LOWER(alias) LIKE '%$value%'");
        })->orderBy('grade', 'ASC');
        $input['usedAction'] = ['edit', 'delete', 'detail'];
        return Define::fetch($input, $constructor);
    }

    public function show($id, Request $request)
    {
        $input = $request->all();
        $input['isDetail'] = 'true';
        $input['usedAction'] = ['edit', 'delete'];
        $search = strtolower($request['search']['value']);
        $constructor = MappingKualitas::when($search, function ($query, $value) {
            return $query->whereRaw("LOWER(kode) LIKE '%$value%'")
            ->orwhereRaw("LOWER(name) LIKE '%$value%'");
        })
            ->where(['id_kualitas' => $id])
            ->orderBy('created_at', 'DESC');
        return Define::fetch($input, $constructor);
    }

    public function create(Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $idParent = $request['id'];
            $data = Kualitas::where('id', $idParent)->first();
            $response['render'] = view('contents.database.kualitas.mapping.form', compact('data', 'idParent'))->render();
        } else {
            $response['render'] = view('contents.database.kualitas.form')->render();
        }
        return $response;
    }

    public function edit($id, Request $request)
    {

        if ($request['isDetail'] == 'true') {
            $data = MappingKualitas::where('id', $id)->first();
            $idParent =  $data->id_kualitas;
            $response['render'] = view('contents.database.kualitas.mapping.form', compact('data', 'idParent', 'id'))->render();
        } else {
            $data = Kualitas::where('id', $id)->first();
            $response['render'] = view('contents.database.kualitas.form', compact('data', 'id'))->render();
        }
        return $response;
    }

    public function store(Request $request)
    {
        $input = $request->all()['input'];
        $isDetail = $request->isDetail == 'true';
        $usingModel = $isDetail ?  self::$modelDetail : self::$model;
        return Define::store($input, $usingModel);
    }

    public function update(Request $request, $id)
    {
        $input = $request->all()['input'];
        $isDetail = $request->isDetail == 'true';
        $usingModel = $isDetail ?  self::$modelDetail : self::$model;
        return Define::update($input, $usingModel, $id);
    }

    public function destroy($id, Request $request)
    {
        $isDetail = $request->isDetail == 'true';
        $usingModel = $isDetail ?  self::$modelDetail : self::$model;
        return Define::delete($id, $usingModel);
    }
}
