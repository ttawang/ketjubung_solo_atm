<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Znck\Eloquent\Traits\BelongsToThrough;

class SizingDetail extends Model
{
    use SoftDeletes, BelongsToThrough;
    protected $table = 'tbl_sizing_detail';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function relSizing()
    {
        return $this->belongsTo(Sizing::class, 'id_sizing', 'id');
    }

    public function relSizingDetailParent()
    {
        return $this->belongsTo(SizingDetail::class, 'id_parent', 'id');
    }

    public function relSizingParent()
    {
        return $this->hasMany(SizingDetail::class, 'id_parent', 'id');
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

    public function relLogStokPenerimaan()
    {
        return $this->belongsTo(LogStokPenerimaan::class, 'id_log_stok_penerimaan', 'id');
    }

    public function relLogStokPenerimaanBL()
    {
        return $this->hasOne(LogStokPenerimaan::class, 'id_beam', 'id_beam')->where('code', 'BL')->orderBy('id', 'asc');
    }

    public function throughSizingBeam()
    {
        return $this->belongsToThrough(
            Beam::class,
            SizingDetail::class,
            null,
            '',
            [SizingDetail::class => 'id_parent', Beam::class => 'id_beam']
        );
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
}
