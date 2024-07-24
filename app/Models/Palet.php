<?php

namespace App\Models;

use App\Helpers\Date;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Palet extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_palet';
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
        return $this->relPaletDetail->setAppends([])->count();
    }

    public function getTanggalCustomAttribute()
    {
        return Date::format($this->tanggal, 98);
    }

    public function relPaletDetail()
    {
        return $this->hasMany(PaletDetail::class, 'id_palet', 'id');
    }
}
