<?php

namespace App\Models;

use App\Helpers\Date;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class PenerimaanBarang extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_penerimaan_barang';
    protected $guarded = [];
    protected $appends = ['tanggal_terima_custom', 'tanggal_po_custom'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function getTanggalTerimaCustomAttribute()
    {
        return Date::format($this->tanggal_terima, 98);
    }

    public function getTanggalPoCustomAttribute()
    {
        return Date::format($this->tanggal_po, 98);
    }

    public function relSupplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id');
    }

    public function relPenerimaanBarangDetail()
    {
        return $this->hasMany(PenerimaanBarangDetail::class, 'id_penerimaan_barang', 'id');
    }
}
