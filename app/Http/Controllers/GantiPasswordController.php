<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GantiPasswordController extends Controller
{
    public function change(Request $request)
    {
        try {
            $idUser = Auth::id();
            $input['password'] = Hash::make($request['password']);
            DB::table('users')->where('id', $idUser)->update($input);
            return response('Password Berhasil Diperbarui!', 200);
        } catch (\Throwable $th) {
            return response($th->getMessage(), 401);
        }
    }
}
