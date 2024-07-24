<?php

namespace App\Models;

use App\Helpers\Date;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class DyeingJasaLuarDetail extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_dyeing_jasa_luar_detail';
    protected $guarded = [];
    protected $appends = ['tanggal_custom', 'nama_barang', 'validated_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function getNamaBarangAttribute()
    {
        return $this->relBarang()->value('name');
    }

    public function getValidatedAtAttribute()
    {
        return $this->relDyeingJasaLuar()->value('validated_at');
    }

    public function getTanggalCustomAttribute()
    {
        return Date::format($this->tanggal, 98);
    }

    public function relDyeingJasaLuar()
    {
        return $this->belongsTo(DyeingJasaLuar::class, 'id_dyeing_jasa_luar', 'id');
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
}
