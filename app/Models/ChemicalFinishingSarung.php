<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ChemicalFinishingSarung extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_chemical_finishing_sarung';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function relBarang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id');
    }

    public function relMotif()
    {
        return $this->belongsTo(Barang::class, 'id_motif', 'id');
    }
}
