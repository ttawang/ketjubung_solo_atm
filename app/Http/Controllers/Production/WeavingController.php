<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WeavingController extends Controller
{
    public function index()
    {
        return redirect()->route('production.warping.index');
    }
}
