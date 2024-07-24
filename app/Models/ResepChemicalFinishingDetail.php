<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ResepChemicalFinishingDetail extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_resep_chemical_finishing_detail';
    protected $guarded = [];
    protected $appends = ['satuan'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function getSatuanAttribute()
    {
        return $this->relSatuan()->value('name');
    }

    public function relBarang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id');
    }

    public function relSatuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan', 'id');
    }
}
