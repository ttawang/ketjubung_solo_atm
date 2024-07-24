<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Dudulan extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_dudulan';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }
    public function relDudulanDetail()
    {
        return $this->hasMany(DudulanDetail::class, 'id_dudulan', 'id');
    }
    public function relSupplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id');
    }
}
