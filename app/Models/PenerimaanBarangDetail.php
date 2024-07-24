<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class PenerimaanBarangDetail extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_penerimaan_barang_detail';
    protected $guarded = [];
    protected $appends = ['nama_barang', 'validated_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function getValidatedAtAttribute()
    {
        return $this->relPenerimaanBarang()->value('validated_at');
    }

    public function getNamaBarangAttribute()
    {
        $txtWarna = '';
        if ($this->id_warna != null) {
            $txtWarna = ' | ' . $this->relWarna()->value('name');
        }
        return $this->relBarang()->value('name') . '' . $txtWarna;
    }

    public function relPenerimaanBarang()
    {
        return $this->belongsTo(PenerimaanBarang::class, 'id_penerimaan_barang', 'id');
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

    public function relGudang()
    {
        return $this->belongsTo(Gudang::class, 'id_gudang', 'id');
    }

    public function relWarna()
    {
        return $this->belongsTo(Warna::class, 'id_warna', 'id');
    }
}
