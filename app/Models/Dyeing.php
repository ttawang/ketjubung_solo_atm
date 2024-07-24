<?php

namespace App\Models;

use App\Helpers\Date;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Dyeing extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_dyeing';
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

    public function relDyeingDetail()
    {
        return $this->hasMany(DyeingDetail::class, 'id_dyeing', 'id');
    }
}
