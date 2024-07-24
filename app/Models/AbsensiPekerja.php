<?php

namespace App\Models;

use App\Helpers\Date;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Znck\Eloquent\Traits\BelongsToThrough;

class AbsensiPekerja extends Model
{
    use SoftDeletes, BelongsToThrough;
    protected $table = 'tbl_absensi_pekerja';
    protected $guarded = ['tanggal_custom'];

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

    public function relPekerja()
    {
        return $this->belongsTo(Pekerja::class, 'id_pekerja', 'id');
    }

    public function relGroup()
    {
        return $this->belongsTo(Group::class, 'id_group', 'id');
    }

    public function relMesin()
    {
        return $this->belongsTo(Mesin::class, 'id_mesin', 'id');
    }

    public function relAbsensiMesin()
    {
        return DB::table('tbl_mesin')->whereIn('id', explode(',', $this->arr_mesin))->pluck('name', 'id')->toArray();
    }
}
