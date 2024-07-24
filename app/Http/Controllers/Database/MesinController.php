<?php

namespace App\Http\Controllers\Database;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mesin;
use App\Helpers\Define;

class MesinController extends Controller
{

    private static $model = 'Mesin';
    public function index(Request $request)
    {
        $input = $request->all();
        $breadcumbs = [['nama' => 'Database', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Mesin', 'link' => route('database.mesin.index'), 'active' => 'active']];
        $menuAssets = menuAssets('', 'mesin', $breadcumbs, true);
        if (!$request->ajax()) return view('contents.database.mesin.index', compact('menuAssets'));
        $search = strtolower($request['search']['value']);
        $constructor = Mesin::when($search, function ($query, $value) {
            return $query->whereRaw("LOWER(name) LIKE '%$value%'")
            ->orwhereRaw("LOWER(jenis) LIKE '%$value%'");
        })->orderBy('id', 'ASC');
        $input['usedAction'] = ['edit', 'delete'];
        $input['btnExtras'] = ['<button type="button" onclick="addPekerjaMesin(%id, $(this));" data-route="' . route('helper.getPekerjaMesin', ['%id']) . '" class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic">
                <i class="icon md-account-add mr-2"></i>
        </button>'];
        $attributes = ['relPekerjaMesin'];
        return Define::fetch($input, $constructor, $attributes);
    }
    public function create()
    {
        $response['render'] = view('contents.database.mesin.form')->render();
        return $response;
    }
    public function edit($id)
    {
        $data = Mesin::where('id', $id)->first();
        // $response['selected'] = [
        //     'select_jenis' => [
        //         'text' => $data->value('jenis')
        //     ]
        // ];
        $response['render'] = view('contents.database.mesin.form', compact('data', 'id'))->render();
        return $response;
    }
    public function store(Request $request)
    {
        $input = $request->all()['input'];
        return Define::store($input, self::$model);
    }
    public function update(Request $request, $id)
    {
        $input = $request->all()['input'];
        return Define::update($input, self::$model, $id);
    }
    public function destroy($id)
    {
        return Define::delete($id, self::$model);
    }
}
