<?php

namespace App\Models;

use App\Helpers\Date;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class PenerimaanSarung extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_penerimaan_sarung';
    protected $guarded = [];
    protected $appends = ['tanggal_custom'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function getCountDetailAttribute()
    {
        return $this->relPenerimaanSarungDetail->setAppends([])->count();
    }

    public function getTanggalCustomAttribute()
    {
        return Date::format($this->tanggal, 98);
    }

    public function relPenerimaanSarungDetail()
    {
        return $this->hasMany(PenerimaanSarungDetail::class, 'id_penerimaan_sarung', 'id');
    }
}
