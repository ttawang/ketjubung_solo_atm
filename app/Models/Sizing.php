<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Sizing extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_sizing';
    protected $guarded = [];
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }
    public function relSizingDetail()
    {
        return $this->hasMany(SizingDetail::class, 'id_sizing', 'id');
    }
    public function relSupplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id');
    }
}
