<?php

namespace App\Http\Controllers\Database;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Motif;
use App\Helpers\Define;

class MotifController extends Controller
{
    private static $model = 'Motif';
    public function index(Request $request)
    {
        $input = $request->all();
        $breadcumbs = [['nama' => 'Database', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Motif', 'link' => route('database.motif.index'), 'active' => 'active']];
        $menuAssets = menuAssets('', 'motif', $breadcumbs, true);
        if (!$request->ajax()) return view('contents.database.motif.index', compact('menuAssets'));
        $search = strtolower($request['search']['value']);
        $constructor = Motif::when($search, function ($query, $value) {
            return $query->whereRaw("LOWER(name) LIKE '%$value%'")
            ->orwhereRaw("LOWER(alias) LIKE '%$value%'");
        })->orderBy('created_at', 'DESC');
        $input['usedAction'] = ['edit', 'delete'];
        return Define::fetch($input, $constructor);
        // return view('contents.database.motif.index');
    }
    
    public function create(){
        
        $response['render'] = view('contents.database.motif.form')->render();
        return $response;
    }
    public function edit($id){
        $data = Motif::where('id', $id)->first();
        $response['render'] = view('contents.database.motif.form', compact('data', 'id'))->render();
        return $response;
    }
    public function store(Request $request)
    {
        $input = $request->all();
        return Define::store($input, self::$model);
    }
    public function update(Request $request, $id){
        $input = $request->except(['_method']);
        return Define::update($input, self::$model, $id);
    }
    public function destroy($id){
        return Define::delete($id, self::$model);
    }
}
