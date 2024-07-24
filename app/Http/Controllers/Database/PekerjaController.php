<?php

namespace App\Http\Controllers\Database;

use App\Helpers\Define;
use App\Http\Controllers\Controller;
use App\Models\Pekerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PekerjaController extends Controller
{
    private static $model = 'Pekerja';

    public function index(Request $request)
    {
        $input = $request->all();
        $breadcumbs = [['nama' => 'Database', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Pekerja', 'link' => route('database.pekerja.index'), 'active' => 'active']];
        $menuAssets = menuAssets('', 'pekerja', $breadcumbs, true);
        if (!$request->ajax()) return view('contents.database.pekerja.index', compact('menuAssets'));
        $search = strtolower($request['search']['value']);
        $constructor = Pekerja::when($search, function ($query, $value) {
            return $query
                ->whereRaw("LOWER(name) LIKE '%$value%'")
                ->orwhereRaw("LOWER(no_register) LIKE '%$value%'")
                ->orwhereRaw("LOWER(no_hp) LIKE '%$value%'");;
        })->orderBy('id', 'ASC');
        $input['usedAction'] = ['edit', 'delete'];
        $attributes = ['relGroup'];
        return Define::fetch($input, $constructor, $attributes);
    }

    public function create(Request $request)
    {
        $response['render'] = view('contents.database.pekerja.form')->render();
        return $response;
    }

    public function edit($id, Request $request)
    {
        $data = Pekerja::where('id', $id)->first();
        $response['selected'] = [
            'select_group' => [
                'id' => $data->id_group,
                'text' => $data->relGroup()->value('name')
            ]
        ];
        $response['render'] = view('contents.database.pekerja.form', compact('data', 'id'))->render();
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
