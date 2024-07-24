<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class FinishingCabutDetail extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_finishing_cabut_detail';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }
    
    public function relFinishingCabut()
    {
        return $this->belongsTo(FinishingCabut::class, 'id_finishing_cabut', 'id');
    }

    public function relBarang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id');
    }

    public function relWarna()
    {
        return $this->belongsTo(Warna::class, 'id_warna', 'id');
    }
    public function relGudang()
    {
        return $this->belongsTo(Gudang::class, 'id_gudang', 'id');
    }
    public function relMesin()
    {
        return $this->belongsTo(Mesin::class, 'id_mesin', 'id');
    }
    public function relBeam()
    {
        return $this->belongsTo(Beam::class, 'id_beam', 'id');
    }
    public function relSongket()
    {
        return $this->belongsTo(Beam::class, 'id_songket', 'id');
    }
    public function relMotif()
    {
        return $this->belongsTo(Motif::class, 'id_motif', 'id');
    }
    public function relGrade()
    {
        return $this->belongsTo(Kualitas::class, 'id_grade', 'id');
    }
    public function relKualitas()
    {
        return $this->belongsTo(MappingKualitas::class, 'id_kualitas', 'id');
    }
    public function relSatuan1()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan_1', 'id');
    }
    
    public function relSatuan2()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan_2', 'id');
    }
}
