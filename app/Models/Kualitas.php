<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Kualitas extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_kualitas';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function relMappingKualitas()
    {
        return $this->hasMany(MappingKualitas::class, 'id_kualitas', 'id');
    }
}
