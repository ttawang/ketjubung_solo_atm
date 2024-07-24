<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class GroupDetail extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_group_detail';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function relPekerja()
    {
        return $this->belongsTo(Pekerja::class, 'id_pekerja', 'id');
    }
}
