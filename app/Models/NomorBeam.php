<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class NomorBeam extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_nomor_beam';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    function relBeam()
    {
        return $this->hasOne(Beam::class, 'id_nomor_beam', 'id')->latestOfMany();
    }
}
