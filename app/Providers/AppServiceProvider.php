<?php

namespace App\Providers;

use App\Models\Barang;
use App\Models\Satuan;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        Collection::macro('setAppends', function ($attributes) {
            return $this->map(function ($item) use ($attributes) {
                return $item->setAppends($attributes);
            });
        });

        // View::composer('contents.production.*.index', function ($view) {
        //     $response['index'] = view(str_replace('index', 'form', $view->getName()))->render();
        //     $response['detail'] = view(str_replace('index', 'form-detail', $view->getName()))->render();
        //     $view->with('form', $response);
        // });

        // View::composer(['components.*'], function ($view) {
        //     $param['suppliers'] = Supplier::orderBy('id')->get();
        //     $param['barangs']   = Barang::orderBy('id')->get();
        //     $param['satuans']   = Satuan::orderBy('id')->get();
        //     $view->with($param);
        // });
    }
}
