<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class CucukDetail extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_cucuk_detail';
    protected $guarded = [];
    protected $appends = ['validated_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function getValidatedAtAttribute()
    {
        return $this->relCucuk()->value(validated_at);
    }

    public function relCucuk()
    {
        return $this->belongsTo(Cucuk::class, 'id_cucuk', 'id');
    }

    public function relPekerja()
    {
        return $this->belongsTo(Pekerja::class, 'id_pekerja', 'id');
    }
}
