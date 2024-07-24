<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class MappingKualitas extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_mapping_kualitas';
    protected $guarded = [];
    protected $appends = ['grade', 'nama_kualitas'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function getGradeAttribute()
    {
        return $this->relKualitas()->value('grade');
    }

    public function getNamaKualitasAttribute()
    {
        return '[' . $this->kode . '] ' . $this->name;
    }

    public function relKualitas()
    {
        return $this->belongsTo(Kualitas::class, 'id_kualitas', 'id');
    }
}
