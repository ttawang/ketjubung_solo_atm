<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $menuAssets = menuAssets('', 'dashboard', []);
        return view('contents.dashboard', compact('menuAssets'));
    }
}
