<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class NomorKikw extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_nomor_kikw';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function relBeam()
    {
        return $this->hasMany(Beam::class, 'id_nomor_kikw', 'id');
    }
}
