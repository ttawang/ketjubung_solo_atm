<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class DistribusiPakan extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_distribusi_pakan';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function relDistribusiPakanDetail()
    {
        return $this->hasMany(DistribusiPakanDetail::class, 'id_distribusi_pakan', 'id');
    }
}
