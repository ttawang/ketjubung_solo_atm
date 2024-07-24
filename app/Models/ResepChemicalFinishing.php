<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ResepChemicalFinishing extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_resep_chemical_finishing';
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
        return $this->relResepDetail->setAppends([])->count();
    }

    public function relResepDetail()
    {
        return $this->hasMany(ResepChemicalFinishingDetail::class, 'id_resep', 'id');
    }

    public function relBarang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id');
    }

    public function relMotif()
    {
        return $this->belongsTo(Motif::class, 'id_motif', 'id');
    }
}
