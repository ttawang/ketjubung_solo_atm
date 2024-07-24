<?php

namespace App\Http\Controllers\Database;

use App\Helpers\Define;
use App\Http\Controllers\Controller;
use App\Models\Motif;
use App\Models\ResepChemicalFinishing;
use App\Models\ResepChemicalFinishingDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResepChemicalFinishingController extends Controller
{
    private static $model = 'ResepChemicalFinishing';
    private static $modelDetail = 'ResepChemicalFinishingDetail';

    public function index(Request $request)
    {
        $input = $request->all();
        $breadcumbs = [['nama' => 'Database', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Resep Chemical Finishing', 'link' => route('database.resep_chemical_finishing.index'), 'active' => 'active']];
        $menuAssets = menuAssets('', 'Resep Chemical Finishing', $breadcumbs, true);
        if (!$request->ajax()) return view('contents.database.resep_chemical_finishing.index', compact('menuAssets'));
        $search = strtolower($request['search']['value']);
        $constructor = ResepChemicalFinishing::when($search, function ($query, $value) {
            return $query->whereRaw("LOWER(name) LIKE '%$value%'");
        })->orderBy('created_at', 'DESC');
        $attributes = ['relBarang', 'relMotifArray'];
        return Define::fetch($input, $constructor, $attributes);
    }

    public function show($id, Request $request)
    {
        $input = $request->all();
        $input['isDetail'] = 'true';
        $input['usedAction'] = ['edit', 'delete'];
        $search = strtolower($request['search']['value']);
        $constructor = ResepChemicalFinishingDetail::when($search, function ($query, $value) {
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
            $data = ResepChemicalFinishing::where('id', $idParent)->first();
            $response['render'] = view('contents.database.resep_chemical_finishing.form-detail', compact('data', 'idParent'))->render();
        } else {
            $response['render'] = view('contents.database.resep_chemical_finishing.form')->render();
        }
        return $response;
    }

    public function edit($id, Request $request)
    {
        if ($request['isDetail'] == 'true') {
            $data = ResepChemicalFinishingDetail::where('id', $id)->first();
            // $motif = Motif::whereIn('id', $data->id_motif);
            $idParent = $data->id_resep;
            $response['selected'] = [
                'select_barang' => [
                    'id'            => $data->id_barang,
                    'text'          => $data->relBarang()->value('name'),
                ],
            ];
            $response['render'] = view('contents.database.resep_chemical_finishing.form-detail', compact('data', 'idParent', 'id'))->render();
        } else {
            $data = ResepChemicalFinishing::where('id', $id)->first();
            $response['selected'] = [
                'select_barang' => [
                    'id'            => $data->id_barang,
                    'text'          => $data->relBarang()->value('name'),
                ],
            ];

            $selectedMotif = [];
            $arrMotif = json_decode($data->id_motif, true);
            $motif = DB::table('tbl_motif')->whereIn('id', $arrMotif)->get();
            foreach ($motif as $key => $value) {
                $selectedMotif['select_motif'][] = [
                    'id'            => $value->id,
                    'text'          => $value->alias,
                ];
            }

            $response['selected'] = array_merge($response['selected'], $selectedMotif);
            $response['render'] = view('contents.database.resep_chemical_finishing.form', compact('data', 'id'))->render();
        }
        return $response;
    }

    public function store(Request $request)
    {
        $input = $request->all()['input'];
        $isDetail = $request->isDetail == 'true';
        $usingModel = $isDetail ?  self::$modelDetail : self::$model;
        if (!$isDetail) $input['id_motif'] = json_encode($input['id_motif']);
        return Define::store($input, $usingModel);
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
