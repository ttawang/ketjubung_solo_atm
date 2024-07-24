<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class P2 extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_p2';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }
    public function relP2Detail()
    {
        return $this->hasMany(P2Detail::class, 'id_p2', 'id');
    }
    public function relSupplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id');
    }
}
