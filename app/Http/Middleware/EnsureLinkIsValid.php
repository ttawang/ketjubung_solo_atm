<?php

namespace App\Http\Middleware;

use App\Models\MappingMenu;
use App\Models\Menu;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureLinkIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $currentRouteName =  getCurrentRoutes('index', [], true);
        // $checkingRouteName = MappingMenu::whereHas('relMenu', function ($query) use ($currentRouteName) {
        //     return $query
        //         ->where('name', $currentRouteName);
        // })->where('roles_id', Auth::user()->roles_id)->get()->count();

        $checkingRouteName = Menu::whereHas('relMapping', function ($query) {
            return $query->where('roles_id', Auth::user()->roles_id);
        })
            ->where(function ($query) use ($currentRouteName) {
                $query
                    ->where('name', $currentRouteName)
                    ->orWhere('is_main_nav', 'TIDAK');
            })->get()->count();
        if ($checkingRouteName == 0) return redirect()->route('dashboard');
        return $next($request);
    }
}
