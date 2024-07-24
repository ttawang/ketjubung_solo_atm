<?php

namespace App\Models;

use App\Helpers\Date;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Znck\Eloquent\Traits\BelongsToThrough;

class SaldoAwal extends Model
{
    use SoftDeletes, BelongsToThrough;
    protected $table = 'tbl_saldoawal';
    protected $guarded = [];
    protected $appends = ['tanggal_custom'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function getTanggalCustomAttribute()
    {
        return Date::format($this->tanggal, 98);
    }

    public function relBarang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id');
    }

    public function relSatuan1()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan_1', 'id');
    }

    public function relSatuan2()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan_2', 'id');
    }

    public function relWarna()
    {
        return $this->belongsTo(Warna::class, 'id_warna', 'id');
    }

    public function relMotif()
    {
        return $this->belongsTo(Motif::class, 'id_motif', 'id');
    }

    public function relGudang()
    {
        return $this->belongsTo(Gudang::class, 'id_gudang', 'id');
    }

    public function relGrade()
    {
        return $this->belongsTo(Kualitas::class, 'id_grade', 'id');
    }

    public function relKualitas()
    {
        return $this->belongsTo(MappingKualitas::class, 'id_kualitas', 'id');
    }

    public function relMesin()
    {
        return $this->belongsTo(Mesin::class, 'id_mesin', 'id');
    }

    public function relBeam()
    {
        return $this->belongsTo(Beam::class, 'id_beam', 'id');
    }

    public function throughNomorBeam()
    {
        return $this->belongsToThrough(
            NomorBeam::class,
            Beam::class,
            null,
            '',
            [Beam::class => 'id_beam', NomorBeam::class => 'id_nomor_beam']
        );
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

    public function relMesinHistoryLatest()
    {
        return $this->hasOne(MesinHistory::class, 'id_beam', 'id_beam')->latestOfMany();
    }

    public function relProductionCode()
    {
        return $this->belongsTo(ProductionCode::class, 'code', 'code');
    }
}
