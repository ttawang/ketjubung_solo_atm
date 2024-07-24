<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Tipe extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_tipe';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function getCountDetailAttribute()
    {
        return $this->relBarang->setAppends([])->count();
    }

    public function relBarang()
    {
        return $this->hasMany(Barang::class, 'id_tipe', 'id');
    }
}
