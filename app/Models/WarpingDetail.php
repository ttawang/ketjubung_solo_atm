<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class WarpingDetail extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_warping_detail';
    protected $appends = ['nama_barang', 'nama_satuan_1', 'nama_satuan_2', 'nama_warna', 'nama_motif', 'mesin'];
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
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

    public function getMesinAttribute()
    {
        return $this->relMesin()->value('name');
    }

    public function getNamaBarangAttribute()
    {
        $txtWarna = $txtMotif = '';
        if ($this->id_warna != null) {
            $txtWarna = ' | ' . $this->relWarna()->value('alias');
        }
        if ($this->id_motif != null) {
            $txtMotif = ' | ' . $this->relMotif()->value('alias');
        }
        return $this->relBarang()->value('name') . '' . $txtWarna . '' . $txtMotif;
    }

    public function relWarping()
    {
        return $this->belongsTo(Warping::class, 'id_warping', 'id');
    }

    public function relBarang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id');
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

    public function relWarna()
    {
        return $this->belongsTo(Warna::class, 'id_warna', 'id');
    }
    public function relMotif()
    {
        return $this->belongsTo(Motif::class, 'id_motif', 'id');
    }
    public function relMesin()
    {
        return $this->belongsTo(Mesin::class, 'id_mesin', 'id');
    }
    public function relLogPenerimaan()
    {
        return $this->belongsTo(LogStokPenerimaan::class, 'id_log_stok_penerimaan');
    }
    public function relBeam()
    {
        return $this->belongsTo(Beam::class, 'id_beam', 'id');
    }
}
