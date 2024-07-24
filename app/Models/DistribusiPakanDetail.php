<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Znck\Eloquent\Traits\BelongsToThrough;

class DistribusiPakanDetail extends Model
{
    use SoftDeletes, BelongsToThrough;
    protected $table = 'tbl_distribusi_pakan_detail';
    protected $guarded = [];
    protected $appends = ['nama_barang', 'validated_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function getNamaBarangAttribute()
    {
        $txtWarna = $txtGreyWarna = '';
        if ($this->code == 'BHDS') $txtGreyWarna = ' - SISA';
        if ($this->code == 'BHDG') $txtGreyWarna = ' - GREY';
        if ($this->id_warna != null) $txtWarna = ' | ' . $this->relWarna()->value('alias') . $txtGreyWarna;
        return $this->relBarang()->value('name') . '' . $txtWarna;
    }

    public function getValidatedAtAttribute()
    {
        return $this->relDistribusiPakan()->value('validated_at');
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

    public function relDistribusiPakan()
    {
        return $this->belongsTo(DistribusiPakan::class, 'id_distribusi_pakan', 'id');
    }

    public function relBeam()
    {
        return $this->belongsTo(Beam::class, 'id_beam', 'id');
    }

    public function relMesin()
    {
        return $this->belongsTo(Mesin::class, 'id_mesin', 'id');
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

    public function relTenunDetail()
    {
        return $this->belongsTo(TenunDetail::class, 'id_tenun_detail', 'id');
    }
}
