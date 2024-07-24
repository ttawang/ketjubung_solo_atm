<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Znck\Eloquent\Traits\BelongsToThrough;

class PengirimanSarungDetail extends Model
{
    use SoftDeletes, BelongsToThrough;
    protected $table = 'tbl_pengiriman_sarung_detail';
    protected $guarded = [];
    protected $appends = ['nama_barang'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function getNamaBarangAttribute()
    {
        $txtMesin = $txtWarna = $txtMotif = $txtGrade = $txtKualitas = $txtNoKikw = '';
        if ($this->id_beam != null) $txtNoKikw = $this->no_kikw . ' | ';
        if ($this->id_mesin != null) $txtMesin = $this->nama_mesin . ' | ';
        if ($this->id_warna != null) $txtWarna = ' | ' . $this->relWarna()->value('alias');
        if ($this->id_motif != null) $txtMotif = ' | ' . $this->relMotif()->value('alias');
        if ($this->id_grade != null) $txtGrade = ' | ' . $this->relGrade()->value('grade');
        if ($this->id_kualitas != null) $txtKualitas = ' | ' . $this->relKualitas()->value('kode');
        return $txtNoKikw . '' . $txtMesin . '' . $this->relBarang()->value('name') . '' . $txtWarna . '' . $txtMotif . '' . $txtGrade . '' . $txtKualitas;
    }

    public function relPengirimanSarung()
    {
        return $this->belongsTo(PengirimanSarung::class, 'id_pengiriman_sarung', 'id');
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
    public function throughNomorKiks()
    {
        return $this->belongsToThrough(
            NomorKikw::class,
            Beam::class,
            null,
            '',
            [Beam::class => 'id_songket', NomorKikw::class => 'id_nomor_kikw']
        );
    }
}
