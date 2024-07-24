<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('guest')->except('logout');
    }

    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $loginType = filter_var($request['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        $login = [
            $loginType => $request['email'],
            'password' => $request['password'],
        ];

        if (!Auth::attempt($login)) return response('Gagal Login, Email atau Password salah!', 401);
        return response(['message' => 'Berhasil Login!', 'route' => route("dashboard")], 200);
    }

    public function logout(Request $request)
    {
        Auth::guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('login')->with('message', 'Behasil Logout!');
    }
}
