<?php

namespace App\Models;

use App\Helpers\Date;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Znck\Eloquent\Traits\BelongsToThrough;

class PenomoranBeamRetur extends Model
{
    use SoftDeletes, BelongsToThrough;
    protected $table = 'tbl_beam_retur';
    protected $guarded = [];
    protected $appends = ['tanggal_custom', 'nama_barang'];

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

    public function getNamaBarangAttribute()
    {
        $txtWarna = $txtMotif = $txtNoKikw = $txtMesin = '';
        if ($this->id_warna != null) $txtWarna = ' | ' . $this->relWarna()->value('alias');
        if ($this->id_motif != null) $txtMotif = ' | ' . $this->relMotif()->value('alias');
        if ($this->id_beam != null) $txtNoKikw = $this->throughNomorKikw()->value('name') . ' | ';
        if ($this->id_mesin != null) $txtMesin = $this->relMesin()->value('name') . ' | ';
        return $txtNoKikw . '' . $txtMesin . '' . $this->relBarang()->value('name') . $txtWarna . '' . $txtMotif;
    }

    public function getNamaBarangBaruAttribute()
    {
        return $this->relPenomoranBeamDetail->nama_barang;
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

    public function relGroup()
    {
        return $this->belongsTo(Group::class, 'id_group', 'id');
    }

    public function relPekerja()
    {
        return $this->belongsTo(Pekerja::class, 'id_pekerja', 'id');
    }

    public function relBeam()
    {
        return $this->belongsTo(Beam::class, 'id_beam', 'id');
    }

    public function relMesin()
    {
        return $this->belongsTo(Mesin::class, 'id_mesin', 'id');
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

    public function relPenomoranBeamReturDetail()
    {
        return $this->belongsTo(PenomoranBeamRetur::class, 'id_beam_retur', 'id');
    }
}
