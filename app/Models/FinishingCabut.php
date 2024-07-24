<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class FinishingCabut extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_finishing_cabut';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }
    public function relFinishingCabutDetail()
    {
        return $this->hasMany(FinishingCabutDetail::class, 'id_finishing_cabut', 'id');
    }
    public function relSupplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id');
    }
}
