<?php

namespace App\Http\Controllers\Database;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Helpers\Define;

class SupplierController extends Controller
{
    private static $model = 'Supplier';
    public function index(Request $request)
    {
        $input = $request->all();
        $breadcumbs = [['nama' => 'Database', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Supplier', 'link' => route('database.supplier.index'), 'active' => 'active']];
        $menuAssets = menuAssets('', 'supplier', $breadcumbs, true);
        if (!$request->ajax()) return view('contents.database.supplier.index', compact('menuAssets'));
        $search = strtolower($request['search']['value']);
        $constructor = Supplier::when($search, function ($query, $value) {
            return $query->whereRaw("LOWER(name) LIKE '%$value%'");
        })->orderBy('created_at', 'DESC');
        $input['usedAction'] = ['edit', 'delete'];
        return Define::fetch($input, $constructor);
    }
    
    public function create(){
        
        $response['render'] = view('contents.database.supplier.form')->render();
        return $response;
    }
    public function edit($id){
        $data = Supplier::where('id', $id)->first();
        $response['render'] = view('contents.database.supplier.form', compact('data', 'id'))->render();
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
