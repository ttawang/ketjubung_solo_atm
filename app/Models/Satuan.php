<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Satuan extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_satuan';
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
        $logStokPenerimaan = $this->relLogStokPenerimaanBarang->setAppends([])->count();
        return $logStokPenerimaan;
    }

    public function relLogStokPenerimaanBarang()
    {
        return $this->hasMany(LogStokPenerimaan::class, 'id_satuan_1', 'id');
    }
}
