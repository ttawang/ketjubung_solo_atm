<?php

namespace App\Models;

use App\Helpers\Date;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class DyeingGresikDetail extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_dyeing_gresik_detail';
    protected $guarded = [];
    protected $appends = ['tanggal_custom', 'nama_barang', 'jenis_benang', 'nama_satuan_1', 'nama_satuan_2', 'nama_warna', 'validated_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function getValidatedAtAttribute()
    {
        return $this->relDyeingGresik()->value('validated_at');
    }

    public function getTanggalCustomAttribute()
    {
        return Date::format($this->tanggal, 98);
    }

    public function getNamaBarangAttribute()
    {
        $txtWarna = '';
        if ($this->id_warna != null) {
            $txtWarna = ' | ' . $this->relWarna()->value('alias');
        }
        return $this->relBarang()->value('name') . '' . $txtWarna;
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

    public function getJenisBenangAttribute()
    {
        if ($this->status == 'SOFTCONE' || $this->status == 'DYEOVEN') {
            $txtWarna = '';
            $volume1 = $volume2 = 0;
            if ($this->id_warna != null) $txtWarna = ' | ' . $this->relWarna()->value('alias');

            if ($this->volume_1 != null) $volume1 = $this->volume_1;
            if ($this->volume_2 != null) $volume2 = $this->volume_2;
            if ($this->stok_utama != null) $volume1 = $this->stok_utama;
            if ($this->stok_pilihan != null) $volume2 = $this->stok_pilihan;

            $txtVolume = " | {$volume1} cones/{$volume2} kg";
            return $this->key . '. ' . $this->tanggal_custom . ' | ' . $this->relBarang()->value('name') . '' . $txtWarna . '' . $txtVolume;
        } else {
            return $this->relBarang()->value('name');
        }
    }

    public function relDyeingGresik()
    {
        return $this->belongsTo(DyeingGresik::class, 'id_dyeing_gresik', 'id');
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

    public function relDyeingWarna()
    {
        return $this->hasMany(DyeingWarna::class, 'id_dyeing_detail', 'id');
    }

    public function relGudang()
    {
        return $this->belongsTo(Gudang::class, 'id_gudang', 'id');
    }

    public function relMesin()
    {
        return $this->belongsTo(Mesin::class, 'id_mesin', 'id');
    }
}
