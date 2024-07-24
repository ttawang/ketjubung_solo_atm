<?php

namespace App\Http\Controllers\Management;

use App\Helpers\Define;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $breadcumbs = [['nama' => 'Management', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Menu', 'link' => route('management.menu.index'), 'active' => 'active']];
        $menuAssets = menuAssets('', 'menu', $breadcumbs, true);
        if (!$request->ajax()) return view('contents.management.menu.index', compact('menuAssets'));
        $search = $request['search']['value'];
        $constructor = Menu::with(['relParent'])->when($search, function ($query, $value) {
            return $query->where('name', 'LIKE', "%$value%");
        })->orderBy('created_at', 'DESC');
        $attribute = ['rel_parent.name'];
        return Define::fetch($request, $constructor, $attribute);
    }
}
