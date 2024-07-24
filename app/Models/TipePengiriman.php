<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class TipePengiriman extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_tipe_pengiriman';
    protected $guarded = [];
    protected $appends = ['gudang_asal', 'gudang_tujuan'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function getGudangAsalAttribute()
    {
        return $this->relGudangAsal()->value('name');
    }

    public function getGudangTujuanAttribute()
    {
        return $this->relGudangTujuan()->value('name');
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
