<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Mesin extends Model
{
    protected $table = 'tbl_mesin';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function relPekerjaMesin()
    {
        return $this->hasMany(MappingPekerjaMesin::class, 'id_mesin', 'id');
    }
}
