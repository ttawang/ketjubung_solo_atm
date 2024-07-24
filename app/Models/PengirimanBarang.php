<?php

namespace App\Models;

use App\Helpers\Date;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class PengirimanBarang extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_pengiriman_barang';
    protected $guarded = [];
    protected $appends = ['tanggal_custom', 'nama_tipe_pengiriman', 'column_name'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function getColumnNameAttribute()
    {
        $columnName = 'Default';
        if ($this->id_tipe_pengiriman == '5' || $this->id_tipe_pengiriman == '6') $columnName = 'Beam';
        return $columnName;
    }

    public function getNamaTipePengirimanAttribute()
    {
        return $this->relTipePengiriman()->value('title') ?? $this->txt_tipe_pengiriman;
    }

    public function getTanggalCustomAttribute()
    {
        return Date::format($this->tanggal, 98);
    }

    public function relPengirimanDetail()
    {
        return $this->hasMany(PengirimanBarangDetail::class, 'id_pengiriman_barang', 'id');
    }

    public function relTipePengiriman()
    {
        return $this->belongsTo(TipePengiriman::class, 'id_tipe_pengiriman', 'id');
    }

    public function relGudangAsal()
    {
        return $this->belongsTo(Gudang::class, 'id_gudang_asal', 'id');
    }

    public function relGudangTujuan()
    {
        return $this->belongsTo(Gudang::class, 'id_gudang_tujuan', 'id');
    }
}
