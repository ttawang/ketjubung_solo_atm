<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class P1 extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_p1';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }
    public function relP1Detail()
    {
        return $this->hasMany(P1Detail::class, 'id_p1', 'id');
    }
    public function relSupplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id');
    }
}
