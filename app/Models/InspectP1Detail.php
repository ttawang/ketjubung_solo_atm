<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Znck\Eloquent\Traits\BelongsToThrough;

class InspectP1Detail extends Model
{
    use SoftDeletes, BelongsToThrough;
    protected $table = 'tbl_inspect_p1_detail';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function getNomorAttribute()
    {
        return $this->relP1()->value('nomor');
    }

    public function relInspectP1()
    {
        return $this->belongsTo(InspectP1::class, 'id_inspect_p1', 'id');
    }
    public function relP1()
    {
        return $this->belongsTo(P1::class, 'id_p1', 'id');
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
    public function relInspectP1Kualitas()
    {
        return $this->hasMany(InspectP1Kualitas::class, 'id_inspect_p1_detail', 'id');
    }
    public function relSatuan1()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan_1', 'id');
    }

    public function relSatuan2()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan_2', 'id');
    }

    public function relBeam()
    {
        return $this->belongsTo(Beam::class, 'id_beam', 'id');
    }
    public function relSongket()
    {
        return $this->belongsTo(Beam::class, 'id_songket', 'id');
    }
    public function relMesin()
    {
        return $this->belongsTo(Mesin::class, 'id_mesin', 'id');
    }

    public function relReturInspect()
    {
        return $this->hasOne(P1Detail::class, 'id_inspect_retur', 'id')->where('code', 'JS');
    }

    public function throughNomorKikw()
    {
        return $this->belongsToThrough(
            NomorKikw::class,
            Beam::class,
            null,
            '',
            [Beam::class => 'id_beam', NomorKikw::class => 'id_nomor_kikw']
        );
    }
}
