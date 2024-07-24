<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Znck\Eloquent\Traits\BelongsToThrough;

class PengirimanBarangDetail extends Model
{
    use SoftDeletes, BelongsToThrough;
    protected $table = 'tbl_pengiriman_barang_detail';
    protected $guarded = [];
    protected $appends = ['nama_barang', 'nama_barang_terima', 'nama_satuan_1', 'nama_satuan_2', 'nama_warna', 'nama_motif', 'no_beam', 'nama_mesin', 'no_kikw', 'no_kiks', 'is_sizing', 'nama_grade', 'nama_kualitas', 'validated_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function getValidatedAtAttribute()
    {
        return $this->relPengirimanBarang()->value('validated_at');
    }

    public function getNamaSatuan1Attribute()
    {
        return $this->relSatuan1()->value('name');
    }

    public function getNamaSatuan2Attribute()
    {
        return $this->relSatuan2()->value('name');
    }

    public function getNamaWarnaAttribute()
    {
        return $this->relWarna()->value('name');
    }

    public function getNamaMotifAttribute()
    {
        return $this->relMotif()->value('name');
    }

    public function getNoKikwAttribute()
    {
        return $this->throughNomorKikw()->value('name');
    }
    public function getNoKiksAttribute()
    {
        return $this->throughNomorKiks()->value('name');
    }

    public function getNoBeamAttribute()
    {
        return $this->throughNomorBeam()->value('name');
    }

    public function getNamaGradeAttribute()
    {
        return $this->relGrade()->value('grade') . ' - ' . $this->relGrade()->value('alias');
    }

    public function getNamaKualitasAttribute()
    {
        return $this->relKualitas()->value('kode') . ' - ' . $this->relKualitas()->value('name');
    }

    public function getNamaMesinAttribute()
    {
        return $this->relMesin()->value('name');
    }
    public function getTanggalPotongTextAttribute()
    {
        $tanggalPotong = $this->attributes['tanggal_potong'];
        return date('d-m-Y', strtotime($tanggalPotong));
    }

    public function getNamaBarangTerimaAttribute()
    {
        $txtMesin = $txtWarna = $txtMotif = $txtGrade = $txtKualitas = $txtNoKikw = $txtNoKiks = $txtTanggalPotong = '';
        $txtGudang = $this->relGudang()->value('name');
        if ($this->id_beam != null) $txtNoKikw = $this->no_kikw . ' | ';
        if ($this->id_songket != null) $txtNoKiks = $this->no_kiks . ' | ';
        if ($this->id_mesin != null) $txtMesin = $this->nama_mesin . ' | ';
        if ($this->id_warna != null) $txtWarna = ' | ' . $this->relWarna()->value('alias');
        if ($this->id_motif != null) $txtMotif = ' | ' . $this->relMotif()->value('alias');
        if ($this->id_grade != null) $txtGrade = ' | ' . $this->relGrade()->value('grade');
        if ($this->id_kualitas != null) $txtKualitas = ' | ' . $this->relKualitas()->value('kode');
        if ($this->tanggal_potong != null) $txtTanggalPotong = ' | ' . $this->tanggal_potong_text;
        return $txtNoKikw . '' . $txtNoKiks . '' . $txtMesin . '' . $this->relBarang()->value('name') . '' . $txtWarna . '' . $txtMotif . '' . $txtGrade . '' . $txtKualitas . ' | ' . $txtGudang . '' . $txtTanggalPotong;
    }

    public function getNamaBarangAttribute()
    {
        $txtMesin = $txtWarna = $txtMotif = $txtGrade = $txtKualitas = $txtNoKikw = $txtNoKiks = $txtTanggalPotong = '';
        if ($this->id_beam != null) $txtNoKikw = $this->no_kikw . ' | ';
        if ($this->id_songket != null) $txtNoKiks = $this->no_kiks . ' | ';
        if ($this->id_mesin != null) $txtMesin = $this->nama_mesin . ' | ';
        if ($this->id_warna != null) $txtWarna = ' | ' . $this->relWarna()->value('alias');
        if ($this->id_motif != null) $txtMotif = ' | ' . $this->relMotif()->value('alias');
        if ($this->id_grade != null) $txtGrade = ' | ' . $this->relGrade()->value('grade');
        if ($this->id_kualitas != null) $txtKualitas = ' | ' . $this->relKualitas()->value('kode');
        if ($this->tanggal_potong != null) $txtTanggalPotong = ' | ' . $this->tanggal_potong_text;
        return $txtNoKikw . '' . $txtNoKiks . '' . $txtMesin . '' . $this->relBarang()->value('name') . '' . $txtWarna . '' . $txtMotif . '' . $txtGrade . '' . $txtKualitas . '' . $txtTanggalPotong;
    }

    public function getIsSizingAttribute()
    {
        return $this->relBeam()->value('is_sizing');
    }

    public function relPengirimanBarang()
    {
        return $this->belongsTo(PengirimanBarang::class, 'id_pengiriman_barang', 'id');
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

    public function relGudang()
    {
        return $this->belongsTo(Gudang::class, 'id_gudang', 'id');
    }

    public function relMotif()
    {
        return $this->belongsTo(Motif::class, 'id_motif', 'id');
    }

    public function relWarpingDetail()
    {
        return $this->belongsTo(WarpingDetail::class, 'id_warping_detail', 'id');
    }

    public function relBeam()
    {
        return $this->belongsTo(Beam::class, 'id_beam', 'id');
    }
    public function relSongket()
    {
        return $this->belongsTo(Beam::class, 'id_songket', 'id');
    }

    public function relGrade()
    {
        return $this->belongsTo(Kualitas::class, 'id_grade', 'id');
    }

    public function relKualitas()
    {
        return $this->belongsTo(MappingKualitas::class, 'id_kualitas', 'id');
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

    public function throughGudangTujuan()
    {
        return $this->belongsToThrough(
            Gudang::class,
            PengirimanBarang::class,
            null,
            '',
            [PengirimanBarang::class => 'id_pengiriman_barang', Gudang::class => 'id_gudang_tujuan']
        );
    }

    public function throughTipePengiriman()
    {
        return $this->belongsToThrough(
            TipePengiriman::class,
            PengirimanBarang::class,
            null,
            '',
            [PengirimanBarang::class => 'id_pengiriman_barang', TipePengiriman::class => 'id_tipe_pengiriman']
        );
    }

    public function relMesin()
    {
        return $this->belongsTo(Mesin::class, 'id_mesin', 'id');
    }

    public function relDetailTujuan()
    {
        return $this->hasMany(PengirimanBarangDetail::class, 'id_parent_detail', 'id');
    }
}
