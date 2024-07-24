<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Leno extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_leno';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }
    public function relLenoDetail()
    {
        return $this->hasMany(LenoDetail::class, 'id_leno', 'id');
    }
}
