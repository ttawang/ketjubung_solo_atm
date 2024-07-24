<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ChemicalDetail extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_chemical_detail';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function getValidatedAtAttribute()
    {
        return $this->relChemical()->value('validated_at');
    }

    public function relChemical()
    {
        return $this->belongsTo(Chemical::class, 'id_chemical', 'id');
    }

    public function relGudang()
    {
        return $this->belongsTo(Gudang::class, 'id_gudang', 'id');
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
