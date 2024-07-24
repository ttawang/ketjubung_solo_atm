<?php

namespace App\Models;

use App\Helpers\Date;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class PengirimanDyeingGresik extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_pengiriman_dyeing_gresik';
    protected $guarded = [];
    protected $appends = ['tanggal_custom', 'tipe_custom'];

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

    public function getTipeCustomAttribute()
    {
        return $this->tipe == 'BDG' ? '<span class="badge badge-outline badge-primary">Benang Warna</span>' : '<span class="badge badge-outline badge-default">Benang Grey</span>';
    }

    public function relPengirimanDyeingGresikDetail()
    {
        return $this->hasMany(PengirimanDyeingGresikDetail::class, 'id_pengiriman_dyeing_gresik', 'id');
    }

    public function relSupplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id');
    }
}
