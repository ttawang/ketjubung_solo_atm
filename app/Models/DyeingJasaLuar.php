<?php

namespace App\Models;

use App\Helpers\Date;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class DyeingJasaLuar extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_dyeing_jasa_luar';
    protected $guarded = [];
    protected $appends = ['tanggal_custom'];

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

    public function relDyeingJasaLuarDetail()
    {
        return $this->hasMany(DyeingJasaLuarDetail::class, 'id_dyeing_jasa_luar', 'id');
    }

    public function relSupplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id');
    }
}
