<?php

namespace App\Http\Controllers\Database;

use App\Helpers\Define;
use App\Http\Controllers\Controller;
use App\Models\Resep;
use App\Models\ResepDetail;
use Illuminate\Http\Request;

class ResepController extends Controller
{
    private static $model = 'Resep';
    private static $modelDetail = 'ResepDetail';
    public function index(Request $request)
    {
        $search = strtolower($request['search']['value']);
        $constructor = Resep::when($search, function ($query, $value) {
            return $query->whereRaw("LOWER(name) LIKE '%$value%'");
        })->orderBy('created_at', 'DESC');
        $input = $request->all();
        $input['usedAction'] = ['detail'];
        $input['btnExtras'] = ['<button type="button" onclick="editForm(%id, false, $(this));" data-route="' . route('database.resep.edit', ['%id']) . '" class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic">
                <i class="icon md-edit mr-2"></i>
            </button><button type="button" onclick="deleteForm(%id, false, $(this));" data-route="' . route('database.resep.destroy', ['%id']) . '" class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic">
            <i class="icon md-delete mr-2"></i>
        </button>'];
        return Define::fetch($input, $constructor, []);
    }

    public function show($id, Request $request)
    {
        $input = $request->all();
        $input['isDetail'] = 'true';
        $input['usedAction'] = 'NOUSED';
        $input['btnExtras'] = ['<button type="button" onclick="editForm(%id, true, $(this));" data-route="' . route('database.resep.edit', ['%id']) . '" class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic">
                <i class="icon md-edit mr-2"></i>
            </button></button><button type="button" onclick="deleteForm(%id, true, $(this));" data-route="' . route('database.resep.destroy', ['%id']) . '" class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic">
            <i class="icon md-delete mr-2"></i>
        </button>'];
        $search = strtolower($request['search']['value']);
        $constructor = ResepDetail::when($search, function ($query, $value) {
            return $query->whereHas('relBarang', function ($query) use ($value) {
                return $query->whereRaw("LOWER(name) LIKE '%$value%'");
            });
        })
            ->where(['id_resep' => $id])
            ->orderBy('created_at', 'DESC');
        $attributes = ['relBarang'];
        return Define::fetch($input, $constructor, $attributes);
    }

    public function create(Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $idParent = $request['id'];
            $data = Resep::where('id', $idParent)->first();
            $response['render'] = view('contents.database.warna.resep.form-detail', compact('data', 'idParent'))->render();
        } else {
            $response['render'] = view('contents.database.warna.resep.form')->render();
        }
        return $response;
    }

    public function edit($id, Request $request)
    {

        if ($request['isDetail'] == 'true') {
            $data = ResepDetail::where('id', $id)->first();
            $idParent = $data->id_resep;
            $response['selected'] = [
                'select_barang' => [
                    'id'            => $data->id_barang,
                    'text'          => $data->relBarang()->value('name'),
                ],
            ];
            $response['render'] = view('contents.database.warna.resep.form-detail', compact('data', 'idParent', 'id'))->render();
        } else {
            $data = Resep::where('id', $id)->first();
            $response['selected'] = [
                'select_barang' => [
                    'id'            => $data->id_barang,
                    'text'          => $data->relBarang()->value('name'),
                ],
                'select_warna' => [
                    'id'            => $data->id_warna,
                    'text'          => $data->relWarna()->value('name'),
                ],
            ];
            $response['render'] = view('contents.database.warna.resep.form', compact('data', 'id'))->render();
        }
        return $response;
    }

    public function store(Request $request)
    {
        $input = $request->all()['input'];
        if (isset($input['volume'])) $input['volume'] = floatValue($input['volume']);
        $isDetail = $request->isDetail == 'true';
        $usingModel = $isDetail ?  self::$modelDetail : self::$model;
        return Define::store($input, $usingModel);
    }

    public function update($id, Request $request)
    {
        $input = $request->all()['input'];
        if (isset($input['volume'])) $input['volume'] = floatValue($input['volume']);
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
