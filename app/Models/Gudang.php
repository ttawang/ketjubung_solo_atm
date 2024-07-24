<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Gudang extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_gudang';
    protected $guarded = [];
    protected $appends = ['field'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function getFieldAttribute()
    {
        return getFIeldGudang($this->id);
    }

    public function relLogStokPenerimaan()
    {
        return $this->hasMany(LogStokPenerimaan::class, 'id_gudang', 'id');
    }
}
