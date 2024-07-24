<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Resep extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_resep';
    protected $guarded = [];
    protected $appends = ['jenis_benang'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function getJenisBenangAttribute()
    {
        return $this->relBarang()->value('name') . ' | ' . $this->relWarna()->value('name');
    }

    public function getResepDetailAttribute()
    {
        return $this->relResepDetail->load('relBarang:id,name');
    }

    public function relResepDetail()
    {
        return $this->hasMany(ResepDetail::class, 'id_resep', 'id');
    }

    public function relBarang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id');
    }

    public function relWarna()
    {
        return $this->belongsTo(Warna::class, 'id_warna', 'id');
    }
}
