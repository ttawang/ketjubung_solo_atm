<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class DyeingGresikWarna extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_dyeing_gresik_warna';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function relWarna()
    {
        return $this->belongsTo(Warna::class, 'id_warna', 'id');
    }

    public function relBarang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id');
    }

    public function relSatuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan', 'id');
    }

    public function relDyeingGresikDetail()
    {
        return $this->belongsTo(DyeingDetail::class, 'id_dyeing_gresik_detail', 'id');
    }
}
