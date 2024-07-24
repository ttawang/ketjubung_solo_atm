<?php

namespace App\Http\Controllers\Database;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Warna;
use App\Helpers\Define;

class WarnaController extends Controller
{
    private static $model = 'Warna';
    public function index(Request $request)
    {
        $input = $request->all();
        $breadcumbs = [['nama' => 'Database', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Warna', 'link' => route('database.warna.index'), 'active' => 'active']];
        $menuAssets = menuAssets('', 'warna', $breadcumbs, true);
        if (!$request->ajax()) return view('contents.database.warna.index', compact('menuAssets'));
        $search = strtolower($request['search']['value']);
        $constructor = Warna::when($search, function ($query, $value) {
            return $query->whereRaw("LOWER(name) LIKE '%$value%'");
        })->orderBy('created_at', 'DESC');
        $input['usedAction'] = ['edit', 'delete'];
        return Define::fetch($input, $constructor);
    }
    
    public function create(){
        
        $response['render'] = view('contents.database.warna.form')->render();
        return $response;
    }
    public function edit($id){
        $data = Warna::where('id', $id)->first();
        $response['render'] = view('contents.database.warna.form', compact('data', 'id'))->render();
        return $response;
    }
    public function store(Request $request)
    {
        $input = $request->all()['input'];
        return Define::store($input, self::$model);
    }
    public function update(Request $request, $id){
        $input = $request->all()['input'];
        return Define::update($input, self::$model, $id);
    }
    public function destroy($id){
        return Define::delete($id, self::$model);
    }
}
