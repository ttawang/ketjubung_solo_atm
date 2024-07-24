<?php

namespace App\Http\Controllers\Database;

use App\Helpers\Define;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    private static $model = 'Group';
    private static $modelDetail = 'GroupDetail';
    public function index(Request $request)
    {
        $search = strtolower($request['search']['value']);
        $constructor = Group::when($search, function ($query, $value) {
            return $query->whereRaw("LOWER(name) LIKE '%$value%'");
        })->orderBy('created_at', 'DESC');
        $input = $request->all();
        $input['usedAction'] = 'NOUSED';
        $input['btnExtras'] = ['<button type="button" onclick="editForm(%id, false, $(this));" data-route="' . route('database.group.edit', ['%id']) . '" class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic">
                <i class="icon md-edit mr-2"></i>
            </button>'];
        // </button><button type="button" onclick="deleteForm(%id, false, $(this));" data-route="' . route('database.group.destroy', ['%id']) . '" class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic">
        //     <i class="icon md-delete mr-2"></i>
        // </button>
        return Define::fetch($input, $constructor);
    }

    public function show($id, Request $request)
    {
        $input = $request->all();
        $input['isDetail'] = 'true';
        $input['usedAction'] = 'NOUSED';
        $input['btnExtras'] = ['<button type="button" onclick="editForm(%id, true, $(this));" data-route="' . route('database.group.edit', ['%id']) . '" class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic">
                <i class="icon md-edit mr-2"></i>
            </button></button><button type="button" onclick="deleteForm(%id, true, $(this));" data-route="' . route('database.group.destroy', ['%id']) . '" class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic">
            <i class="icon md-delete mr-2"></i>
        </button>'];
        $search = strtolower($request['search']['value']);
        $constructor = GroupDetail::when($search, function ($query, $value) {
            return $query->whereHas('relPekerja', function ($query) use ($value) {
                return $query->whereRaw("LOWER(name) LIKE '%$value%'");
            });
        })
            ->where(['id_group' => $id])
            ->orderBy('created_at', 'DESC');
        $attributes = ['relPekerja'];
        return Define::fetch($input, $constructor, $attributes);
    }

    public function create(Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $idParent = $request['id'];
            $data = Group::where('id', $idParent)->first();
            $response['render'] = view('contents.database.pekerja.group.form-detail', compact('data', 'idParent'))->render();
        } else {
            $response['render'] = view('contents.database.pekerja.group.form')->render();
        }
        return $response;
    }

    public function edit($id, Request $request)
    {

        if ($request['isDetail'] == 'true') {
            $data = GroupDetail::where('id', $id)->first();
            $idParent = $data->id_group;
            $response['selected'] = [
                'select_pekerja' => [
                    'id'            => $data->id_pekerja,
                    'text'          => $data->relPekerja()->value('name'),
                ]
            ];
            $response['render'] = view('contents.database.pekerja.group.form-detail', compact('data', 'idParent', 'id'))->render();
        } else {
            $data = Group::where('id', $id)->first();
            $response['render'] = view('contents.database.pekerja.group.form', compact('data', 'id'))->render();
        }
        return $response;
    }

    public function store(Request $request)
    {
        $input = $request->all()['input'];
        $isDetail = $request->isDetail == 'true';
        $usingModel = $isDetail ?  self::$modelDetail : self::$model;
        DB::beginTransaction();
        if ($isDetail) {
            try {
                $item = [];
                foreach ($input['id_pekerja'] as $key => $value) {
                    $item[$key]['id_group']   = $input['id_group'];
                    $item[$key]['id_pekerja'] = $value;
                }
                GroupDetail::insert($item);
                DB::commit();
                return response('Data Successfully Saved!', 200);
            } catch (\Throwable $th) {
                DB::rollBack();
                return response($th->getMessage(), 401);
            }
        } else {
            return Define::store($input, $usingModel);
        }
    }

    public function update($id, Request $request)
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
