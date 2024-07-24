<?php

namespace App\Models;

use App\Helpers\Date;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Warping extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_warping';
    protected $guarded = [];
    protected $appends = ['tanggal_custom', 'count_detail'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function getCountDetailAttribute()
    {
        return $this->relWarpingDetail->setAppends([])->count();
    }

    public function getTanggalCustomAttribute()
    {
        return Date::format($this->tanggal, 98);
    }

    public function relWarpingDetail()
    {
        return $this->hasMany(WarpingDetail::class, 'id_warping', 'id');
    }
    public function relMesin()
    {
        return $this->belongsTo(Mesin::class, 'id_mesin', 'id');
    }
}
