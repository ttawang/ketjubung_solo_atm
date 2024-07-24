<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Barang extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_barang';
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
        return $this->relLogStokPenerimaan->setAppends([])->count();
    }

    public function relTipe()
    {
        return $this->belongsTo(Tipe::class, 'id_tipe', 'id');
    }

    public function relLogStokPenerimaan()
    {
        return $this->hasMany(LogStokPenerimaan::class, 'id_barang', 'id');
    }

    public function relPengirimanDetail()
    {
        return $this->hasMany(PengirimanBarangDetail::class, 'id_barang', 'id');
    }

    public function relPenerimaanBarangDetail()
    {
        return $this->hasMany(PenerimaanBarangDetail::class, 'id_barang', 'id');
    }

    public function relPenerimaanChemicalDetail()
    {
        return $this->hasMany(PenerimaanChemicalDetail::class, 'id_barang', 'id');
    }

    public function relResepDetail()
    {
        return $this->hasMany(ResepDetail::class, 'id_barang', 'id');
    }
}
